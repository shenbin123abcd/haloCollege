<?php
/**
 * 接口
 * @author wtwei
 * @version $Id$
 */
namespace Api\Controller;

use Think\Controller;

class ApiController extends ApiBaseController {

    /**
     * 根据类型获取列表
     * @param  string  $type 类型
     * @param  integer $per_page 每页显示数量
     * @return [type]            [description]
     */
    public function getListByType($type, $per_page = 6) {
        if (!in_array($type, array('index_new', 'new', 'hot', 'score'))) {
            $this->error('参数错误');
        }
        $per_page = min(15, $per_page);
        $data = D('SchoolVideo')->getList(array(), $type, $per_page);
        $this->success('success', $data);
    }

    /**
     * 根据类型获取列表
     * @param  integer $cate 分类id
     * @param  integer $per_page 每页显示数量
     * @return [type]            [description]
     */
    public function getListByCate($cate, $per_page = 12) {
        $cate = I('cate');
        /*if ($cate <= 0 || $cate != 'all') {
            $this->error('参数错误');
        }*/
        // $per_page = min(24, $per_page);
        $map = $cate == 'all' ? array() : array('cate1' => $cate);
        $data = D('SchoolVideo')->getList($map, '', $per_page);
        $this->success('success', $data);
    }

    /**
     * 根据类型3获取列表
     * @param  integer $cate 分类id
     * @param  integer $per_page 每页显示数量
     * @return [type]            [description]
     */
    public function getListByCate2($cate, $per_page = 12) {
        $cate = intval(I('cate'));
        if (empty($cate)) {
            $this->error('error');
        }
        $map = array('_string' => 'FIND_IN_SET(' . $cate . ', cate3)');
        $data = D('SchoolVideo')->getList($map, '', $per_page);
        $this->success('success', $data);
    }

    /**
     * 根据类型获取列表
     * @param  integer $cate 分类id
     * @param  integer $per_page 每页显示数量
     * @return [type]            [description]
     */
    public function getListByTopic($cate, $per_page = 12) {
        // $per_page = min(24, $per_page);
        switch ($cate) {
            case 'all':
                $map = array();
                break;
            case 'wfc2015':
                $map = array('cate2' => array('in', array(5, 6)));
                break;
            case 'bear2015':
                $map = array('cate2' => array('in', array(7, 8)));
                break;
            case 'wfc2014':
                $map = array('cate2' => array('in', array(9, 10)));
                break;
            case 'wfc2013':
                $map = array('cate2' => array('in', array(11, 12)));
                break;
            case 'wfc2012':
                $map = array('cate2' => array('in', array(13, 14)));
                break;

            default:
                $map = array('cate2' => intval($cate));
                break;
        }

        $data = D('SchoolVideo')->getList($map, '', $per_page);
        $this->success('success', $data);
    }

    /**
     * 搜索
     * @param  string  $keyword 关键词
     * @param  integer $per_page [description]
     * @return [type]            [description]
     */
    public function search($keyword, $per_page = 12) {
        if (empty($keyword) && $keyword != '0') {
            $this->error('参数错误');
        }
        $map = array('title' => array('like', '%' . $keyword . '%'));
        $guests = M('SchoolGuests')->where(array('title' => array('like', '%' . $keyword . '%'), 'position' => array('like', '%' . $keyword . '%'), '_logic' => 'OR'))->getField('id,title');
        $guests_id = array_keys($guests);
        if ($guests_id) {
            $map = array('_string' => "title like '%{$keyword}%' OR guests_id IN(" . implode(',', $guests_id) . ')');
        }

        $data = D('SchoolVideo')->getList($map, '', $per_page);
        // 记录搜索关键词
        if ($data) {
            $tag = M('school_video_tag');
            $tag_id = $tag->where(array('tag' => $keyword))->getField('id');
            if ($tag_id) {
                $tag->where(array('id' => $tag_id))->setInc('num');
            } else {
                $tag->add(array('tag' => $keyword, 'num' => 1));
            }
        }

        $this->success('success', $data);
    }

