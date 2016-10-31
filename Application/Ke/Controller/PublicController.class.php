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

    // 检票
    public function userInfo(){
        $code = I('code');
        $key = 'halobearcollege';
        $unionid = think_decrypt($code,$key);

        empty($unionid) && $this->error('二维码错误');
        $wechat_id = M('WechatAuth')->where(['unionid'=>$unionid])->getField('id');

        empty($wechat_id) && $this->error('用户不存在');

        // 课程
        $today = strtotime(date('Y-m-d'));
        $course = M('Course')->where(['start_date'=>$today, 'cate_id'=>['in', [1,2]]])->find();

        empty($course) && $this->error('暂无课程');

        $reserve = M('CourseReserve')->where(['wechat_id'=>$wechat_id, 'course_id'=>$course['id']])->field('name,phone,avatar_url,company')->find();

        empty($reserve) && $this->error('用户尚未购买课程（'. $course['title'] .'）');

        $reserve['avatar_url'] = 'http://7xopel.com2.z0.glb.qiniucdn.com/'. $reserve['avatar_url'] .'?imageView/1/w/260/q/85';
        $reserve['course'] = $course['title'];

        $this->success($reserve);
    }

}