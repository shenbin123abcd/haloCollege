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

        // 创建订单
        $model = M('CourseOrder');
        $order = $model->where(array('wechat_id'=>$this->user['id'], 'course_id'=>$course_id, 'status'=>0))->find();
        if (!empty($order) && $order['exp_time'] > time()) {
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
            $pay->setNotify('http://ke.halobear.com/course/notifyn');

            $sign = $pay->sign(['subject'=>$body, 'body'=>$body, 'order_no'=>$order['order_no'], 'amount'=>$order['price'], 'openid'=>$this->user['openid']]);

            if ($sign['iRet']){
                $model->where(array('id'=>$order_id))->setField('sign', serialize($sign['data']));
                $this->success($sign['data']);
            }else{
                $this->error($sign['info']);
            }
        }

    }
}