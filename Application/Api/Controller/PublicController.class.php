<?php
/**
 * 公开的操作
 * @author wtwei
 * @version $Id$
 */
namespace Api\Controller;

use Think\Controller;

class PublicController extends CommonController {

    protected $module_auth = 0;
    protected $action_auth = array('loginStatus','getPushMsg','readedStatus','wechatUnion','myCourseList','unbindWechat');

    /**
     * 七牛上传回调 --非编辑器
     */
    public function qiniuUploadCallback() {
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
        $this->ajaxReturn(array('id' => $id, 'w' => $_POST['w'], 'h' => $_POST['h'], 'key' => $_POST['key'], 'fsize' => $_POST['fsize']));
    }

    /**
     * 获取用户信息（视视评论列表用到）
    */
    public function getUserInfo() {
        $uid = $_POST['uid'];
        if (empty($uid)) {
            $this->error('参数错误！');
        }
        $uid_arr = json_decode($uid);
        $where['uid'] = array('in', $uid_arr);
        $where['status'] = 1;
        $userInfo = M('Userinfo')->where($where)->select();
        if (empty($userInfo)) {
            $this->error('用户信息不存在！');
        }
        $data['userInfo'] = $userInfo;
        $this->success('success', $data);
    }

    /**
     * 登录
    */
    public function login() {
        $phone = I('phone');
        $password = I('password');
        $model = D('SchoolAccount');

        // 本地登录
        /*$user = M('SchoolAccount')->where(array('phone'=>$phone))->field('id,username,phone, password')->find();

        if ($user['password'] !== md5($password)) {
            $this->ajaxReturn(array('iRet'=>0,'info'=>'用户名或密码错误'));
        }else{*/
        // 账号中心登录
        $result = $model->login($phone, $password);

        if ($result['iRet'] == 1) {
            // 产生用户标识
            $user = $result['data'];
            unset($user['company'], $user['store'], $user['token']);
            $user['avatar'] = get_avatar($user['id']);

            //获取用户职位信息给友盟
            $whereUser['uid'] = $user['id'];
            $userInfo = D('Userinfo')->where($whereUser)->field('company,position,truename')->find();
            $user['company'] = $userInfo['company'];
            $user['position'] = $userInfo['position'];
            $user['truename'] = $userInfo['truename'];

            $key = get_avatar($user['id'], 'middle', 0);
            $avatar_token = make_qiniu_token_headimg('haloavatar', 'avatar', 'http://college.halobear.com/public/qiniuUpload', $key);

            $model->where(array('phone' => $user['phone']))->save(array('last_time' => time(), 'login_ip' => get_client_ip(),'uid'=>$user['uid']));


            $token = jwt_encode($user);
            $add_token['uid'] = $user['id'];
            $add_token['token'] = md5($token);
            $result =  M('Session')->where(array('uid'=>$user['id']))->delete();
            if($result!==false){
                $id = M('Session')->add($add_token);
            }
            $this->success('登录成功', array('token' => $token,'avatar_token' => $avatar_token, 'avatar_token_key' => 'avatar/' . $key, 'user' => $user));
        } else {
            $this->error('账号或密码错误');
        }
    }


    /**
     * 注册
    */
    public function register() {

        // 邀请码
        //$_POST['code'] = I('invite_code');
        $model = D('SchoolAccount');

        // 账号中心注册
        if ($model->create()) {
            $result = $model->register();

            // Log::write(var_export($result));
            if ($result['iRet'] == 1) {
                $_POST['uid']=!empty($result['data']) ? $result['data'] : 0;
                // 产生用户标识
                $id = $model->add();
                $user = array('id' => $result['data'], 'username' => I('username'), 'phone' => I('phone'));
                $token = jwt_encode($user);
                $key = get_avatar($user['id'], 'middle', 0);
                $avatar_token = make_qiniu_token_headimg('haloavatar', 'avatar', 'http://college.halobear.com/public/qiniuUpload', $key);
                $this->success('注册成功', array('token' => $token, 'avatar_token' => $avatar_token, 'avatar_token_key' => 'avatar/' . $key, 'user' => $user));
            } else {
                $info = $result['info'];
                if ($info == 'The phone has already been taken.') {
                    $info = '该手机已存在！';
                } else if ($info == 'The username has already been taken.') {
                    $info = '该用户名已存在！';
                }
                //$this->error($result['info']);
                $this->error($info);
            }
        } else {
            $this->error($model->getError());
        }
    }

