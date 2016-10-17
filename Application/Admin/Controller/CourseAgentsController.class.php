<?php

namespace Admin\Controller;

class CourseAgentsController extends CommonController {
    public function _join(&$data){
        foreach ($data as $key=>$item) {
            $data[$key]['code'] = 'http://ke.halobear.com?code=' . $item['code'];
        }
    }

    public function tixian(){
        if (IS_POST){
            $amount = I('amount');
            $id = I('id');

            if (empty($amount)){
                $this->error('请填写提现金额');
            }

            $balance = $this->model()->where(['id'=>$id])->getField('balance');
            if (empty($balance) || $amount > $balance){
                $this->error('金额不能超过 ' . $balance);
            }

            $remark = '余额提现';
            $data = ['agents_id'=>$id, 'order_id'=>0, 'amount'=>$balance - $amount, 'amount_log'=> -$amount, 'create_time'=>time(), 'type'=>1, 'remark'=>$remark];
            M('CourseAgentsLog')->add($data);

            $this->model()->where(['id'=>$id])->setField('balance', $balance - $amount);

            $this->success('提现成功');
        }
        $this->display();
    }


}