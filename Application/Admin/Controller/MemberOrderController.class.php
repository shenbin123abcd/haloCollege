<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/10
 * Time: 18:27
 */

namespace Admin\Controller;


class MemberOrderController extends CommonController{
    
    public function _join(&$data){
        $pay_type = ['<b style="color: blue">支付宝</b>','<b style="color: #00dd1c">微信</b>'];
        $status_arr = ['<b style="color: #774400">未支付</b>','<b style="color: green">支付成功</b>','<b style="color: red">订单已过期</b>'];
        foreach ($data as $key=>$value){
            $uid_arr[] = $value['uid'];
        }
        array_unique($uid_arr);
        if (!empty($uid_arr)){
            $user = M('SchoolAccount')->where(array('status'=>1,'id'=>array('in',$uid_arr)))->getField('id,username,phone');            
            foreach ($data as $key=>$value){
                $data[$key]['username'] = $user[$value['uid']['username']];
                $data[$key]['phone'] = $user[$value['uid']['phone']];
                $data[$key]['pay_type'] = $pay_type[$value['pay_type']];
                $data[$key]['status'] = $status_arr[$value['status']];
            }
        }

    }

    public function _before_index(){
        $this->pay_status = array('未支付','已支付','订单过期');
        $this->member_cate = M('SchoolMemberCate')->select();
    }

    public function _before_add(){
        $this->member_cate = M('SchoolMemberCate')->where(array('status'=>1))->select();
    }

    public function _before_insert(){
        empty($_POST['uid']) && $this->error('用户ID不能为空！');
        $_POST['uid'] = !preg_match('/[^0-9]/',$_POST['uid']) ? (int)$_POST['uid'] : $this->error('用户ID只能为整数！');
        empty($_POST['cate']) && $this->error('开通的会员类型不能为空！');
        $cate = M('SchoolMemberCate')->where(array('id'=>$_POST['cate']))->find();
        $_POST['order_no'] = '';
        $_POST['price'] = $cate['count_price'];
        $_POST['pay_type'] = 0;
        $_POST['body'] = $cate['title'];
        $_POST['pay_time'] = time();
        $_POST['transaction_id'] = '';
        $_POST['sign'] = '';
        $_POST['create_time'] = time();
        $_POST['exp_time'] = 0;
        $_POST['status'] =1;
    }

}