    /**
     * 忘记密码
    */
    public function forget() {
        $model = D('SchoolAccount');

        $verify_code = I('verify_code');
        $password = I('password');
        $rpassword = I('rpassword');
        $phone = I('phone');

        // 验证码
        if (!$model->checkVerifyCode()) {
            $this->error('验证码错误或已失效！');
        } elseif ($password != $rpassword) {
            $this->error('2次密码输入不一致！');
        }
        $user = M('SchoolAccount')->where(array('phone' => $phone, 'status' => 1))->field('id,username,phone')->find();
        if (empty($user)) {
            $this->error('手机号不存在');
        }

        // 本地修改
        // $ret = $model->where(array('phone'=>$phone))->save(array('password'=>md5($password)));

        // 账号中心修改
        $data = array('phone' => $phone, 'new_password' => $password);
        $ret = $model->editPassword($data);
        if ($ret['iRet'] == 1) {
            // 产生用户标识
            $token = jwt_encode($user);
            $this->success('修改成功', array('token' => $token, 'user' => $user));
        } elseif ($ret['iRet'] == 0) {
            if ($ret['info'] == 'Old password is wrong') {
                $this->error('原密码错误');
            } else {
                $this->error($ret['info']);
            }
        } else {
            $this->error('网络繁忙，请稍候再试！');
        }
    }

    /**
     * 修改密码
    */
    public function editPassword() {
        $this->_auth();

        $model = D('SchoolAccount');

        $password = I('password');
        $new_password = I('new_password');
        
        // 验证码
        $user = M('SchoolAccount')->where(array('phone' => $phone, 'status' => 1))->field('id,username,phone')->find();
        // if($user['password'] != md5($password)){
        // 	$this->error('原密码错误');
        // }

        // 账号中心修改
        $data = array('id' => $this->user['id'], 'password' => $password, 'new_password' => $new_password);
        $ret = $model->editPassword($data);

        if ($ret['iRet'] == 1) {
            // 产生用户标识
            $token = jwt_encode($user);
            $this->success('修改成功', array('token' => $token, 'user' => $user));
        } elseif ($ret['iRet'] == 0) {
            $this->error($ret['info']);
        } else {
            $this->error('网络繁忙，请稍候再试！');
        }
    }

    /**
     * 忘记密码验证码
     */
    public function forgetCode() {
        // 检查手机
        $to = I('phone');

        if (empty($to) || strlen($to) != 11) {
            $this->error('参数错误');
        }

        $ret = M('SchoolAccount')->where(array('phone' => $to, 'status' => 1))->count();

        if (!$ret) {
            $this->error('该手机号不存在');
        }

        $data = M('phone')->where(array('phone' => $to))->find();

        $phone_code = $data['code'];
        if (!empty($phone_code) && time() - $data['create_time'] < 60) {
            $this->error('发送过于频繁，请一分钟后再试');
        }

            $code = rand(100001, 999999);
        $ret = send_msg($to, array($code), 23351, '8a48b551488d07a80148a5a1ea330a06');
        if ($ret['iRet'] == 0) {
            M('phone')->where(array('phone' => $to))->delete();
            $ret = M('phone')->add(array('phone' => $to, 'code' => $code, 'create_time' => time()));

            $this->success('短信发送成功，请注意查收！');
        } elseif ($ret['iRet'] == 160040) {
            $this->error('该号码发送过于频繁，请明天再来！');
        } else {
            $this->error('网络繁忙，请稍候再试！');
        }
    }

    /**
     * 获取验证码
    */
    public function verify() {

        // 检查邀请码
        //$invite = I('invite_code');
        $to = I('phone');

        //if (empty($invite) || empty($to) || strlen($invite) < 2 || strlen($to) != 11) {
        //    $this->error('参数错误');
        //}
        if (empty($to) || strlen($to) != 11) {
            $this->error('参数错误');
        }

        if (!D('SchoolAccount')->checkPhone()) {
            $this->error('请输入正确的手机号');
        }

        //$ret = M('SchoolCode')->where(array('code' => $invite, 'status' => 1))->find();
        $ret = M('SchoolCode')->where(array('status' => 1))->find();

        if (!$ret || $ret['total_num'] <= $ret['use_num']) {
            $this->error('邀请码错误或已失效');
        }

        $data = M('phone')->where(array('phone' => $to))->find();

        $phone_code = $data['code'];
        if (!empty($phone_code) && time() - $data['create_time'] < 60) {
            $this->error('发送过于频繁，请一分钟后再试');
        }

        $code = rand(100001, 999999);
        $ret = send_msg($to, array($code), 23351, '8a48b551488d07a80148a5a1ea330a06');
        if ($ret['iRet'] == 0) {
            M('phone')->where(array('phone' => $to))->delete();
            $ret = M('phone')->add(array('phone' => $to, 'code' => $code, 'create_time' => time()));

            $this->success('短信发送成功，请注意查收！');
        } elseif ($ret['iRet'] == 160040) {
            $this->error('该号码发送过于频繁，请明天再来！');
        } else {
            $this->error('网络繁忙，请稍候再试！');
        }
    }

    /**
     * 意见反馈
    */
    public function feedback() {
        $this->_auth();
        $name = D('SchoolAccount')->username;
        $tel = D('SchoolAccount')->phone;
        $content = trim(I('content'));

        if (empty($content)) {
            $this->error('内容不能为空');
        }
        $this->_loadConfig();

        $email = 'ceo@halobear.com';
        $subject = '幻熊学院意见反馈 - ' . $name;
        $body = '联系人：' . $name . '<br>';
        $body .= '联系方式：' . $tel . '<br>';
        $body .= $content . '<br>';
        $body .= '系统信息：' . $_SERVER['HTTP_USER_AGENT'];
        $return = sendEmail($email, $subject, $body, $html = true);

        $return ? $this->success('非常感谢，您的留言提交成功！') : $this->error('网络繁忙，请稍候再试！');
    }

