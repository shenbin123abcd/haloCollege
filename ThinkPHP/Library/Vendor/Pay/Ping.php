<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/8/31
 * Time: 17:38
 */
namespace Org\Util;

use Pingpp\Charge;
use Pingpp\Error\Base;
use Pingpp\Pingpp;
use Pingpp\Util\Util;

class Ping {
    private $apiTestKey = 'sk_test_b98Oy1mz94iLPyH0qHXPe10S';
    private $apiLiveKey = 'sk_live_8m5yX1TerD0CaXnPGOa5yDCS';
    private $appId = 'app_4CSWP48m1eLGC080';
    private $channel = 'alipay';

    public function __construct() {
        // 设置 API Key
        Pingpp::setApiKey($this->apiTestKey);

        // 设置请求签名密钥
        Pingpp::setPrivateKeyPath(VENDOR_PATH.'Pay/ping/ping_rsa_private_key.pem');
    }

    public function pay($order, $channel = 'alipay', $type = 'member') {
        try {
            $data = array(
                'subject' => $order['body'],
                'body' => $order['body'],
                'amount' => intval($order['price'] * 100), // 转成分
                'order_no' => $order['order_no'],
                'currency' => 'cny',
                //'extra' => $this->_getExtra($channel),
                'channel' => $channel,
                'client_ip' => get_client_ip(),
                'app' => array('id' => $this->appId),
                'metadata'=>['order_type'=>$type]); 

            $ch = Charge::create($data);

            return array('iRet' => 1, 'data' => $ch);
        } catch (Base $e) {
            // 捕获报错信息
            if ($e->getHttpStatus() != NULL) {
                header('Status: ' . $e->getHttpStatus());
                return array('iRet' => 0, 'info' => $e->getHttpBody());
            } else {
                return array('iRet' => 0, 'info' => $e->getMessage());
            }
        }
    }

    public function verify(){
        $raw_data = file_get_contents('php://input');

        $headers = Util::getRequestHeaders();

        // 签名在头部信息的 x-pingplusplus-signature 字段
        $signature = isset($headers['X-Pingplusplus-Signature']) ? $headers['X-Pingplusplus-Signature'] : '';
        if (empty($signature)){
            $signature = isset($headers['x-pingplusplus-signature']) ? $headers['x-pingplusplus-signature'] : '';
        }
        empty($signature) && write_log('ping_verify_headers', var_export($headers, 1));

        // Ping++ 公钥
        $pub_key_path = VENDOR_PATH.'Pay/ping/pingpp_rsa_public_key.pem';

        $result = $this->_signature($raw_data, $signature, $pub_key_path);
        if ($result === 1) {
            // 验证通过
            write_log('ping_verify_success', "verification success \t|". $raw_data ." \t|" . $signature);
            $event = json_decode($raw_data, 1);
        } elseif ($result === 0) {
            write_log('ping_verify_error', "verification failed \t|". $raw_data ." \t|" . $signature);
            $event = false;
        } else {
            write_log('ping_verify_error', "verification error \t|". $raw_data ." \t|" . $signature);
            $event = false;
        }

        return $event;
    }

    private function _signature($raw_data, $signature, $pub_key_path){
        $pub_key_contents = file_get_contents($pub_key_path);

        return openssl_verify($raw_data, base64_decode($signature), $pub_key_contents, OPENSSL_ALGO_SHA256);
    }

}