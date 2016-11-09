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
    protected $action_auth = array('loginStatus','getPushMsg','readedStatus','wechatUnion'
    ,'myCourseList','unbindWechat','courseTwoBarCodes','getBindStatus');

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
        //$user = M('SchoolAccount')->where(array('phone' => $phone, 'status' => 1))->field('id,username,phone')->find();
        //if (empty($user)) {
        //    $this->error('手机号不存在');
        //}

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

        //$ret = M('SchoolAccount')->where(array('phone' => $to, 'status' => 1))->count();
        //
        //if (!$ret) {
        //    $this->error('该手机号不存在');
        //}

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
     * 学院用户id和微信绑定
    */
    public function wechatUnion(){
        $union_id = I('union_id');
        empty($union_id) && $this->error('参数错误！');
        $uid = $this->user['uid'];
        $wechat_id = M('WechatAuth')->where(array('unionid'=>$union_id))->getField('unionid,id');
        //empty($wechat_id) && $this->error('参数错误！');
        $data['college_uid'] = $uid;
        $data['unionid'] = $union_id;
        $data['wechat_id'] = !empty($wechat_id[$union_id]) ? $wechat_id[$union_id] : 0;
        $union = M('CollegeWechatUnion')->where(array('college_uid'=>$data['college_uid']))->count();
        !empty($union) && $this->success('您已经微信绑定过了！');
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
        $uid = $this->user['uid'];
        $bind = M('CollegeWechatUnion')->where(array('college_uid'=>$uid))->count();
        if ($bind){
            $result = M('CollegeWechatUnion')->where(array('college_uid'=>$uid))->delete();
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
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $uid = $this->user['uid'];
        $bind_info = M('CollegeWechatUnion')->where(array('college_uid'=>$uid))->find();
        if (!empty($bind_info)){
            if ($bind_info['wechat_id']==0){
                $this->check_wechat_id($bind_info);
            }
            $bind_info_new = M('CollegeWechatUnion')->where(array('college_uid'=>$uid))->field('college_uid,unionid,wechat_id')->find();
            $reserve = M('CourseReserve')->where(array('wechat_id'=>$bind_info_new['wechat_id'],'status'=>1))->select();
        }else{
            $reserve = array();
        }
        $courses = $this->get_course_list($reserve,$page,$per_page);
        $data['courses'] = $courses;
        $data['url'] = 'http://ke.halobear.com/course/index';
        $this->success('success',$data);
    }

    /**
     * 返回检查用户是否写入有效wechat_id
    */
    public function check_wechat_id($bind_info){
        $wechat_id = M('WechatAuth')->where(array('unionid'=>$bind_info['unionid']))->getField('unionid,id');
        $bind_info['wechat_id'] = !empty($wechat_id[$bind_info['unionid']]) ? $wechat_id[$bind_info['unionid']] : 0;
        $result = M('CollegeWechatUnion')->save($bind_info);
    }

    /**
     * 获取课程列表
    */
    public function get_course_list($reserve,$page,$per_page){
        foreach ($reserve as $key=>$value){
            $course_type [$value['course_id']] =$value['type'];
            $course_id [] =$value['course_id'];
        }
        if (!empty($course_id)){
            $courses = M('Course')->where(array('id'=>array('in',$course_id),'status'=>1))->page($page,$per_page)->field('id,title,guest_id,city,start_date,cover_url,end_date')->select();
        }else{
            $courses =array();
        }
        if (!empty($courses)){
            foreach ($courses as $key=>$value){
                 $guests_id[] =$value['guest_id'];
                $courses[$key]['type'] = $course_type[$value['id']];
            }
            $guests_id = array_unique($guests_id);
            $guests = M('SchoolGuests')->where(array('id'=>array('in', $guests_id)))->getField('id, title, position');
            foreach ($courses as $key => $value) {
                $courses[$key]['guests'] = $guests[$value['guest_id']];
                $courses[$key]['cover_url'] = C('IMG_URL') . $value['cover_url'];
            }
        }else{
            $courses = array();
        }

        return $courses;

    }

    /**
     * 获取课程的二维码unionid和用户信息
    */
    public function courseTwoBarCodes(){
        $url = 'http://7xopel.com2.z0.glb.qiniucdn.com/';
        $uid = $this->user['uid'];
        $union = M('CollegeWechatUnion')->where(array('college_uid'=>$uid))->field('unionid,wechat_id')->find();
        empty($union) && $this->error('该账号还未绑定微信,请先授权微信绑定！',$data ='');
        //获取用户信息
        $course_reserve = M('CourseReserve')->where(array('wechat_id'=>$union['wechat_id'],'status'=>1))->field('name,company,avatar_url')->find();
        $course_reserve['name'] = $course_reserve['name'] ? $course_reserve['name'] : '';
        $course_reserve['company'] = $course_reserve['company'] ? $course_reserve['company'] : '';
        $course_reserve['avatar_url'] = $course_reserve['avatar_url'] ? $url.$course_reserve['avatar_url'] : '';
        $course_reserve['unionid_encode'] = $union['unionid'];
        $key = 'halobearcollege';
        //过期时间十分钟
        $expire = 600;
        $course_reserve['unionid_encode'] = think_encrypt($course_reserve['unionid_encode'],$key,$expire);

        $this->success('success',$course_reserve);

    }

    /**
     * 二维码解密
     */
    public function unionid_decode($data){
        $key = 'halobearcollege';
        $data_json = think_decrypt($data,$key);
        $data_arr= json_decode($data_json);

        return $data_arr;
    }

    /**
     * 研习社分享页面
    */
    public function sharePage(){
        $model = M('Banner');
        $data = $model->where(array('id'=>18))->field('title,desc,banner_url as img,redirect_url_id as url,type,subimg_url as subimg')->find();
        if (!empty($data)){
            $data['img'] = 'http://7xopel.com2.z0.glb.clouddn.com/'.$data['img'];
            $data['subimg'] = empty($value['subimg']) ? '' : 'http://7xopel.com2.z0.glb.clouddn.com/'.$data['subimg'];
        }


        $this->success('success', $data);
    }

    /**
     * 获取用户微信授权绑定的状态
    */
    public function getBindStatus(){
        $uid = $this->user['uid'];
        $unionid = M('CollegeWechatUnion')->where(array('college_uid'=>$uid))->getField('unionid');
        $data['unionid']= empty($unionid) ? '' : $unionid;

        $this->success('success',$data);
    }

    /**
     * 获取版本审核状态
    */
    public function versionStatus(){
        $version = I('version');
        empty($version) && $this->error('参数错误！');
        $status = M('IosVersion')->where(array('version'=>$version))->getField('version,status');
        if (!empty($status)){
            $data['status'] = $status[$version];
        }else{
            $data['status'] = -1;
        }

        $this->success('success',$data);

    }

    /**
     * 嘉宾个人主页
    */
    public function personalHomePage(){
        $guest_id = I('guest_id');
        empty($guest_id) && $this->error('参数错误！');
        $uid = $this->user['uid'];
        $page = 1;
        $per_page = 3;
        $guest = M('SchoolGuests')->where(array('id'=>$guest_id,'status'=>1))->find();
        empty($guest) && $this->error('嘉宾不存在！');
        $guest['avatar_url'] = $guest['avatar_url'] ? C('IMG_URL').$guest['avatar_url'] : '';
        //公司信息
        if (!empty($guest['company_id'])){
            $companys = company_id($guest['company_id']);
            $company['id'] = $companys['data']['id'];
            $company['name'] = $companys['data']['name'];
        }else{
            $company = null;
        }

        //热文列表
        $article_where = array('wtw_school_wedding.auther_type'=>array('in',array(1,3)),'wtw_school_wedding.auther_id'=>$guest_id);
        $articles = A('Wedding')->wedding_list($article_where,$uid,$page,$per_page);
        //视频列表
        $video_where = array('guests_id'=>$guest_id);
        $videos = D('SchoolVideo')->getListByCate($video_where,$page,$per_page);
        $data['guest'] = !empty($guest) ? $guest : null;
        $data['company'] = !empty($company) ? $company : null;
        $data['articles'] = !empty($articles['list']) ? $articles : null;
        $data['videos'] = !empty($videos['list']) ? $videos : null;
        //分享页地址
        $data['share_url'] = 'http://college-share.halobear.com/share/guests/guest/'.$guest_id;

        $this->success('success',$data);

    }

    /**
     * 公司主页
    */
    public function companyHomePage(){
        $url = 'http://7ktsyl.com2.z0.glb.qiniucdn.com/';
        $company_id = I('company_id');
        empty($company_id) && $this->error('参数错误！');
        $uid = $this->user['uid'];
        $page = 1;
        $per_page = 3;
        //公司信息
        $data_company = company_id($company_id);
        empty($data_company) && $this->error('公司不存在！');
        $company['id'] = $data_company['data']['id'];
        $company['name'] = $data_company['data']['name'];
        $company['description'] = $data_company['data']['description'];
        $company['logo'] = $data_company['data']['logo'][0]['file_path'] ? $url.$data_company['data']['logo'][0]['file_path'] : '';
        //公司成员
        $member_where = array('company_id'=>$company_id);
        $members = $this->get_guests($member_where);
        //热文列表
        $article_where = array('wtw_school_wedding.auther_type'=>2,'wtw_school_wedding.auther_id'=>$company_id);
        $articles = A("Wedding")->wedding_list($article_where,$uid,$page,$per_page);
        //视频列表
        $video_where = array('company_id'=>$company_id);
        $videos = D('SchoolVideo')->getListByCate($video_where,$page,$per_page);

        $data['company'] = $company;
        $data['members'] = $members;
        $data['articles'] = !empty($articles['list']) ? $articles : null;
        $data['videos'] = !empty($videos['list']) ? $videos : null;
        //分享页地址
        $data['share_url'] = 'http://college-share.halobear.com/share/companies/company/'.$company_id;

        $this->success('success',$data);
    }

    

    /**
     * 获取嘉宾
    */
    public function get_guests($map){
        $map['status'] = 1;
        $guests = M('SchoolGuests')->where($map)->select();
        if (!empty($guests)){
            foreach ($guests as $key=>$value){
                $guests[$key]['avatar_url'] = $guests[$key]['avatar_url'] ? C('IMG_URL').$guests[$key]['avatar_url'] : '';
            }
        }else{
            $guests = array();
        }

        return $guests;
    }

    /**
     * 公司主页、个人主页所有文章列表
    */
    public function homeArticlesList(){
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $uid = $this->user['uid'];
        $home_id = I('home_id');
        $type = I('type');
        empty($home_id || $type) && $this->error('参数错误！');
        if ($type==1){
            //嘉宾主页热文
            $article_where = array('wtw_school_wedding.auther_type'=>array('in',array(1,3)),'wtw_school_wedding.auther_id'=>$home_id);
            $articles = A('Wedding')->wedding_list($article_where,$uid,$page,$per_page);
        }elseif ($type==2){
            //公司主页热文
            $article_where = array('wtw_school_wedding.auther_type'=>2,'wtw_school_wedding.auther_id'=>$home_id);
            $articles = A('Wedding')->wedding_list($article_where,$uid,$page,$per_page);
        }else{
            $this->error('参数错误！');
        }
        $data = empty($articles) ? array() : $articles;
        $this->success('success',$data);

    }

    /**
     * 公司主页、个人主页、金熊奖主页所有视频列表
     */
    public function homeVideosList(){
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $home_id = I('home_id');
        $type = I('type');
        $match_level = I('match_level');
        empty($home_id || $type) && $this->error('参数错误！');
        if ($type==1){
            //嘉宾视频列表
            $video_where = array('guests_id'=>$home_id);
            $videos = D('SchoolVideo')->getListByCate($video_where,$page,$per_page);
        }elseif ($type==2){
            //公司视频列表
            $video_where = array('company_id'=>$home_id);
            $videos = D('SchoolVideo')->getListByCate($video_where,$page,$per_page);
        }elseif ($type==3){
            //金熊奖视频列表
            if ($match_level==1){
                $match_first_where = array('match_type'=>2,'match_parent_id'=>$home_id,'match_level'=>1);
                $videos = D('SchoolVideo')->getListByCate($match_first_where,1,3);
            }elseif ($match_level==2){
                $match_final_where = array('match_type'=>2,'match_parent_id'=>$home_id,'match_level'=>2);
                $videos = D('SchoolVideo')->getListByCate($match_final_where,1,3);
            }else{
                $this->error('参数错误！');
            }
        }
        $data = empty($videos) ? array() : $videos;
        $this->success('success',$data);

    }

    /**
     * 金熊奖主页
    */
    public function awardsHomePage(){
        $vid = I('vid');
        empty($vid) && $this->error('参数错误！');
        $video = D('SchoolVideo')->getDetail($vid);
        $video_feature = $video['video'];
        $award_base_info = M('GoldAwards')->where(array('id'=>$video_feature['gold_award_id'],'status'=>1))->find();
        $award_base_info['cover_url'] = $award_base_info['cover_url'] ? C('IMG_URL').$award_base_info['cover_url'] : '';
        $match_first_where = array('match_type'=>2,'match_parent_id'=>$vid,'match_level'=>1);
        $match_first = D('SchoolVideo')->getListByCate($match_first_where,1,3);
        $match_final_where = array('match_type'=>2,'match_parent_id'=>$vid,'match_level'=>2);
        $match_final = D('SchoolVideo')->getListByCate($match_final_where,1,3);

        $data['gold_award'] = $award_base_info;
        $data['video_feature']['id'] = $video_feature['id'];
        $data['video_feature']['title'] = $video_feature['title'];
        $data['video_feature']['url'] = $video_feature['url'];
        $data['video_feature']['cover_url'] = $video_feature['cover_url'];
        $data['match_first'] = !empty($match_first['list']) ? $match_first : null;
        $data['match_final'] = !empty($match_final['list']) ? $match_final : null;

        $this->success('success',$data);
    }












}