    /**
     * 加载配置
     */
    protected function _loadConfig() {
        $data = D('Config')->select();
        $result = array();
        foreach ($data as $value) {
            $result[$value['name']] = json_decode($value['value']) ? json_decode($value['value'],true) : $value['value'];
        }
        C($result);
    }

    public function qiniuUpload() {

        $this->ajaxReturn(array('url' => C('AVATAR_URL') . $_POST['key'], 'width' => $_POST['w'], 'height' => $_POST['h']));
    }

    /**
     * 前端向后端通知用户的登录状态
    */
    public function loginStatus(){
        $model = M('UserLogin');
        $exp = 2592000;
        $user = $this->user;
        $is_login = I('is_login');
        $is_login=='' && $this->error('参数错误！');
        $data['uid'] = $user['id'];
        $record =$model->where(array('uid'=>$data['uid']))->find();
        if ($is_login){
            $data['is_login'] = $is_login;
            $data['token_exp'] = time()+$exp;
            if (!empty($record)){
                $record['is_login'] = $is_login;
                $record['token_exp'] = $data['token_exp'];
                $model->save($record);
            }else{
                $model->add($data);
            }
        }else{
            $data['is_login'] = $is_login;
            $data['token_exp'] = time();
            if (!empty($record)){
                $record['is_login'] = $is_login;
                $record['token_exp'] = $data['token_exp'];
                $model->save($record);
            }else{
                $model->add($data);
            }
        }
        $this->success('success');

    }

    /**
     * 获取推送消息
    */
    public function getPushMsg(){
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $order = 'id DESC';
        $uid = $this->user['uid'];
        $data = M('PushMsg')->where(array('to_uid'=>$uid))->order($order)->page($page,$per_page)->select();
        $this->success('success',$data);
    }

    /**
     * 推送消息已读状态修改
    */
    public function readedStatus(){
        $uid = $this->user['uid'];
        $msg_no = I('msg_no');
        empty($msg_no) && $this->error('参数错误！');
        $model = M('PushMsg');
       $msg = $model->where(array('msg_no'=>$msg_no,'to_uid'=>$uid))->find();
        if (!empty($msg)){
            $msg['is_read'] =1;
            $result = $model->save($msg);
            if ($result!==false){
                $this->success('success');
            }else{
                $this->error('状态修改失败！');
            }
        }else{
            $this->error('不存在该条消息！');
        }
    }

    /**
     * 专为IOS写的判断app是否在审核中的状态返回接口
    */
    public function checkStatus(){
        $this->success('success',array('status'=>1));
    }

    /**
     * 学院用户id和微信id关联
    */
    public function wechatUnion(){
        $union_id = I('union_id');
        empty($union_id) && $this->error('参数错误！');
        $uid = $this->user['uid'];
        $wechat_id = M('WechatAuth')->where(array('unionid'=>$union_id))->getField('unionid,id');
        empty($wechat_id) && $this->error('参数错误！');
        $data['college_uid'] = $uid;
        $data['unionid'] = $union_id;
        $data['wechat_id'] = $wechat_id[$union_id];
        $union = M('CollegeWechatUnion')->where(array('college_uid'=>$data['college_uid'],'unionid'=>$data['unionid'],'wechat_id'=>$data['wechat_id']))->count();
        !empty($union) && $this->success('您已经关联过了！');
        $id = M('CollegeWechatUnion')->add($data);
        if ($id){
            $this->success('关联成功！');
        }else{
            $this->error('关联失败！');
        }
    }

    /**
     * 解除微信绑定
    */
    public function unbindWechat(){
        $union_id = I('union_id');
        empty($union_id) && $this->error('参数错误！');
        $uid = $this->user['uid'];
        $bind = M('CollegeWechatUnion')->where(array('college_uid'=>$uid,'unionid'=>$union_id))->count();
        if ($bind){
            $result = M('CollegeWechatUnion')->where(array('college_uid'=>$uid,'unionid'=>$union_id))->delete();
            if ($result!==false){
                $this->success('解除绑定成功！');
            }else{
                $this->error('解除绑定失败！');
            }
        }else{
            $this->success('该账号未曾被微信授权绑定！');
        }

    }

    /**
     * 我的报名课程列表
    */
    public function myCourseList(){
        $uid = $this->user['uid'];
        $wechat_ids = M('CollegeWechatUnion')->where(array('college_uid'=>$uid))->field('college_uid,wechat_id')->select();
        foreach ($wechat_ids as $key=>$value){
            $wechat_id_arr[] = $value['wechat_id'];
        }
        if (!empty($wechat_id_arr)){
            $courses = M('CourseReserve')->where(array('wechat_id'=>array('in',$wechat_id_arr),'status'=>1))->select();
        }else{
            $courses = array();
        }

         $data['courses'] = $courses;
        $this->success('success',$data);
    }






}