    public function total() {
        $week = M()->query('SELECT count(*) AS count FROM wtw_school_video WHERE YEARWEEK(FROM_UNIXTIME(create_time,\'%Y-%m-%d\')) = YEARWEEK(now())');
        $month = M()->query("SELECT count(*) AS count FROM  wtw_school_video WHERE FROM_UNIXTIME(create_time,'%Y%m') = DATE_FORMAT( CURDATE() , '%Y%m' ) ");

        $this->success('success', array('week' => intval($week[0]['count']), 'month' => intval($month[0]['count'])));
    }

    /**
     * 视频详情
     */
    public function videoDetail($id) {
        $id = intval($id);
        empty($id) && $this->error('参数错误');
        $data = D('SchoolVideo')->getDetail($id);

        empty($data) ? $this->error('视频不存在') : $this->success('success', $data);
    }

    /**
     * 视频详情无需登录
     */
    public function videoDetailV2($id) {
        $id = intval($id);
        empty($id) && $this->error('参数错误');
        $data = D('SchoolVideo')->getDetail($id, 0);

        empty($data) ? $this->error('视频不存在') : $this->success('success', $data);
    }

    /**
     * 获取视频地址
     * @param  [type] $id 视频编号
     * @return [type]     视频地址
     */
    public function getUrl($vid) {
        $this->_auth();
        $url = D('SchoolVideo')->getUrl(intval($vid));

        $url ? $this->success('success', $url) : $this->error('视频不存在', $url);
    }


    /**
     * 视频推荐
     * @param  [type] $vid 视频编号
     * @return [type]      推荐视频列表
     */
    public function videoRecommend($vid) {
        $list = D('SchoolVideo')->getRecommend($vid);

        $this->success('success', $list);
    }

    /**
     * 评论
     * @return [type] [description]
     */
    public function commontSave() {
        if (!IS_POST) {
            $this->error('Access denied');
        }
        $this->_auth();
        $model = D('SchoolComment');
        if ($model->create()) {
            $id = $model->add();
            $id ? $this->success('评论成功！') : $this->error('评论失败！');
        } else {
            $this->error($model->getError());
        }
    }

    public function commentList($vid, $per_page = 12) {
        $vid = intval($vid);

        if (empty($vid) || $vid < 0) {
            $this->error('参数错误');
        }

        $list = D('SchoolComment')->getList(array('vid' => $vid), $per_page);

        $this->success('success', $list);
    }

    // 登录
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
            $avatar_token = make_qiniu_token_headimg('haloavatar', 'avatar', 'http://college.halobear.com/api/qiniuUpload', $key);

            $model->where(array('id' => $user['id']))->save(array('last_time' => time(), 'login_ip' => get_client_ip()));

