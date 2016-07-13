<?php
/**
 * 模型管理
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Model;
class MemberModel extends CommonModel{
	/**
	 * 自动验证
	 * @var $_validate
	 */
	protected $_validate = array(
			//array('username', '/^\w+$/', '用户名格式错误！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
			array('username', '', '该用户名已存在！', self::MUST_VALIDATE, 'unique', self:: MODEL_BOTH),
			array('password', 'require', '密码不允许为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_INSERT),
			array('password', '5,32', '密码长度不能小于5位！', self::VALUE_VALIDATE, 'length', self:: MODEL_BOTH),
			array('password', 'repassword', '密码2次输入不一致！', self::VALUE_VALIDATE, 'confirm', self:: MODEL_UPDATE),
			array('email', 'email', '邮箱格式错误！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
			array('email', '', '该邮箱已存在！', self::MUST_VALIDATE, 'unique', self:: MODEL_BOTH),
	);
	
	/**
	 * 自动完成
	 * @var $_auto
	 */
	protected $_auto = array(
			array('create_time', 'time', self:: MODEL_INSERT, 'function'),
			array('last_time', 'time', self:: MODEL_INSERT, 'function'),
			array('status', '1', self::MODEL_INSERT, 'string'),
			array('password', 'md5', self::MODEL_INSERT, 'function'),
			array('password', 'updatePwd', self::MODEL_UPDATE, 'callback'),
	);
	
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
	 * 增加创始人
	 * @see Model::_before_insert()
	 */
	protected function _before_insert($data){
		if($_POST['act'] == 'founder'){
			$data['uid'] = $data['id'];
			M('Founder')->add($data);
		}
	}
}

?>