<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/9/6
 * Time: 17:14
 */

namespace Ke\Controller;


use Org\Util\Payment;

class PayController extends CommonController {
    public function course(){
        $course_id = intval(I('course_id'));
        empty($course_id) && $this->error('课程编号错误');

        $course = M('Course')->where(array('id'=>$course_id, 'status'=>1))->find();
        empty($course) || ($course['start_date'] < time()) && $this->error('课程不存在或已经结束');

        $model = M('CourseOrder');
        // 检查用户是否已经报名
        $count = $model->where(array('wechat_id'=>$this->user['id'], 'course_id'=>$course_id, 'status'=>1))->count();
        $count && $this->error('你已经报过名了，不能重复报名');

        // 创建订单
        $order = $model->where(array('wechat_id'=>$this->user['id'], 'course_id'=>$course_id, 'status'=>0))->find();
        if (!empty($order) && $order['exp_time'] > time() && !empty($order['sign'])) {
            $this->success(unserialize($order['sign']));
        }else{
            $model->where(array('id'=>$order['id']))->save(array('status'=>2));
            $order = array();
        }

        if (empty($order)){
            $body = $course['title'] . '-' . $course['city'];
            $order['wechat_id'] = $this->user['id'];
            $order['course_id'] = $course_id;
            $order['body'] = $body;
            $order['order_no'] = 'KE' . date("d") . rand(10,99) . implode(explode('.', microtime(1)));
            $order['exp_time'] = time() + 6900;
            $order['create_time'] = time();
            $order['price'] = $course['price'];
            $order['type'] = 0;
            $order['status'] = 0;
            $order['sign'] = '';

            $order_id = $model->add($order);

            vendor('Pay.Payment');

            $pay = new Payment('wxmp');
            $pay->setNotify('http://ke.halobear.com/pay/notifyn');

            $sign = $pay->sign(['subject'=>$body, 'body'=>$body, 'order_no'=>$order['order_no'], 'amount'=>$order['price'], 'openid'=>$this->user['openid']]);

            if ($sign['iRet'] && $sign['data']){
                $model->where(array('id'=>$order_id))->setField('sign', serialize($sign['data']));
                $this->success($sign['data']);
            }else{
                $model->where(array('id'=>$order_id))->delete();
                $this->error($sign['info']);
            }
        }

    }

    public function notifyn(){
        vendor('Pay.Payment');
        $pay = new Payment('wxmp');
        $notify = $pay->wxmpVerify();

        $response = $notify->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = M('CourseOrder')->where(array('order_no'=>$notify['out_trade_no']))->find();
            if (!$order) { // 如果订单不存在
                return 'Order not exist.';
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ($order['pay_time']) {
                return true;
            }
            // 用户是否支付成功
            if ($successful) {
                // 不是已经支付状态则修改为已经支付状态
                $data['pay_time'] = time();
                $data['status'] = 1;
                $data['transaction_id'] = $notify['transaction_id'];
                $ret = M('CourseOrder')->where(array('id'=>$order['id']))->save($data);

                if ($ret){
                    M('Course')->where(array('id'=>$order['course_id']))->setInc('num');
                }
            } else { // 用户支付失败
                write_log('pay_mpwx_error' . date('Ymd'), var_export($notify, 1));
                write_log('pay_mpwx_error' . date('Ymd'), var_export($successful, 1));
            }
            return true; // 返回处理完成
        });
        $response->send();
    }
}