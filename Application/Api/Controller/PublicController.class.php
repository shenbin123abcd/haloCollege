<?php
/**
 * 公开的操作
 * @author wtwei
 * @version $Id$
 */
namespace Api\Controller;

use Think\Controller;

class PublicController extends CommonController {

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
     * 微社区注册/登录
     */
    public function MicroCommunityLogin($data){
        $ua = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($ua, 'iPhone') || strpos($ua, 'iPad')) {
            //$ak = '57624435e0f55ab83b000868';
            //$key = 'f1040c987c3ca653985b4c486e560b67';
            $ak = C('IOS_AK');
            $key = C('IOS_SECRET');
        } else {
            //$ak = '57624411e0f55ab83b000848';
            //$key = '65115406623996afcc0a14f2e4d00c7f';
            $ak = C('ANDROID_AK');
            $key = C('ANDROID_SECRET');
        }


        //封装用户信息
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