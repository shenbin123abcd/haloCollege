<?php
/**
 * 公开的操作
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Controller;
use Think\Controller;
class PublicController extends Controller {
    /**
     * 登录
     */
    public function login(){

    	if (IS_POST) {
    		// 支持用户名、UID、邮箱登陆
    		$username = trim($_POST['username']);
    		$password = $_POST['password'];

    		if (empty($username) || empty($password)) {
    			$this->error('账号密码不允许为空！');
    		}

    		if(is_numeric($username)){
    			$where = array('id' => $username);
    		}elseif(strpos($username,'@')){
    			$where = array('email' => $username);
    		}else{
    			$where = array('username' => $username);
    		}

    		$where['status'] = 1;
    		$data = M('Member')->where($where)->find();

    		if (!empty($data) && $data['password'] == md5($password)) {
                $admin = M('RoleUser')->where(array('user_id'=>$data['id']))->count();				
                $founder = D('Founder')->getFounder();
                !$admin && !in_array($data['id'], $founder) && $this->error('您没有权限登录后台！');
    			member_info($data);
				
    			$this->success('登录成功！',U('Index/index'));
    		} else {
    			$this->error('账号或密码错误！');
    		}
    		
    	}
    	member_info() && $this->redirect('/admin');
		$this->display();
	}

   /**
     * 退出登录
     */
	public function logout() {
		member_info(null);
		session(C('SAVE_ACCESS_NAME'), null);
		$this->redirect(C('USER_AUTH_GATEWAY'));
	}

	/**
     * 验证码
     */
	public function verify() {
		import("ORG.Util.Images");
		$length = C('VERIFY_CODE_LENGTH');
		Images::verify($value,$length?$length : 4);
		session('verify',$value);
	}

    
    /**
     * 七牛上传回调
     */
    public function qiniuUpload(){
        $data['key'] = '';
        $data['name'] = $_POST['fname'];
        $data['size'] = $_POST['fsize'];
        $data['module'] = $_POST['module'];
        $data['savename'] = $_POST['key'];
        $data['width'] = $_POST['w'];
        $data['height'] = $_POST['h'];
        $data['create_time'] = time();
        $data['type'] = $_POST['filetype'];
        $data['status'] = 1;
        $data['record_id'] = $data['user_id'] = 0;

        $id = D('Attach')->add($data);
        $this->ajaxReturn(array('id'=>$id,'error'=>0,'url'=>'http://7xopel.com2.z0.glb.clouddn.com/' . $_POST['key']));
    }
}