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
        $list = $this->where(array('status'=>1, 'month'=>$month))->order('num DESC,start_date ASC')->field('id,title,cover_url,cate_id,guest_id,city,start_date,price,total,num,day,price_model')->select();

        if (!empty($list)){
            $cate = C('KE.COURSE_CATE');
            $course_id = [];
            foreach ($list AS $key=>$value){
                $list[$key]['cover_url'] = 'http://7xopel.com2.z0.glb.qiniucdn.com/' . $value['cover_url'];
                $list[$key]['cate'] = $cate[$value['cate_id']];
                $list[$key]['last_num'] = $value['total'] - $value['num'];
                $result = $this->_getPrice($value['price'], $value['price_model']);
                $list[$key]['price'] = $value['total'] - $value['num'];
                $list[$key]['original_price'] = $value['price'];
                $list[$key]['price'] = $result['price'];
                unset($list[$key]['price_model']);

                if ($this->getStep($value['id'])){
                    $list[$key]['start_date'] = $this->_parseDate($value['start_date'], $value['day']);
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
            $guest = M('SchoolGuests')->where(array('id'=>array('in', $guest_id), 'status'=>1))->getField('id,title AS name, position');

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
        $data = $this->where(array('id'=>$id, 'status'=>1))->field('id,title,cover_url,guest_id,cate_id,city,start_date,price,total,num,place,day,content,isv_id,price_model')->find();
        if($data){
            $cate = C('KE.COURSE_CATE');
            $data['cate'] = $cate[$data['cate_id']];

            $data['month'] = date('Ym', $data['start_date']);
            if ($this->getStep($id)){
                //if ($data['day'] > 1){
                //    $end_date = $data['start_date'] + ($data['day'] - 1) * 86400;
                //    $data['start_date'] = date('m月d', $data['start_date']) . '-' . date('d日', $end_date);
                //}else{
                //    $data['start_date'] = date('Y.m.d', $data['start_date']);
                //}
                $data['start_date'] = $this->_parseDate($data['start_date'], $data['day']);
            }else{
                $data['start_date'] = date('Y.m', $data['start_date']);
            }

            $data['last_num'] = $data['total'] - $data['num'];
            // 嘉宾
            $data['guest'] = M('SchoolGuests')->where(array('id'=>$data['guest_id'], 'status'=>1))->field('title AS name, position, content')->find();
            $data['video'] = M('SchoolVideo')->where(array('cate1'=>2, 'guests_id'=>$data['guest_id']))->field('id, cover_url, title')->select();
            foreach ($data['video'] as $key=>$item) {
                $data['video'][$key]['cover_url'] = 'http://7xopel.com2.z0.glb.qiniucdn.com/' . $item['cover_url'];
            }
            $data['content'] = htmlspecialchars_decode($data['content']);
            $data['cover_url'] = 'http://7xopel.com2.z0.glb.qiniucdn.com/' . $data['cover_url'];

            // 服务商
            $data['isv_name'] = M('CourseIsv')->where(['id'=>$data['isv_id']])->getField('title');
            $result = $this->_getPrice($data['price'], $data['price_model']);
            $data['original_price'] = $data['price'];
            $data['price'] = $result['price'];
            $data['next_date'] = $result['date'];
//            $data['next_price'] = $result['next_price'];
            unset($data['price_model']);
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
     * 获取报名用户
     * @param $course_id
     * @return array|mixed
     */
    public function getSeatUser($course_id){
        $order_list = M('CourseRecord')->where(array('course_id'=>$course_id))->getField('wechat_id,course_id');
        $user_id = array_keys($order_list);
        if (!empty($user_id)){
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
        $data = $this->where(array('id'=>$course_id, 'status'=>1))->field('id,title,guest_id,city,cate_id,start_date,price,total,num,place,day,price_model')->find();
        if($data){
            if ($this->getStep($course_id)){
                //if ($data['day'] > 1){
                //    $end_date = $data['start_date'] + ($data['day'] - 1) * 86400;
                //    $data['start_date'] = date('m月d', $data['start_date']) . '-' . date('d日', $end_date);
                //}else{
                //    $data['start_date'] = date('m月d', $data['start_date']);
                //}
                $data['start_date'] = $this->_parseDate($data['start_date'], $data['day']);
            }else{
                $data['start_date'] = date('m月', $data['start_date']);
            }

            $data['last_num'] = $data['total'] - $data['num'];
            // 嘉宾
            $data['guest'] = M('SchoolGuests')->where(array('id'=>$data['guest_id'], 'status'=>1))->field('title AS name, position, content,avatar_url')->find();
            if (!empty($data['guest'])){
                $data['guest']['avatar_url'] = 'http://7xopel.com2.z0.glb.qiniucdn.com/' . $data['guest']['avatar_url'];
            }

            $result = $this->_getPrice($data['price'], $data['price_model']);
            $data['original_price'] = $data['price'];
            $data['price'] = $result['price'];
            $data['next_date'] = $result['date'];
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

    /**
     * 是否预约或报名
     * @param int $course_id
     * @param int $type
     * @return mixed
     */
    public function isReserve($course_id, $type = 0){
        $uid = session('wechat_user2.id');
        $reserve = M('CourseReserve')->where(array('wechat_id'=>$uid, 'course_id'=>$course_id, 'type'=>$type, 'status'=>1))->count();

        return $reserve;
    }

    /**
     * 是否提交报名
     * @param $course_id
     * @return mixed
     */
    public function isSubmitApply($course_id){
        $uid = session('wechat_user2.id');
        $reserve = M('CourseReserve')->where(array('wechat_id'=>$uid, 'course_id'=>$course_id, 'type'=>1))->count();

        return $reserve;
    }

    public function _parseDate($time, $day){
        if ($day > 1){
            $end_date = $time + ($day - 1) * 86400;
            $format = date('m', $time) != date('m', $end_date) ? 'm月d日' : 'd日';


            $start_date = date('m月d', $time) . '-' . date($format, $end_date);
        }else{
            $start_date = date('m月d日', $time);
        }
        return $start_date;
    }

    public function _getPrice($price, $price_model){
        $date = '';
        $next_price = 0;
        if(!empty($price_model)){
            $price_model = json_decode(html_entity_decode($price_model), 1);
            $today = strtotime(date('Y-m-d'));
            $temp = $temp2 = [];
            foreach ($price_model AS $value){
                if (strtotime($value['date']) > $today){
                    $temp[strtotime($value['date'])] = ['price'=>$value['price'], 'date'=>$value['date']];
                }else{
                    $temp2[strtotime($value['date'])] = ['price'=>$value['price'], 'date'=>$value['date']];
                }
            }

            if ($temp){
                ksort($temp);
                list($index, $date_arr) = each($temp);
                $date = $date_arr['date'];
//                $next_price = $date_arr['price'];
                $price = $date_arr['price'];
            }

//            if ($temp2){
//                krsort($temp2);
//                list($index, $price_arr) = each($temp2);
//                $price = $price_arr['price'];
//            }

        }
        return ['price'=>$price, 'date'=>$date, 'next_price'=>$next_price];
    }
}

?>