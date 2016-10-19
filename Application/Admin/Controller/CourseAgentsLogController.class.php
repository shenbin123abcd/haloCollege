<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/10/16
 * Time: 18:34
 */

namespace Admin\Controller;


class CourseAgentsLogController extends CommonController
{
    protected function _join(&$data){
        $type = ['返佣', '提现', '退款'];
        foreach ($data as $key=>$value) {
            $data[$key]['type'] = $type[$value['type']];
            if ($value['order_id'] > 0){
                $order_id[] = $value['order_id'];
            }
        }

//        $order = M('CourseOrder')->where(['id'=>['in', $order_id]])->select();
//        foreach ($order as $item) {
//
//            $user = M('CourseReserve')->where(array('wechat_id' => $item['wechat_id'], 'course_id' => $item['course_id'], 'type' => 1))->find();
//            $course = M('Course')->where(['id'=>$item['course_id']])->find();
//            $remark = $course['title'] . '（'. $user['name'] .' 购买）';
//            $this->model()->where(['order_id'=>$item['id']])->setField('remark', $remark);
//        }
    }
}