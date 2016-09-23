<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/9/6
 * Time: 17:14
 */

namespace Ke\Controller;


class PublicController extends CommonController {
    // 预约提醒
    public function reserveNotice(){
        $list = M('CourseReserve')->where(['type'=>0, 'is_notice'=>0,'status'=>1])->getField('course_id,phone');

        $course_id = array_keys($list);
        // 开放报名的课程
        $model = D('Course');
        $course = $model->where(array('id'=>$course_id, 'step'=>1))->getField('id,title,start_date,day,place,guest_id');

        $guest_model = M('SchoolGuests');
        foreach ($course AS $value){
            $phone = $list[$value['id']];
            $date = $model->_parseDate($value['start_date'], $value['day']);
            $link = get_url('http://ke.halobear.com/course/detail_' . $value['id']);
            $guest = $guest_model->where(['id'=>$value['guest_id']])->find();

            $title = $guest['position'] . $guest['title'] . '《'. $value['title'] .'》';

            $ret = send_msg($phone, array($title, $date, $value['place'], $link), 118547, '8aaf070857418a58015745ded06402d3');

            M('CourseReserve')->where(['course_id'=>$value['id'], 'phone'=>$phone])->setField('is_notice', 1);
        }
    }

}