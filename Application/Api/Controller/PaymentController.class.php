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
    protected $action_auth = ['createMemberOrder'];
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
        $cate['price'] = 0.01;
        if (empty($order)){
            $body = $cate['title'];
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

    public function haloNotify(){
        $this->_checkIp();

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
                    $this->_paySuccess($object);
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

    private function _paySuccess($data) {
        // 检查订单状态
        $model = M('MemberOrder');
        $order = $model->where('order_no = \'' . $data['order_no'] . '\'')->find();

        // 订单不存在，或已经付款
        if(empty($order)){
            // 微信错误通知
            write_log('ping_notify_error', '支付完成，订单不存在|订单号：'. $data['order_no']. '；支付方式：'. $data['channel'].'；交易号：'. $data['transaction_no']);
            return false;
        }elseif ($order['pay_status'] == 1){
            write_log('ping_notify_error', '支付完成，已付款完成，不能重复操作|订单号：'. $data['order_no']. '；支付方式：'. $data['channel'].'；交易号：'. $data['transaction_no']);
            return false;
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
        return true;
    }

    // 检查IP的合法性
    private function _checkIp(){
        $ip = get_client_ip();
        $ping_ip = ['121.41.137.124','120.55.109.27','115.29.181.209','115.29.180.6','121.41.124.180','121.41.124.121','121.41.124.240','121.41.126.254'];
        if (!in_array($ip, $ping_ip)){
            send_http_status(401);
            write_log('ping_notify_error', '支付完成，IP校验失败来源ip' . $ip);
            exit("fail");
        }
    }
}