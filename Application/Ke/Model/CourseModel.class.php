<?php
/**
 * 分类模型
 * @author wtwei
 * @version $Id$
 */
namespace Ke\Model;
use Think\Model;

class CourseModel extends Model {
    /**
     * 获取列表
     * @param $month
     * @return array
     */
    public function getList($month){
        $list = $this->where(array('status'=>1, 'month'=>$month))->order('id asc')->field('id,title,cover_url,cate_id,guest_id,city,start_date,price,total,num')->select();

        if (!empty($list)){
            $cate = C('KE.COURSE_CATE');
            $course_id = [];
            foreach ($list AS $key=>$value){
                $list[$key]['cover_url'] = 'http://7xopel.com2.z0.glb.qiniucdn.com/' . $value['cover_url'];
                $list[$key]['cate'] = $cate[$value['cate_id']];
                $list[$key]['last_num'] = $value['total'] - $value['num'];

                if ($this->getStep($value['id'])){
                    $list[$key]['start_date'] = date('m月d日', $value['start_date']);
                }else{
                    $list[$key]['start_date'] = date('m月', $value['start_date']);
                }

                $list[$key]['user'] = array();

                $course_id[] = $value['id'];
                $guest_id[] = $value['guest_id'];
            }

            // 课程报名用户
            $order_list = M('CourseOrder')->where(array('status'=>1, 'course_id'=>array('in', $course_id)))->field('wechat_id,course_id')->select();
            $user_id = $course_record = [];
            foreach ($order_list as $item) {
                $course_record[$item['course_id']][] = ['id'=>$item['wechat_id'], 'avatar'=>''];
                $user_id[] = $item['wechat_id'];
            }

            if ($user_id){
                $user = M('WechatAuth')->where(array('id'=>array('in', $user_id)))->getField('id,headimgurl');
                foreach ($course_record as $key=>$item) {
                    foreach ($item as $key2=>$value) {
                        $course_record[$key][$key2] = $user[$value['id']];
                    }
                }

                foreach ($list as $key => $value) {
                    $list[$key]['user'] = $course_record[$value['id']];
                }
            }

            // 嘉宾
            $guest = M('SchoolGuests')->where(array('id'=>array('in', $guest_id)))->getField('id,title AS name, position');

            foreach ($list as $key => $value) {
                $list[$key]['guest'] = $guest[$value['guest_id']];
            }
        }else {
            $list = [];
        }

        return $list;
    }

    /**
     * 获取详情
     * @param $id
     * @return array|mixed
     */
	public function detail($id){
        $data = $this->where(array('id'=>$id, 'status'=>1))->field('id,title,cover_url,guest_id,cate_id,city,start_date,price,total,num,place,day,content')->find();
        if($data){
            $cate = C('KE.COURSE_CATE');
            $data['cate'] = $cate[$data['cate_id']];

            if ($this->getStep($id)){
                $data['start_date'] = date('Y.m.d', $data['start_date']);
            }else{
                $data['start_date'] = date('Y.m', $data['start_date']);
            }

            $data['last_num'] = $data['total'] - $data['num'];
            // 嘉宾
            $data['guest'] = M('SchoolGuests')->where(array('id'=>$data['guest_id']))->field('title AS name, position, content')->find();
            $data['video'] = M('SchoolVideo')->where(array('cate1'=>2, 'guests_id'=>$data['guest_id']))->field('id, cover_url, title')->select();
            foreach ($data['video'] as $key=>$item) {
                $data['video'][$key]['cover_url'] = 'http://7xopel.com2.z0.glb.qiniucdn.com/' . $item['cover_url'];
            }
            $data['content'] = htmlspecialchars_decode($data['content']);
            $data['cover_url'] = 'http://7xopel.com2.z0.glb.qiniucdn.com/' . $data['cover_url'];
        }

        return $data ? $data : array();
    }

    /**
     * 获取报名用户
     * @param $course_id
     * @return array|mixed
     */
    public function getUser($course_id){
        $order_list = M('CourseOrder')->where(array('status'=>1, 'course_id'=>$course_id))->field('wechat_id,course_id')->select();
        $user = [];
        if (!empty($order_list)){
            $user_id = [];
            foreach ($order_list as $item) {
                $user_id[] = $item['wechat_id'];
            }
            $user = M('WechatAuth')->where(array('id'=>array('in', $user_id)))->getField('id,headimgurl');
            $user = array_values($user);
        }
        return $user;
    }

    /**
     * 获取座位列表
     * @param  integer $course_id 课程编号
     * @return array         座位列表
     */
    public function getSeat($course_id){
        // 课程信息
        $course = M('Course')->where(array('id'=>$course_id))->find();
        if (empty($course)){
            return [];
        }

        $rows = $course['room_rows'];
        $cols = $course['room_cols'];

        $record = M('CourseRecord')->where(array('course_id'=>$course_id))->getField('seat_no,wechat_id');

        $list = array();
        for ($i=1; $i <= $rows; $i++) {
            for ($j=1; $j <= $cols; $j++) {
                $seat = $i . ',' . $j;
                $user = isset($record[$seat]) ? $record[$seat] : 0;
                $list[$i][] = array('seat_no'=>$seat, 'user'=>$user);
            }
        }

        return array_values($list);
    }

    /**
     * 获取课程信息
     * @param $course_id
     * @return array|mixed
     */
    public function getInfo($course_id){
        $data = $this->where(array('id'=>$course_id, 'status'=>1))->field('id,title,guest_id,city,start_date,price,total,num,place,day')->find();
        if($data){
            if ($this->getStep($course_id)){
                if ($data['day'] > 1){
                    $end_date = $data['start_date'] + $data['day'] * 86400;
                    $data['start_date'] = date('m月d', $data['start_date']) . '-' . date('d日', $end_date);
                }else{
                    $data['start_date'] = date('m月d', $data['start_date']);
                }
            }else{
                $data['start_date'] = date('m月', $data['start_date']);
            }


            $data['last_num'] = $data['total'] - $data['num'];
            // 嘉宾
            $data['guest'] = M('SchoolGuests')->where(array('id'=>$data['guest_id']))->field('title AS name, position, content,avatar_url')->find();
            $data['guest']['avatar_url'] = 'http://7xopel.com2.z0.glb.qiniucdn.com/' . $data['guest']['avatar_url'];
        }

        return $data ? $data : array();
    }

    /**
     * 获取课程阶段 0预约 1报名
     * @param $course_id
     * @return mixed
     */
    public function getStep($course_id){
        return $this->where(array('id'=>$course_id))->getField('step');
    }
}

?>