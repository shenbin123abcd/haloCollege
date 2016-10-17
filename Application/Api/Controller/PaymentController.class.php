<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/9/13
 * Time: 15:45
 */

namespace Api\Controller;

use Org\Util\Ping;

class PaymentController extends CommonController {
    protected $action_auth = ['createMemberOrder','createVideoOrder'];
    private $payType = ['alipay', 'wx'];

    public function createMemberOrder(){
        $cate_id = intval(I('cate_id'));
        $pay_type = intval(I('pay_type'));


        !in_array($pay_type, [0, 1]) && $this->error('支付参数错误');

        // 检查开通分类
        $cate = M('SchoolMemberCate')->where(array('id'=>$cate_id, 'status'=>1))->find();
        empty($cate) && $this->error('分类不存在');

        // 创建订单
        $model = M('MemberOrder');
        $order = $model->where(array('uid'=>$this->user['id'], 'pay_type'=>$pay_type, 'cate'=>$cate_id, 'status'=>0))->find();
        if (!empty($order) && $order['exp_time'] > time() && !empty($order['sign'])) {
            $this->success('success',unserialize($order['sign']));
        }else{
            $model->where(array('id'=>$order['id']))->save(array('status'=>2));
            $order = array();
        }
        //$cate['price'] = 0.01;
        if (empty($order)){
            $body = '【幻熊学院】' . $cate['title'];
            $order['order_no'] = 'MB' . date("d") . rand(10,99) . implode(explode('.', microtime(1)));
            $order['uid'] = $this->user['id'];
            $order['cate'] = $cate_id;
            $order['price'] = $cate['count_price'];
            $order['pay_type'] = $pay_type;
            $order['body'] = $body;
            $order['exp_time'] = time() + 6900;
            $order['pay_time'] = 0;
            $order['create_time'] = time();
            $order['type'] = 0;
            $order['status'] = 0;
            $order['sign'] = '';

            $order_id = $model->add($order);

            vendor('Pay.Ping');

            $ping = new Ping();

            $data = array('price'=>$order['price'], 'order_no'=>$order['order_no'], 'body'=>$body);
            $return = $ping->pay($data, $this->payType[$order['pay_type']]);
            if ($return['iRet'] == 0){
                $model->where(array('id'=>$order_id))->delete();

                $info = json_decode($return['info'], 1);
                if ($info && isset($info['error']['message'])){
                    $return['info'] = $info['error']['message'];
                }
                $this->error($return['info']);
            }else{
                $model->where(array('id'=>$order_id))->setField('sign', serialize($return['data']));
                $this->success('success',$return['data']);
            }
        }
    }

    public function createVideoOrder(){
        $cate_id = intval(I('cate_id'));
        $pay_type = intval(I('pay_type'));
        $vid = intval(I('vid'));
        $order_type = I('order_type');

        !in_array($pay_type, [0, 1]) && $this->error('支付参数错误');

        // 检查收费类型
        $cate = M('VideoChargeStandard')->where(array('id'=>$cate_id, 'status'=>1))->find();
        empty($cate) && $this->error('收费标准不存在');

        //检查收费类型跟视频是否匹配
        $video = M('SchoolVideo')->where(array('id'=>$vid,'status'=>1))->find();
        empty($video) && $this->error('视频不存在！');
        empty($video['charge_standard']) && $this->error('视频与收费类型不匹配！');
        $standard_arr = explode(',',$video['charge_standard']);
        !in_array($cate_id,$standard_arr) && $this->error('视频与收费类型不匹配！');

        // 创建订单
        $model = M('VideoOrder');
        $order = $model->where(array('uid'=>$this->user['id'], 'pay_type'=>$pay_type, 'cate'=>$cate_id,'vid'=>$vid, 'status'=>0))->find();
        if (!empty($order) && $order['exp_time'] > time() && !empty($order['sign'])) {
            $this->success('success',unserialize($order['sign']));
        }else{
            $model->where(array('id'=>$order['id']))->save(array('status'=>2));
            $order = array();
        }
        //$cate['price'] = 0.01;
        if (empty($order)){
            $body = '【幻熊学院】' . $cate['note'];
            $order['order_no'] = 'MB' . date("d") . rand(10,99) . implode(explode('.', microtime(1)));
            $order['uid'] = $this->user['id'];
            $order['cate'] = $cate_id;
            $order['price'] = $cate['price'];
            $order['pay_type'] = $pay_type;
            $order['body'] = $body;
            $order['exp_time'] = time() + 6900;
            $order['pay_time'] = 0;
            $order['create_time'] = time();
            $order['type'] = 0;
            $order['status'] = 0;
            $order['sign'] = '';
            $order['vid'] = $vid;

            $order_id = $model->add($order);

            vendor('Pay.Ping');

            $ping = new Ping();

            $data = array('price'=>$order['price'], 'order_no'=>$order['order_no'], 'body'=>$body);
            $return = $ping->pay($data, $this->payType[$order['pay_type']],$order_type);
            if ($return['iRet'] == 0){
                $model->where(array('id'=>$order_id))->delete();

                $info = json_decode($return['info'], 1);
                if ($info && isset($info['error']['message'])){
                    $return['info'] = $info['error']['message'];
                }
                $this->error($return['info']);
            }else{
                $model->where(array('id'=>$order_id))->setField('sign', serialize($return['data']));
                $this->success('success',$return['data']);
            }
        }
    }

