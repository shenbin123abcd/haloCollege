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

        // 检查是否已经爆满
        $course['total'] <= $course['num'] && $this->error('抱歉，你来晚了该课程已爆满');

        $model = M('CourseOrder');
        // 检查用户是否已经报名
        $count = $model->where(array('wechat_id'=>$this->user['id'], 'course_id'=>$course_id, 'status'=>1))->count();
        $count && $this->error('你已经报过名了，不能重复报名');

        // 创建订单
        $order = $model->where(array('wechat_id'=>$this->user['id'], 'course_id'=>$course_id, 'status'=>0))->find();
        if (!empty($order) && $order['exp_time'] > time() && !empty($order['sign'])) {
            $this->success(array('config'=>unserialize($order['sign']), 'order_id'=>$order['order_no']));
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
                $this->success(array('config'=>$sign['data'], 'order_id'=>$order['order_no']));
            }else{
                $model->where(array('id'=>$order_id))->delete();
                $this->error($sign['info']);
            }
        }

    }

    public function notifyn(){
        vendor('Pay.Payment');
        $pay = new Payment('wxmp');
        $app = $pay->wxmpVerify();

        $response = $app->payment->handleNotify(function($notify, $successful){
            write_log('pay_mpwx_course_send' . date('Ymd'), var_export($notify, 1));
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = M('CourseOrder')->where(array('order_no'=>$notify['out_trade_no'], 'price'=>$notify['total_fee']/100))->find();
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
                    // 报名状态
                    M('CourseReserve')->where(array('wechat_id' => $order['wechat_id'], 'course_id' => $order['course_id'], 'type' => 1))->setField('status', 1);
                    M('Course')->where(array('id'=>$order['course_id']))->setInc('num');
                    // 短信通知
                    $phone = M('CourseReserve')->where(array('wechat_id' => $order['wechat_id'], 'course_id' => $order['course_id'], 'type' => 1))->getField('phone');
                    $this->_notice($order['course_id'], $phone);

                    //微信通知
                    $this->_wechat_notice('DV7UGPfq2Wt7FhHUmaLa_x6IYmFus4k0AyPJ535dR2A',$order['course_id'],$order['wechat_id'],$this->user['username']);
                }
            } else { // 用户支付失败
                write_log('pay_course_error' . date('Ymd'), var_export($notify, 1));
                write_log('pay_course_error' . date('Ymd'), var_export($successful, 1));
            }
            return true; // 返回处理完成
        });
        $response->send();
    }

    /**
     *  金熊奖 type 1 现场购买 2邮寄
     */
    public function book(){
        $type = intval(I('type'));
        $num = intval(I('num'));
        if (($type != 1 && $type != 2) || $num <= 0) {
            $this->error('参数错误！');
        }

        $data = $this->_createBookOrder($type, $num);

        $sign = $data['sign'];
        if ($sign['iRet'] == 0 && $sign['data']) {
            $this->error($sign['info']);
        }
        $sign_data = $sign['data'];
        if(!$sign_data) {
            $info = $sign['result_code'] == 'FAIL' ? $sign_data['err_code_des'] : '参数错误';
            write_log('wfc2016_order_error', var_export($sign_data, 1));
            $this->error($info, $sign_data);
        }else{
            $this->success(array('config'=>$sign_data, 'order_id'=>$data['order']['order_no']));
        }
    }

    /**
     * 创建案例订单
     */
    private function _createBookOrder($type, $num){
        $model = M('wfc2016_order_case');
        // $map = array(1=>0.01, 2=>0.02);
        $map = array(1=>499, 2=>499);

        //①、获取用户openid
        $openid = $this->user['openid'];

        // 金额
        $price = $map[$type] * $num;

        // 检查订单是否过期
        $order = $model->where(array('openid'=>$openid, 'module'=>'book', 'status'=>0, 'type'=>$type))->find();
        if (!empty($order)) {
            $model->where(array('id'=>$order['id']))->save(array('status'=>2));
            $order = array();
        }

        if (empty($order)) {
            $order['openid'] = $openid;
            $order['body'] = '精装限量版金熊奖参赛作品集' . ($type == 2 ? '（含邮费）' : '');
            $order['order_no'] = 'BK' . date("d") . rand(10,99) . implode(explode('.', microtime(1)));
            $order['exp_time'] = time() + 6900;
            $order['create_time'] = time();
            $order['price'] = $price;
            $order['type'] = $type;
            $order['record_id'] = $this->user['id'];
            $order['goods_name'] = $order['body'];
            $order['goods_cover'] = 'http://7xkkfd.com1.z0.glb.clouddn.com/goldbear-book.png';
            $order['goods_url'] = 'http://ke.halobear.com/uc/book';
            $order['module'] = 'book';
            $order['num'] = $num;
            $order['status'] = $order['pay_time'] = 0;

            $model->add($order);
        }else{
            $order['order_no'] = 'BK' . date("d") . rand(10,99) . implode(explode('.', microtime(1)));
            $order['exp_time'] = time() + 6900;
            $order['price'] = $price;
            $model->save($order);
        }

        vendor('Pay.Payment');

        $pay = new Payment('wxmp');
        $pay->setNotify('http://ke.halobear.com/pay/booknotifyn');

        $sign = $pay->sign(['subject'=>$order['body'], 'body'=>$order['body'], 'order_no'=>$order['order_no'], 'amount'=>$order['price'], 'openid'=>$this->user['openid']]);
        return ['sign'=>$sign, 'order'=>$order];
    }

    public function booknotifyn(){
        vendor('Pay.Payment');
        $pay = new Payment('wxmp');
        $notify = $pay->wxmpVerify();

        $response = $notify->payment->handleNotify(function($notify, $successful){
            write_log('pay_mpwx_book_send' . date('Ymd'), var_export($notify, 1));
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $model = M('wfc2016_order_case');
            $order = $model->where(array('order_no'=>$notify['out_trade_no']))->find();
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
                // 设置订单状态
                $model->where(array('id'=>$order['id']))->save($data);
            } else { // 用户支付失败
                write_log('pay_mpwx_book_error' . date('Ymd'), var_export($notify, 1));
                write_log('pay_mpwx_book_error' . date('Ymd'), var_export($successful, 1));
            }
            return true; // 返回处理完成
        });
        $response->send();
    }

    // 报名成功后短信通知
    private function _notice($course_id, $phone){
        $course = M('Course')->where(array('id'=>$course_id))->find();

        if (!empty($course)){
            // 上课日期
            $date = date('m月d日', $course['start_date']);
            $title = '《'. $course['title'] .'》';
            $guest = M('SchoolGuests')->where(['id'=>$course['guest_id']])->find();

            // 地点
            $place = $course['place'];

            if ($course['cate_id'] == 1){
                // 公开课
                send_msg($phone, array($date, $title), 119538, '8aaf070857418a58015745ded06402d3');
            }else{
                // 培训营
                send_msg($phone, array($date, $title, $guest['title'] . '老师',$place), 119508, '8aaf070857418a58015745ded06402d3');
            }
        }
    }

    //微信通知
    private function _wechat_notice($tpl,$course_id,$wechat_id,$username){
        $openid = array('oEgUssxz4-N1oTyiHDCs7I1qvlO4','oEgUssy9t6hZ-nresv0kImH0UCzg');
        $course = M('Course')->where(array('id'=>$course_id))->find();
        $course_reserve = M('CourseReserve')->where(array('course_id'=>$course_id,'wechat_id'=>$wechat_id))->find();
        $data_wechat_notice=array(
            'course_guest'=>$course_reserve['name'].'|'.$course['title'],
            'buy_time'=>date('Y-m-d',time()),
            'buy_user'=>$username.' '.'报名'
        );
        $wechat = new \Org\Util\Wechat();
        foreach ($openid AS $value){
            $wechat->sendMsg($value, $data_wechat_notice, $tpl);
        }
    }
}