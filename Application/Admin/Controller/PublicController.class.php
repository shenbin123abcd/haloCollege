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

    public function qiniu(){
        $this->ajaxReturn(array('iRet'=>1));
    }
}