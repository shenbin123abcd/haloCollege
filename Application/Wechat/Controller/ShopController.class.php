<?php
namespace Wechat\Controller;

use Think\Controller;

class ShopController extends Controller
{
    public function index()
    {
        $ret_url = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : 'http://shop.halobear.cn';
        $back = 'http://wechat.halobear.com/shop/returnClient?ret_url=' . urlencode($ret_url);
        $url = 'http://wechat.halobear.com/?scope=snsapi_base&back=' . $back;
        redirect($url);
    }

    public function returnClient(){
        $ret_url = urldecode($_GET['ret_url']);
        if (isset($_GET['wechat_code']) && !empty($_GET['wechat_code'])){
            $code = decrypt(base64_decode(urldecode($_GET['wechat_code'])), C('WECHAT_AUTH_KEY'));
            $parse = parse_url($ret_url);

            if ($parse['query']){
                $ret_url .= '&';
            }else{
                $ret_url .= '?';
            }
            $ret_url .= 'openid=' . $code['openid'];
        }
        redirect($ret_url);
    }

    public function coupon()
    {
        $ret_url = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : 'http://shop.halobear.cn/h5/hongbao';
        $back = 'http://wechat.halobear.com/shop/returnClient?ret_url=' . urlencode($ret_url);
        $url = 'http://wechat.halobear.com/?scope=snsapi_userinfo&back=' . $back;
        redirect($url);
    }
}