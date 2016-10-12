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

}