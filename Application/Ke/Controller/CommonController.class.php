<?php
namespace Ke\Controller;

use Think\Controller;

class CommonController extends Controller {
    public $user = array();

    /**
     * 初始化操作
     */
    public function _initialize() {
        cookie('halobear','MDk0YnhkNFJaWlA5cmVubkg3bmZBb2VoWVRFRjd6WkM5T05YZjdxUm91ZFJYTUdrSzlySURoOFNOWEY3M3FkMW9jNEo0a0FEZUVLR285UXU0bTQ2VVdhaDQ3emhKQkFtaHc=', 864000);
        $this->_getWechatUser();
    }

    /**
     * 获取微信用户信息
     */
    protected function _getWechatUser(){

        $halobear = cookie('halobear');
        $this->user = $this->wcache($halobear);

        if (empty($this->user) && !in_array(ACTION_NAME, array('wechat', 'notifyh'))) {
            cookie('halobear', null, -86400);
            if (IS_AJAX) {
                $this->ajaxReturn(array('status'=>-1,'info'=>'No authorization token was found'));
            }else{
                redirect('http://ke.halobear.com/course/wechat?url=' . urlencode('http://ke.halobear.com' . $_SERVER['REQUEST_URI']));
            }
        }
    }

    /**
     * 设置或获取微信登录用户信息
     * @param $ck
     * @param $cv
     * @return array|bool|mixed
     */
    protected function wcache($ck,$cv = ''){
        if (empty($ck)) {
            return false;
        }

        $model = M('WechatAuth');
        $ck = md5($ck);
        $user = array();
        if (isset($cv) && !empty($cv)) {
            // 存储用户信息
            $openid = $cv['access']['openid'];
            $auth = $model->where(array('openid'=>$openid))->find();
            $user = array(
                'ck'=> $ck,
                'cv'=> serialize($cv),
                'openid'=> $cv['user']['openid'],
                'create_time'=> time(),
                'nickname'=> $cv['user']['nickname'],
                'language'=> $cv['user']['language'],
                'city'=> $cv['user']['city'],
                'province'=> $cv['user']['province'],
                'country'=> $cv['user']['country'],
                'headimgurl'=> $cv['user']['headimgurl'],
            );
            if ($auth) {
                $model->where(array('id'=>$auth['id']))->save($user);
                $user['id'] = $auth['id'];
            }else{
                $user['id'] = $model->add($user);
            }
        } else {
            // 获取用户信息
            $user = session('wechat_user');
            if (empty($user)) {
                $user = $model->where(array('ck'=>$ck))->find();
            }
        }
        session('wechat_user', $user);
        return $user;
    }

    // 用户标识
    protected function _make_user_mark($openid){
        $halobear = cookie('halobear');
        // $v = md5($user_agent . uniqid());
        $v = base64_encode(authcode( "w\t".uniqid() . "\t" . $openid, 'ENCODE', 'hKea!10b@ea#$fc%2ol6' ));
        if (empty($halobear) || !$this->checkMark($halobear)) {
            cookie('halobear', $v, 7776000);
        }

        return $v;
    }

    // 检查合法性
    protected function checkMark($halobear){
        $ret = false;
        if (!empty($halobear)) {
            $v = authcode( base64_decode($halobear), 'DECODE', 'hKea!10b@ea#$fc%2ol6' );
            $v = explode("\t", $v);
            if (count($v) == 3 && $v[0] == 'w') {
                $ret = true;
            }
        }
        return $ret;
    }

    public function wechat(){
        $temp = explode('/', $_SERVER['REQUEST_URI']);
        $cur_url = 'http://ke.halobear.com/' . $temp['1'];
        $url = empty($_GET['url']) ? urlencode($cur_url) : $_GET['url'];

        $this->getWechatInfo($url, $cur_url);
    }

    protected function getWechatInfo($url, $base_url){
        $appid = 'wxb43a4c82b5203c21';
        $appsecret = '70f8e2b10b41fba0176013f4526edf7b';

        $redirect_uri = $base_url . '/wechat?url=' . urlencode($url);
        $auth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='. $appid .'&redirect_uri='. urlencode( $redirect_uri ) .'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';

        $url = urldecode($url);
        if ( !isset( $_GET['code'] ) ) {
            redirect( $auth_url );
        }elseif ( $_GET['state'] ) {

            // 通过code换取网页授权access_token
            $open_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='. $appid .'&secret='. $appsecret .'&code='. $_GET['code'] .'&grant_type=authorization_code';
            $access = curl_get( $open_url );

            // token缓存
            $halobear = cookie('halobear');
            $data = array();
            if (!empty($halobear)) {
                $data = $this->wcache($halobear);
            }

            if (empty($access['errcode'])) {
                $data['access'] = $access;
            }else{
                redirect($url);
            }

            $openid = $access['openid'];
            $access_token = $access['access_token'];

            // 获取用户信息
            $getuser_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='. $access_token .'&openid='. $openid .'&lang=zh_CN';
            $user = curl_get( $getuser_url );

            if (empty($user['errcode'])) {
                $data['user'] = $user;
                $halobear = $this->_make_user_mark($openid);
                $this->wcache($halobear,$data);
            }
        }

        redirect($url);
    }

    /**
     * 空操作
     */
    public function _empty() {
        // send_http_status(404);
        if(IS_AJAX){
            $this->ajaxReturn(array('info' => ACTION_NAME . ' Not Found', 'error' => 404, 'iRet' => 0));
        }else{
            $this->display('Index:index');
        }
    }

    /**
     * 用户信息
     */
    protected function _auth() {
        $this->user = get_user();
        if (empty($this->user) && ($this->module_auth || in_array(ACTION_NAME, $this->action_auth))) {
            $type = !empty($_GET['callback']) ? 'jsonp' : 'json';
            $this->ajaxReturn(array('iRet' => -1, 'info' => 'Access denied'));
        }
    }

    /**
     * 错误返回
     * @param string $info
     * @param string $error
     */
    protected function error($info = '网络繁忙请稍候再试', $error = '') {
        $type = !empty($_GET['callback']) ? 'jsonp' : 'json';
        $this->ajaxReturn(array('iRet' => 0, 'info' => $info, 'error' => $error), $type);
    }

    /**
     * 成功返回
     * @param string $info
     * @param array  $data
     */
    protected function success($data = array(), $info = '成功') {
        $type = !empty($_GET['callback']) ? 'jsonp' : 'json';
        $this->ajaxReturn(array('iRet' => 1, 'info' => $info, 'data' => $data), $type);
    }
}