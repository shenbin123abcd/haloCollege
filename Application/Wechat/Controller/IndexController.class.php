<?php
namespace Wechat\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $back = $_GET['back'];
        $scope = $_GET['scope'];
        if (!empty($back)){
            $back = urldecode($_GET['back']);
            $url = parse_url($back);

            $_GET['back'] = urlencode($back);
        }else{
            exit('error');
        }

        $this->wechat();
    }

    public function wechat(){
        $back = $_GET['back'];
        $scope = in_array($_GET['scope'], ['snsapi_userinfo', 'snsapi_base']) ? $_GET['scope'] : 'snsapi_userinfo';

        $this->getWechatInfo($back, $scope);
    }

    private function getWechatInfo($back, $scope){
        $appid = 'wxb43a4c82b5203c21';
        $appsecret = '70f8e2b10b41fba0176013f4526edf7b';

        $redirect_uri = 'http://wechat.halobear.com/index/wechat?back=' . $back;
        $auth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='. $appid .'&redirect_uri='. urlencode( $redirect_uri ) .'&response_type=code&scope='.$scope.'&state=1#wechat_redirect';

        if ( !isset( $_GET['code'] ) ) {
            redirect( $auth_url );
        }elseif ( $_GET['state'] ) {
            // 通过code换取网页授权access_token
            $open_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='. $appid .'&secret='. $appsecret .'&code='. $_GET['code'] .'&grant_type=authorization_code';
            $access = curl_get( $open_url );

            if (!empty($access['errcode'])) {
                exit($access['errmsg']);
            }

            $openid = $access['openid'];
            $access_token = $access['access_token'];

            if ($scope == 'snsapi_base'){
                $user = ['id'=>'','openid'=>$access['openid'], 'unionid'=>''];
            }else{
                // 获取用户信息
                $getuser_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='. $access_token .'&openid='. $openid .'&lang=zh_CN';
                $user = curl_get( $getuser_url );
                if (!empty($user['errcode'])) {
                    exit('user:' . $user['errmsg']);
                }else{
                    $user = $this->_save($openid,$user);
                }
            }
        }
//        $user = ['openid'=>'oEgUsswoTQiV9DqWyYPT-EdfC39U', 'unionid'=>'ofC7IvqAxUIlpNHeeybAQsZwAads'];

        $user_info = encrypt(['openid'=>$user['openid'], 'unionid'=>$user['unionid']], C('WECHAT_AUTH_KEY'));
        $url = (urldecode($back));
        $parse = parse_url($url);

        if ($parse['query']){
            $url .= '&';
        }else{
            $url .= '?';
        }
        $url .= 'wechat_code=' . urlencode(base64_encode($user_info)); write_log('url', $url);
        redirect($url);
    }

    private function _save($ck, $cv){
        $model = M('WechatAuth');
        $ck = md5($ck);
        $user = array();
        if (isset($cv) && !empty($cv)) {
            // 存储用户信息
            $openid = $cv['openid'];
            $auth = $model->where(array('openid'=>$openid))->field('id')->find();
            $user = array(
                'ck'=> $ck,
                'cv'=> serialize($cv),
                'openid'=> $cv['openid'],
                'create_time'=> time(),
                'nickname'=> $cv['nickname'],
                'language'=> $cv['language'],
                'city'=> $cv['city'],
                'province'=> $cv['province'],
                'country'=> $cv['country'],
                'headimgurl'=> $cv['headimgurl'],
                'unionid'=> $cv['unionid'],
            );
            if ($auth) {
                $model->where(array('id'=>$auth['id']))->save($user);
                $user['id'] = $auth['id'];
            }else{
                $user['id'] = $model->add($user);
            }
        }
        return $user;
    }
}