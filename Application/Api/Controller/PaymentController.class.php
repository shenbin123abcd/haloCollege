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
            $this->success(unserialize($order['sign']));
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

            vendor('Pay.Payment');

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
                $this->success(array('config'=>$return['data'], 'order_id'=>$order['order_no']));
            }
        }
    }

    public function haloNotify(){

    }
}