            // 微社区token
            $wsq = $model->getMicroToken($user);
            $user['wsq'] = $wsq;
            $token = jwt_encode($user);
            $this->success('登录成功', array('token' => $token, 'wsq' => $wsq, 'avatar_token' => $avatar_token, 'avatar_token_key' => 'avatar/' . $key, 'user' => $user));
        } else {
            $this->error('账号或密码错误');
        }
    }

    public function qiniuUpload() {

        $this->ajaxReturn(array('url' => C('AVATAR_URL') . $_POST['key'], 'width' => $_POST['w'], 'height' => $_POST['h']));
    }

    // 注册
    public function register() {

        // 邀请码
        $_POST['code'] = I('invite_code');
        $model = D('SchoolAccount');

        // 账号中心注册
        if ($model->create()) {
            $result = $model->register();
            // Log::write(var_export($result));
            if ($result['iRet'] == 1) {
                // 产生用户标识
                $id = $model->add();
                $user = array('id' => $result['data'], 'username' => I('username'), 'phone' => I('phone'));
                $token = jwt_encode($user);
                $key = get_avatar($user['id'], 'middle', 0);
                $avatar_token = make_qiniu_token_headimg('haloavatar', 'avatar', 'http://college.halobear.com/api/qiniuUpload', $key);

                $this->success('注册成功', array('token' => $token, 'avatar_token' => $avatar_token, 'avatar_token_key' => 'avatar/' . $key, 'user' => $user));
            } else {
                $info = $result['info'];
                if ($info == 'The phone has already been taken.') {
                    $info = '该手机已存在！';
                } else if ($info == 'The username has already been taken.') {
                    $info = '该用户名已存在！';
                }
                $this->error($result['info']);
            }
        } else {
            $this->error($model->getError());
        }
    }

    // 忘记密码
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

    // 修改密码
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
        $data = array('id' => $model->id, 'password' => $password, 'new_password' => $new_password);
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

    // 获取验证码
    public function verify() {
        // 检查邀请码
        $invite = I('invite_code');
        $to = I('phone');

        if (empty($invite) || empty($to) || strlen($invite) < 2 || strlen($to) != 11) {
            $this->error('参数错误');
        }

        if (!D('SchoolAccount')->checkPhone()) {
            $this->error('请输入正确的手机号');
        }

        $ret = M('SchoolCode')->where(array('code' => $invite, 'status' => 1))->find();

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

    // 热门标签
    public function hotTag() {
        $tag = array('信仰', '潘珍玉', '金熊奖', '蔡易瑾', '花艺设计');

        $this->success('success', $tag);
    }

    /**
     * 幻灯
     * @param  string $title 标题
     * @param  string $url 地址
     * @param  string $type text静态文本,link网页,video视频
     * @return
     */
    public function banner() {
        $data = array(array('title' => '2015中国婚礼行业高峰论坛', 'desc' => '官方回顾视频', 'img' => 'http://7xopel.com2.z0.glb.qiniucdn.com/college/banner/2015.jpg', 'url' => 'http://7s1t37.com2.z0.glb.qiniucdn.com/WFCHJ/2015WFC.mp4', 'type' => 'video'), array('title' => '2014中国婚礼行业高峰论坛', 'desc' => '官方回顾视频', 'img' => 'http://7xopel.com2.z0.glb.qiniucdn.com/college/banner/2014.jpg', 'url' => 'http://7s1t37.com2.z0.glb.qiniucdn.com/WFCHJ/2014WFC.mp4', 'type' => 'video'), array('title' => '2013中国婚礼行业高峰论坛', 'desc' => '官方回顾视频', 'img' => 'http://7xopel.com2.z0.glb.qiniucdn.com/college/banner/2013.jpg', 'url' => 'http://7s1t37.com2.z0.glb.qiniucdn.com/WFCHJ/2013WFC.mp4', 'type' => 'video'), array('title' => '2012中国婚礼行业高峰论坛', 'desc' => '官方回顾视频', 'img' => 'http://7xopel.com2.z0.glb.qiniucdn.com/college/banner/2012.jpg', 'url' => 'http://7s1t37.com2.z0.glb.qiniucdn.com/WFCHJ/2012WFC.mp4', 'type' => 'video'),);

        $this->success('success', $data);
    }

    // 收藏
    public function favoritesAct() {
        $this->_auth();

        $vid = intval(I('vid'));
        if (empty($vid)) {
            $this->error('参数错误');
        }

        $count = M('SchoolVideo')->where(array('id' => $vid, 'status' => 1))->count();
        if ($count == 0) {
            $this->error('视频不存在');
        }

        D('SchoolFavorites')->act($vid);
        $this->success('操作成功');
    }

    // 我的收藏
    public function myFavorites() {
        $this->_auth();
        $per_page = I('per_page');
        $list = D('SchoolFavorites')->getList($per_page ? $per_page : 12);
        $this->success('success', $list);
    }

    // 取消收藏
    public function delFavorites() {
        $this->_auth();

        $vids = I('vids');
        if (empty($vids)) {
            $this->error('参数错误');
        }

        $ret = D('SchoolFavorites')->where(array('uid' => D('SchoolAccount')->id, 'vid' => array('in', $vids)))->delete();

        $ret ? $this->success('操作成功') : $this->error('操作失败');
    }

    // 我的评论
    public function myComments() {
        $this->_auth();
        $per_page = I('per_page');
        $list = D('SchoolComment')->my($per_page ? $per_page : 12);
        $this->success('success', $list);
    }

    // 删除评论
    public function delComment() {
        $this->_auth();

        $ids = I('id');
        if (empty($ids)) {
            $this->error('参数错误');
        }

        $ret = D('SchoolComment')->where(array('uid' => D('SchoolAccount')->id, 'id' => array('in', $ids)))->delete();
        $ret ? $this->success('删除成功') : $this->error('删除失败');
    }

    /**
     * 获取分类接口
     */
    public function getCate() {
        $list = M('SchoolCate')->where(array('status' => 1))->select();
        $this->success('success', $list);
    }

    // 意见反馈
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
}