    public function haloNotify(){
        $this->_checkIp();

        vendor('Pay.Ping');
        $ping = new Ping();

        $event = $ping->verify();

        // 对异步通知做处理
        if (!isset($event['type'])) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');

            write_log('ping_notify_error', '支付完成，校验失败');
            write_log('ping_notify_error', var_export($event, 1));
            exit("fail");
        }
        switch ($event['type']) {
            case "charge.succeeded":
                // 支付异步通知
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');

                $object = $event['data']['object'];
                if($object['paid'] === true){
                    switch ($object['metadata']['order_type']){
                        case 'member':
                            $this->_paySuccess($object);
                            break;
                        case 'video':
                            $this->_payVideoSuccess($object);
                            break;
                    }
                }

                break;
            case "refund.succeeded":
                // 退款异步通知
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                break;
            default:
                header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
                break;
        }
    }

    //会员支付成功后的回调处理
    private function _paySuccess($data) {
        // 检查订单状态
        $model = M('MemberOrder');
        $order = $model->where('order_no = \'' . $data['order_no'] . '\'')->find();

        // 订单不存在，或已经付款
        if(empty($order)){
            // 微信错误通知
            write_log('ping_notify_error', '支付完成，订单不存在|订单号：'. $data['order_no']. '；支付方式：'. $data['channel'].'；交易号：'. $data['transaction_no']);
            return false;
        }elseif ($order['status'] == 1){
            write_log('ping_notify_error', '支付完成，已付款完成，不能重复操作|订单号：'. $data['order_no']. '；支付方式：'. $data['channel'].'；交易号：'. $data['transaction_no']);
            return false;
        }else{
            // 修改订单状态
            $model->where(array('id'=>$order['id']))->save(array('status'=>1, 'pay_time'=>time(), 'transaction_id'=>$data['transaction_no']));
            write_log('a', M()->_sql());
        }

        // 会员开通时长
        $cycle = M('SchoolMemberCate')->where(array('id'=>$order['cate']))->getField('cycle');

        $member = M('SchoolMember')->where(array('uid'=>$order['uid']))->find();
        $end_time = time() + $cycle*30*86400;
        if (empty($member)){
            M('SchoolMember')->add(array('uid'=>$order['uid'], 'end_time'=>$end_time, 'create_time'=>time(), 'update_time'=>time(), 'status'=>1));
        }else{
            // 是否过期
            if ($member['end_time'] > time()){
                $end_time = $member['end_time'] + $cycle*30*86400;
            }
            M('SchoolMember')->where(array('uid'=>$order['uid']))->save(array('end_time'=>$end_time, 'update_time'=>time()));
        }

        //会员购买成功后的消息推送
        $this->member_notice($order['uid'],$order['body']);
       
        return true;
    }

    //视频购买成功后的的回调处理
    private function _payVideoSuccess($data) {
        // 检查订单状态
        $model = M('VideoOrder');
        $order = $model->where('order_no = \'' . $data['order_no'] . '\'')->find();

        // 订单不存在，或已经付款
        if(empty($order)){
            // 微信错误通知
            write_log('ping_notify_error', '支付完成，订单不存在|订单号：'. $data['order_no']. '；支付方式：'. $data['channel'].'；交易号：'. $data['transaction_no']);
            return false;
        }elseif ($order['status'] == 1){
            write_log('ping_notify_error', '支付完成，已付款完成，不能重复操作|订单号：'. $data['order_no']. '；支付方式：'. $data['channel'].'；交易号：'. $data['transaction_no']);
            return false;
        }else{
            // 修改订单状态
            $model->where(array('id'=>$order['id']))->save(array('status'=>1, 'pay_time'=>time(), 'transaction_id'=>$data['transaction_no']));
            write_log('a', M()->_sql());
        }        

        $buy_record = M('VideoBuyRecord')->where(array('uid'=>$order['uid'],'vid'=>$order['vid'],'status'=>1))->find();
        if (empty($buy_record)){
            M('VideoBuyRecord')->add(array('uid'=>$order['uid'], 'vid'=>$order['vid'], 'create_time'=>time(), 'update_time'=>time(), 'status'=>1,'charge_standard'=>$order['cate']));
        }else{            
            M('VideoBuyRecord')->where(array('uid'=>$order['uid'],'vid'=>$order['vid']))->save(array('charge_standard'=>$buy_record['charge_standard'].','.$order['cate'], 'update_time'=>time()));
        }
        return true;
    }

    // 检查IP的合法性
    private function _checkIp(){
        $ip = get_client_ip();
        $ping_ip = ['121.41.137.124','120.55.109.27','115.29.181.209','115.29.180.6','121.41.124.180','121.41.124.121','121.41.124.240','121.41.126.254','180.153.214.180','180.173.1.204'];
        if (!in_array($ip, $ping_ip)){
            send_http_status(401);
            write_log('ping_notify_error', '支付完成，IP校验失败来源ip' . $ip);
            exit("fail");
        }
    }

    //会员购买成功消息通知
    public function member_notice($uid,$content){
        $push = A('Push');
        $status = is_login($uid);
        if ($status){
            $result = $push->pushMsgPersonal(array('uid'=>$uid,'content'=>'尊敬的会员'.','.'您已成功购买'.$content,'extra'=>array('push_time'=>time()),'type'=>3));
        }
        $msg['from_uid'] = 0;
        $msg['from_username'] = '';
        $msg['to_uid'] = $uid;
        $msg['content'] = '尊敬的会员'.','.'您已成功购买'.$content;
        $msg['detail_id'] = 0;
        $msg['msg_type'] = 3;
        $msg['push_time'] = time();
        $msg['extra'] = '';
        $msg['is_read'] = 0;
        $msg['remark_type'] = 0;
        $msg['msg_no'] = date("d") . rand(10,99) . implode(explode('.', microtime(1)));
        $push_msg = M('PushMsg')->add($msg);
    }
}