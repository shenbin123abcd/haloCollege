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
        empty($course) || ($course['start_data'] < time()) && $this->error('课程不存在或已经结束');

        vendor('Pay.Payment');

        $pay = new Payment('wxmp');
        $pay->setNotify('http://ke.halobear.com/course/notify');

        $body = $course['title'] . '-' . $course['city'];

        echo $sign = $pay->sign(['subject'=>$body, 'body'=>$body, 'order_no'=>'', 'amount'=>'0.01']);

        //$this->success($sign);
    }
}