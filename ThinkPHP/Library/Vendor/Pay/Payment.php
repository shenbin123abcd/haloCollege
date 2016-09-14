<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/8/31
 * Time: 17:38
 */
namespace Org\Util;


use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use mytharcher\sdk\alipay\Alipay;

class Payment {
    /**
     * @var string 支付类型
     */
    private $type = 'alipay';

    private $notifyUrl;

    /**
     * @var array 支付宝配置
     */
    private $alipay = [
        // 即时到账方式
        'payment_type' => 1,
        // 传输协议
        'transport' => 'http',
        // 编码方式
        'input_charset' => 'utf-8',
        // 签名方法
        'sign_type' => 'RSA',
        // 支付完成异步通知调用地址
        'notify_url' => '',
        // 支付完成同步返回地址
        'return_url' => '',
        // 证书路径
        'cacert' => VENDOR_PATH.'Pay/alipay/cacert.pem',
        'alipay_public_key' => VENDOR_PATH.'Pay/alipay/key/alipay_public_key.pem',
        'private_key_path' => VENDOR_PATH.'Pay/alipay/key/rsa_private_key.pem',
        // 支付宝商家 ID
        'partner'      => '2088611020356950',
        'seller_id'      => '2088611020356950',
        // 支付宝商家 KEY
        'key'          => 'jcb66m7er34j4ony0r7fq0pn3eqphgld',
        // 支付宝商家注册邮箱
        'seller_email' => 'ceo@halobear.com'
    ];

    private $wechat = [
        /**
         * Debug 模式，bool 值：true/false
         *
         * 当值为 false 时，所有的日志都不会记录
         */
        'debug'  => true,

        /**
         * 账号基本信息，请从微信公众平台/开放平台获取
         */
        'app_id'  => 'wx996be0675a98e2be',         // AppID
        'secret'  => '505571020b8245f3e7c97992b5fe1ba0',     // AppSecret
        'token'   => 'hxkj20090725hxkj2009072520090725',          // Token
        'aes_key' => '',                    // EncodingAESKey，安全模式下请一定要填写！！！

        /**
         * 日志配置
         *
         * level: 日志级别, 可选为：
         *         debug/info/notice/warning/error/critical/alert/emergency
         * file：日志文件位置(绝对路径!!!)，要求可写权限
         */
        'log' => [
            'level' => 'debug',
            'file'  => LOG_PATH . '/Ke/wechat.log',
        ],

        // payment
        'payment' => [
            'merchant_id'        => '1385772202',
            'key'                => '227c6daa608c56b1eb06ed79f8d9c119',
            'cert_path'          => VENDOR_PATH.'Pay/wechat/api_cert.pem',
            'key_path'           => VENDOR_PATH.'Pay/wechat/api_key.pem',
            'notify_url'         => '',       // 你也可以在下单时单独设置来想覆盖它
            // 'device_info'     => '013467007045764',
            // 'sub_app_id'      => '',
            // 'sub_merchant_id' => '',
            // ...
        ],
    ];
    // 公众平台
    private $wxmp = [
        /**
         * Debug 模式，bool 值：true/false
         *
         * 当值为 false 时，所有的日志都不会记录
         */
        'debug'  => true,

        /**
         * 账号基本信息，请从微信公众平台/开放平台获取
         */
        'app_id'  => 'wxb43a4c82b5203c21',         // AppID
        'secret'  => '70f8e2b10b41fba0176013f4526edf7b',     // AppSecret
        'token'   => 'hxkj20090725hxkj2009072520090725',          // Token
        'aes_key' => '',                    // EncodingAESKey，安全模式下请一定要填写！！！

        /**
         * 日志配置
         *
         * level: 日志级别, 可选为：
         *         debug/info/notice/warning/error/critical/alert/emergency
         * file：日志文件位置(绝对路径!!!)，要求可写权限
         */
        'log' => [
            'level' => 'debug',
            'file'  => LOG_PATH . '/Ke/wxmp.log',
        ],

        // payment
        'payment' => [
            'merchant_id'        => '10017739',
            'key'                => 'CoFAFc040Dk2EB1bDEgE62eB9A7653t0',
            'cert_path'          => VENDOR_PATH.'Pay/wxmp/apiclient1_cert.pem',
            'key_path'           => VENDOR_PATH.'Pay/wxmp/apiclient1_key.pem',
            'notify_url'         => '',       // 你也可以在下单时单独设置来想覆盖它
            // 'device_info'     => '013467007045764',
            // 'sub_app_id'      => '',
            // 'sub_merchant_id' => '',
            // ...
        ],
    ];

