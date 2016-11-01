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
    public function login() {

        if (IS_POST) {
            // 支持用户名、UID、邮箱登陆
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            if (empty($username) || empty($password)) {
                $this->error('账号密码不允许为空！');
            }

            if (is_numeric($username)) {
                $where = array('id' => $username);
            } elseif (strpos($username, '@')) {
                $where = array('email' => $username);
            } else {
                $where = array('username' => $username);
            }

            $where['status'] = 1;
            $data = M('Member')->where($where)->find();

            if (!empty($data) && $data['password'] == md5($password)) {
                $admin = M('RoleUser')->where(array('user_id' => $data['id']))->count();
                $founder = D('Founder')->getFounder();
                !$admin && !in_array($data['id'], $founder) && $this->error('您没有权限登录后台！');
                member_info($data);

                $this->success('登录成功！', U('Index/index'));
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
        Images::verify($value, $length ? $length : 4);
        session('verify', $value);
    }


    /**
     * 七牛上传回调 - 编辑器
     */
    public function qiniuUpload() {
        $upload_ret = base64_decode($_GET['upload_ret']);
        //返回参数解析
        parse_str($upload_ret, $upload_ret_arr);
        $upload_ret_arr['key'] = substr($upload_ret_arr['key'], 0, strlen($upload_ret_arr['key']) - 1);
        $upload_ret_arr['key'] = substr($upload_ret_arr['key'], 1);
        $upload_ret_arr['fname'] = substr($upload_ret_arr['fname'], 0, strlen($upload_ret_arr['fname']) - 1);
        $upload_ret_arr['fname'] = substr($upload_ret_arr['fname'], 1);
        $qiniu_url = 'http://7xopel.com2.z0.glb.clouddn.com/';
        if (!empty($upload_ret_arr['key'])) {
            $data['key'] = '';
            $data['name'] = $upload_ret_arr['fname'];
            $data['size'] = $upload_ret_arr['fsize'];
            $data['module'] = $upload_ret_arr['module'];
            $data['savename'] = $upload_ret_arr['key'];
            $data['width'] = $upload_ret_arr['w'];
            $data['height'] = $upload_ret_arr['h'];
            $data['create_time'] = time();
            $data['type'] = $upload_ret_arr['filetype'];
            $data['status'] = 1;
            $data['record_id'] = $data['user_id'] = 0;
            $id = D('Attach')->add($data);
            $this->ajaxReturn(array('id' => $id, 'error' => 0, 'url' => $qiniu_url . $upload_ret_arr['key']));
        } else {
            $this->ajaxReturn(array('error' => 1, 'message' => '图片上传失败！'));
        }
    }

    /**
     * 七牛上传回调 --非编辑器
     */
    public function qiniuUploadCallback(){
        $data['key'] = $_POST['filetype'];
        $data['name'] = $_POST['fname'];
        $data['size'] = $_POST['fsize'];
        $data['module'] = $_POST['module'];
        $data['savename'] = $_POST['key'];
        $data['create_time'] = time();
        $data['width'] = $_POST['w'];
        $data['height'] = $_POST['h'];
        $data['type'] = '';
        $data['status'] = 1;
        $data['record_id'] = $data['user_id'] = 0;

        $id = M('Attach')->add($data);

        $this->ajaxReturn(array('id'=>$id,'w'=>$_POST['w'],'h'=>$_POST['h'],'key'=>$_POST['key'],'fsize'=>$_POST['fsize']));
    }

    /**
     * 七牛上传回调 --banner图片上传
     */
    public function qiniuUploadBanner(){
        $data['key'] = $_POST['filetype'];
        $data['name'] = $_POST['fname'];
        $data['size'] = $_POST['fsize'];
        $data['module'] = $_POST['module'];
        $data['savename'] = $_POST['key'];
        $data['create_time'] = time();
        $data['width'] = $_POST['w'];
        $data['height'] = $_POST['h'];
        $data['type'] = '';
        $data['status'] = 1;
        $data['record_id'] = $data['user_id'] = 0;

        $id = M('Attach')->add($data);

        $this->ajaxReturn(array('id'=>$id,'w'=>$_POST['w'],'h'=>$_POST['h'],'key'=>$_POST['key'],'fsize'=>$_POST['fsize']));
    }


}