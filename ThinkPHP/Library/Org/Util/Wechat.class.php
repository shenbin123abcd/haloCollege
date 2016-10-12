<?php
namespace Org\Util;

use EasyWeChat\Foundation\Application;

class Wechat {
    public $app = null;

    public function __construct($config = array()) {
        $options = [
            'debug'  => true,
            'app_id' => 'wxb43a4c82b5203c21',
            'secret' => '70f8e2b10b41fba0176013f4526edf7b',
            'token'  => 'HaloBear',
            // 'aes_key' => null, // 可选
            'log' => [
                'level' => 'debug',
                'file'  => APP_ROOT . 'logs' . DIRECTORY_SEPARATOR .'wechat'. '_' . date('Y-m-d') . '.log',
            ],
        ];
        $options = array_merge($options, $config);

        $this->app = new Application($options);
    }

    // 获取 OAuth 授权结果用户信息
    public function getUser(){
        $oauth = $this->app->oauth;

        $user = $oauth->user();

        return $user->toArray();
    }
    
    // 消息模板发生消息
    public function sendMsg($openid, $data, $tpl = '9ukmH3wiAMYfTYjzALN6ClFKJOk-pqZjE6JrZMhwavY'){
        $notice = $this->app->notice;

        $map = [
            // 订单通知
            '9ukmH3wiAMYfTYjzALN6ClFKJOk-pqZjE6JrZMhwavY' => [
                'touser' => $openid,
                'template_id' => $tpl,
                'topcolor' => '#ef4c81',
                'data' => [
                    "first"  => array('您收到了一条新的订单', '#ef4c81'),
                    "tradeDateTime"  => isset($data['time']) ? $data['time'] : date('Y-m-d H:i:s'),
                    "orderType"   => "案例",
                    "customerInfo"  => isset($data['customer']) ? $data['customer'] : '',
                    "orderItemName" => "购买商品",
                    "orderItemData" => isset($data['body']) ? $data['body'] : '',
                    "remark" => '截止'. date('d') .'日'. date('H:i') .'分,您尚有'. (isset($data['order_num']) ? $data['order_num'] : 0) .'个订单未处理。',
                ],
            ],
            // 错误通知
            'wx0PZkkiOBq-E2JRZx25znaxdDroebxRak3qo4YhGB8' => [
                'touser' => $openid,
                'template_id' => $tpl,
                'topcolor' => '#ef4c81',
                'data' => [
                    "first"  => array('您好，系统发生了一个错误', '#f24d4d'),
                    "keyword1"  => isset($data['keyword1']) ? $data['keyword1'] : '',
                    "keyword2"   => isset($data['keyword2']) ? $data['keyword2'] : '',
                    "remark" => isset($data['remark']) ? $data['remark'] : '',
                ],
            ],
            // 反馈信息提醒
            'DV7UGPfq2Wt7FhHUmaLa_x6IYmFus4k0AyPJ535dR2A' => [
                'touser' => $openid,
                'template_id' => $tpl,
                'topcolor' => '#ef4c81',
                'data' => [
                    "first"  => array('通知', '#44b549'),
                    "keyword1"  => isset($data['course_guest']) ? $data['course_guest'] : '',
                    "keyword2"   => isset($data['buy_time']) ? $data['buy_time'] : '',
                    "remark" => isset($data['buy_user']) ? $data['buy_user'] : '',
                ],
            ]
        ];

        $messageId = $notice->send($map[$tpl]);
    }
    
    // 发生错误消息
    public function sendErr($err){
        $this->sendMsg('oEgUsswoTQiV9DqWyYPT-EdfC39U', $err, 'wx0PZkkiOBq-E2JRZx25znaxdDroebxRak3qo4YhGB8');
    }
}