<?php
// 账号模型
namespace Api\Model;

use Think\Model;
class SchoolAccountModel extends Model {
	public $id = 0;
	public $username = '';
	public $phone = '';

	/**
	 * 自动验证
	 * @var $_validate
	 */
	protected $_validate = array(
			//array('username', '/^\w+$/', '用户名格式错误！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
			array('username', '2,20', '用户名长度不能小于2位或大于20位！', self::VALUE_VALIDATE, 'length', self:: MODEL_BOTH),
			array('username', '', '该用户名已存在！', self::MUST_VALIDATE, 'unique', self:: MODEL_BOTH),
			array('password', 'require', '密码不允许为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_INSERT),
			array('password', '5,32', '密码长度不能小于5位或大于32位！', self::VALUE_VALIDATE, 'length', self:: MODEL_BOTH),
			array('phone', 'checkPhone', '手机格式错误！', self::MUST_VALIDATE, 'callback', self:: MODEL_BOTH),
			array('phone', '', '该手机已存在！', self::MUST_VALIDATE, 'unique', self:: MODEL_BOTH),
			// array('city', 'require', '填写城市', self::MUST_VALIDATE, 'regex', self:: MODEL_INSERT),
			// array('company', 'require', '填写公司', self::MUST_VALIDATE, 'regex', self:: MODEL_INSERT),
			// array('wechat', 'require', '填写微信', self::MUST_VALIDATE, 'regex', self:: MODEL_INSERT),
			array('code', 'checkCode', '邀请码错误！', self::MUST_VALIDATE, 'callback', Model:: MODEL_INSERT),
			array('verify_code', 'checkVerifyCode', '验证码错误或已失效！', self::MUST_VALIDATE, 'callback', self:: MODEL_BOTH),
	);
	
	/**
	 * 自动完成
	 * @var $_auto
	 */
	protected $_auto = array(
			array('create_time', 'time', Model:: MODEL_INSERT, 'function'),
			array('update_time', 'time', Model:: MODEL_INSERT, 'function'),
			array('last_time', 'time', Model:: MODEL_INSERT, 'function'),
			array('status', '1', Model::MODEL_INSERT, 'string'),
			array('password', 'md5', Model::MODEL_INSERT, 'function'),
			array('password', 'updatePwd', Model::MODEL_UPDATE, 'callback'),
			array('login_ip', 'get_client_ip', Model::MODEL_INSERT, 'function'),
	);
	
	/**
	 * 检查验证码的合法性
	 * @return [type] 布尔
	 */
	public function checkVerifyCode(){
		// 验证码
		$verify =  I('verify_code');

		// 检查验证码
		$ret = M('phone')->where(array('phone'=>I('phone'), 'code'=>$verify, 'create_time'=>array('gt', time() - 7200)))->count();

		return $ret ? true : false;
	}
	
	/**
	 * 检查邀请码的合法性
	 * @return [type] 布尔
	 */
	public function checkCode(){
		// $ret = M('SchoolCode')->where(array('code'=>$_POST['code'], 'status'=>1, 'total_num'=>array('gt', 'use_num')))->count();
		$ret = M('SchoolCode')->where(array('code'=>$_POST['code'], 'status'=>1))->find();

		if (!$ret || $ret['total_num'] <= $ret['use_num']) {
			return false;
		}else{
			return true;
		}
	}
	
	public function checkPhone(){
		return preg_match("/^1[34578]\d{9}$/",I('phone'));
	}
	
	/**
	 * 修改密码
	 * @return string
	 */
	protected function updatePwd(){
		if ($_POST['password'] && ($_POST['password'] == $_POST['repassword'])) {
			return md5($_POST['password']);
		}
	}

	/**
	 * 注册成功后操作
	 * @param  [type] $data   用户信息
	 * @param  [type] $option [description]
	 * @return [type]         [description]
	 */
	protected function _after_insert($data, $option){
		M('SchoolCode')->where(array('code'=>$data['code']))->setInc('use_num');
	}

	/**
	 * 账号中心注册
	 * @return [type] [description]
	 */
	public function register(){
		$api = C('AUTH_API_URL') . 'user';
		$data = array(
			'username' => I('username'),
			'phone' => I('phone'),
			'password' => I('password'),
			'regip' => get_client_ip()
			);
		$result = curl_post($api, $data);

		return $result;
	}

	/**
	 * 账号中心登录
	 * @param  [type] $phone    手机号
	 * @param  [type] $password 密码
	 * @return [type]           登录状态
	 */
	public function login($phone, $password){
		$api = C('AUTH_API_URL') . 'user/login';

		$data = array(
			'phone' => I('phone'),
			'password' => I('password')
			);
		$result = curl_post($api, $data);

		return $result;
	}

	public function editPassword($data){
		$api = C('AUTH_API_URL') . 'user/updatePassword';
		$result = curl_request($api, $data, 'POST');

		return $result;
	}

	/**
	 * 获取微社区token
	 */
	public function getMicroToken($data){
		$ua = $_SERVER['HTTP_USER_AGENT'];
		if(strpos($ua, 'iPhone') || strpos($ua, 'iPad')){
			$ak = '57624435e0f55ab83b000868';
		    $key = 'f1040c987c3ca653985b4c486e560b67';
		}else{
			$ak = '57624411e0f55ab83b000848';
		    $key = '65115406623996afcc0a14f2e4d00c7f';
		}

		//封装用户职位信息
		//$custom['company'] =$data['company'];
		//$custom['position'] =$data['position'];
		//$custom['truename'] =$data['truename'];
		//$custom_json = json_encode($custom);


		$temp = array(
			'user_info'=>array(
				'name'=>$data['username'],
				'icon_url'=>$data['avatar'],
				),
			'source_uid'=> (string)$data['id'],
			'source'=>'self_account',
		);

		$data = json_encode($temp);

		// 加密
		$data = pack("N",strlen($data)).$data;
		$string = encrypt($data, $key);
		$encrypted_data = base64_encode($string);



		$url = 'https://rest.wsq.umeng.com/0/get_access_token?ak=' . $ak;
		$result = curl_post($url, array('encrypted_data'=>$encrypted_data));

		return isset($result['access_token']) ? array('uid'=>$result['id'], 'access_token'=>$result['access_token']) : array('uid'=>'', 'access_token'=>'');
	}
}

?>
