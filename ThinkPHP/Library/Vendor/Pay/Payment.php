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
        'cacert' => VENDOR_PATH.'Pay/alipay/rsa_public_key_alipay.pem',
        'private_key_path' => VENDOR_PATH.'Pay/alipay/rsa_private_key.pem',
        // 支付宝商家 ID
        'partner'      => '2088611020356950',
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
            'key'                => 'hxkj20090725hxkj2009072520090725',
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
            'subject' => $order['subject'],
            'body' => $order['body'],
            'total_fee' => $order['amount'],
            '_input_charset' => 'utf-8',
            'sign_type' => 'RSA'
        );
        return $obj->buildSignedParametersForApp($params);
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
        $result = $payment->prepare($order);dump($result);
        $config = false;
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepay_id = $result->prepay_id;

            // 生成支付配置
            $config = $payment->configForAppPayment($prepay_id);
        }

        return $config;
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
        $app = new Application($this->wechat);
        $response = $app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = 查询订单($notify['out_trade_no']);
            if (!$order) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ($order->paid_at) { // 假设订单字段“支付时间”不为空代表已经支付
                return true; // 已经支付成功了就不再更新了
            }
            // 用户是否支付成功
            if ($successful) {
                // 不是已经支付状态则修改为已经支付状态
                $order->paid_at = time(); // 更新支付时间为当前时间
                $order->status = 'paid';
            } else { // 用户支付失败
                $order->status = 'paid_fail';
            }
            $order->save(); // 保存订单
            return true; // 返回处理完成
        });
        $response->send();
    }
}