    public function __construct($type = 'alipay'){
        $this->type = $type;
    }

    /**
     * 设置异步通知地址
     * @param $notify string 地址
     */
    public function setNotify($notify){
        $this->notifyUrl = $notify;
    }

    /**
     * 获取支付签名
     * @param $order array (subject,body,amount,order_no)
     * @return mixed
     */
    public function sign($order){
        $obj = $this->type . 'Sign';

        return $this->$obj($order);
    }

    /**
     * 获取支付宝订单签名
     * @param $order
     * @return mixed
     */
    private function alipaySign($order){
        $this->alipay['notify_url'] = $this->notifyUrl;
        $obj = new Alipay($this->alipay,'app');

        $params = array(
            'out_trade_no' => $order['order_no'],
            'subject' => $order['body'],
            'body' => $order['body'],
            'total_fee' => $order['price'],
            '_input_charset' => 'utf-8',
            'sign_type' => 'RSA',
            'it_b_pay'=>date('Y-m-d H:i:s', time() + 7200)
        );

        $config = $obj->buildSignedParametersForApp($params);
        return $config ? array('iRet'=>1, 'data'=>$config) : array('iRet'=>0, 'info'=>'签名错误');
    }

    private function alipaySign2($order){
        \Pingpp\Pingpp::setApiKey('sk_live_8m5yX1TerD0CaXnPGOa5yDCS');
        \Pingpp\Pingpp::setPrivateKeyPath(VENDOR_PATH.'Pay/alipay/ping/ping_rsa_private_key.pem');

        $data = array(
            'subject' => $order['body'],
            'body' => $order['body'],
            'amount' => intval($order['price'] * 100), // 转成分
            'order_no' => $order['order_no'],
            'currency' => 'cny',
            //'extra' => $this->_getExtra($channel),
            'channel' => 'alipay',
            'client_ip' => get_client_ip(),
            'app' => array('id' => 'app_4CSWP48m1eLGC080'));

        $ch = \Pingpp\Charge::create($data);

        return array('iRet' => 1, 'data' => $ch['credential']['alipay']['orderInfo']);
    }

    private function wechatSign($order){
        $this->wechat['payment']['notify_url'] = $this->notifyUrl;
        $app = new Application($this->wechat);
        $payment = $app->payment;

        // 创建订单
        $attributes = [
            'trade_type'       => 'APP',
            'body'             => '幻熊学院-' . $order['subject'],
            'detail'           => $order['body'],
            'out_trade_no'     => $order['order_no'],
            'total_fee'        => $order['amount'] * 100
        ];
        $order = new Order($attributes);

        // 下单
        $result = $payment->prepare($order);
        $config = false;
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepay_id = $result->prepay_id;

            // 生成支付配置
            $config = $payment->configForAppPayment($prepay_id);
        }

        return $config ? array('iRet'=>1, 'data'=>$config) : array('iRet'=>0, 'info'=>$result->return_msg);
    }

    private function wxmpSign($order){
        $this->wxmp['payment']['notify_url'] = $this->notifyUrl;
        $app = new Application($this->wxmp);
        $payment = $app->payment;

        // 创建订单
        $attributes = [
            'trade_type'       => 'JSAPI',
            'body'             => $order['subject'],
            'detail'           => $order['body'],
            'out_trade_no'     => $order['order_no'],
            'total_fee'        => $order['amount'] * 100,
            'openid'           => $order['openid'],
        ];
        $order = new Order($attributes);

        // 下单
        $result = $payment->prepare($order);
        $config = false;
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepay_id = $result->prepay_id;

            // 生成支付配置
            $config = $payment->configForJSSDKPayment($prepay_id);
            if($config && isset($config['timestamp'])){
                $config['timeStamp'] = $config['timestamp'];
                unset($config['timestamp']);
            }
        }

        return $config ? array('iRet'=>1, 'data'=>$config) : array('iRet'=>0, 'info'=>$result->return_msg);
    }

    public function verify(){
        $obj = $this->type . 'Verify';
        return $this->$obj();
    }

    private function alipayVerify(){
        $alipay = new Alipay($this->alipay,'app');
        // 获得验证结果 true/false
        return $alipay->verifyCallback();
    }

    public function wxmpVerify(){
        return new Application($this->wxmp);
    }

    private function wechatVerify(){
        return new Application($this->wechat);
    }
}