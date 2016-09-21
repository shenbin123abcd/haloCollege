<?php
namespace Ke\Controller;

use Org\Util\Payment;

class CoursesController extends CommonController {
    // 课程列表
    public function index() {
        $month = intval(I('month'));
        $month = empty($month) ? date('Ym') : $month;

        $list = D('Course')->getList($month);

        $this->success($list);
    }

    // 课程详情
    public function detail($id) {
        $id = intval($id);

        $data = D('Course')->detail($id);

        if (empty($data)) {
            $this->error('课程不存在');
        } else {
            $data['tel'] = '';
            $this->success($data);
        }
    }

    // 座位表请求
    public function seatInfo($course_id) {
        $course_id = intval($course_id);

        empty($course_id) && $this->error('课程编号错误');

        $model = D('Course');
        $seat = $model->getSeat($course_id);
        $user = $model->getSeatUser($course_id);
        $course = $model->getInfo($course_id);

        $this->success(['seat' => $seat, 'user' => $user, 'course' => $course]);
    }

    // 选座页
    public function seat($course_id) {
        $course_id = intval($course_id);

        empty($course_id) && $this->error('课程编号错误');

        $model = D('Course');
        $seat = $model->getSeat($course_id);
        $course = $model->getInfo($course_id);

        $this->success(['seat' => $seat, 'course' => $course]);
    }

    // 选座
    public function selectSeat() {
        $course_id = intval(I('course_id'));
        $seat_no = I('seat_no');

        empty($course_id) && $this->error('课程编号错误');
        empty($seat_no) && $this->error('座位号号错误');

        // 检查用户是否报名
        $apply = M('CourseOrder')->where(array('course_id' => $course_id, 'wechat_id' => $this->user['id'], 'status' => 1))->count();
        empty($apply) && $this->error('抱歉，你还没有报名');

        // 检查是否选座
        $select = M('CourseRecord')->where(array('wechat_id' => $this->user['id'], 'course_id' => $course_id))->count();
        $select && $this->error('你已经选过坐了');

        // 检查座位是否已选
        $select = M('CourseRecord')->where(array('seat_no' => $seat_no, 'course_id' => $course_id))->count();
        $select && $this->error('该座位已经被选了');

        // 增加选座记录
        $data = array('uid' => 0, 'wechat_id' => $this->user['id'], 'course_id' => $course_id, 'seat_no' => $seat_no, 'create_time' => time());
        $ret = M('CourseRecord')->add($data);

        $ret ? $this->success('选座成功') : $this->error('选座失败');
    }

    // 报名状态
    public function applyStatus() {
        $course_id = intval(I('course_id'));

        $course = M('Course')->where(array('id' => $course_id, 'status' => 1))->find();
        empty($course) && $this->error('课程编号错误');

        if ($course['step'] == 0) {
            // 是否预约
            $model = M('CourseReserve');
            $reserve = $model->where(array('wechat_id' => $this->user['id'], 'course_id' => $course_id, 'type' => 0))->count();
            $ret = $reserve ? 2 : 1;
        } else {
            $order = M('CourseOrder')->where(array('wechat_id' => $this->user['id'], 'status' => 1, 'course_id' => $course_id))->count();
            $ret = $order ? 4 : 3;

            if ($ret == 4) {
                $count = M('CourseRecord')->where(array('wechat_id' => $this->user['id'], 'course_id' => $course_id))->getField('');
                $ret = $count ? 41 : 40;
            }

            if ($course['start_date'] < time()) {
                $ret = 5;
            }
        }

        $this->success($ret);
    }

    public function mySeat() {
        $course_id = intval(I('course_id'));

        $course = M('Course')->where(array('id' => $course_id, 'status' => 1))->find();
        empty($course) && $this->error('课程编号错误');
        $seat_no = M('CourseRecord')->where(array('wechat_id' => $this->user['id'], 'course_id' => $course_id))->getField('seat_no');

        $this->success($seat_no ? $seat_no : '');
    }

    // 我的课程
    public function my() {
        $course_id = M('CourseOrder')->where(array('wechat_id' => $this->user['id'], 'status' => 1))->getField('id,course_id');

        $list = array();
        if ($course_id) {
            // 选座
            $record = M('CourseRecord')->where(array('wechat_id' => $this->user['id'], 'course_id' => array('in', $course_id)))->getField('course_id, seat_no');

            // 课程
            $list = M('Course')->where(array('id' => array('in', $course_id)))->field('id,guest_id,title,start_date,place,day,cate_id')->select();

            // 嘉宾
            $guests_id = [];
            foreach ($list as $item) {
                $guests_id[] = $item['guest_id'];
            }
            $guests = M('SchoolGuests')->where(array('id' => array('in', $guests_id), 'status' => 1))->getField('id, CONCAT("http://7xopel.com2.z0.glb.qiniucdn.com/",avatar_url) AS avatar');
            $cate = C('KE.COURSE_CATE');
            foreach ($list AS $key => $value) {
                $list[$key]['cate'] = $cate[$value['cate_id']];
                $list[$key]['avatar_url'] = isset($guests[$value['guest_id']]) ? $guests[$value['guest_id']]['avatar'] : '';
                $list[$key]['seat_no'] = $record[$value['id']];
                $list[$key]['start_day'] = $value['start_date'] > time() ? ceil(($value['start_date'] - time()) / 86400) : 0;

                if ($value['day'] > 1) {
                    $end_date = $value['start_date'] + $value['day'] * 86400;
                    $list[$key]['start_date'] = date('m月d', $value['start_date']) . '-' . date('d日', $end_date);
                } else {
                    $list[$key]['start_date'] = date('m月d', $value['start_date']);
                }
            }
        }

        $this->success(['list' => $list, 'user' => ['username' => $this->user['nickname'], 'avatar' => $this->user['headimgurl']]]);
    }

    // 预约课程
    public function reserve() {
        $course_id = intval(I('course_id'));
        $phone = trim(I('phone'));
        $name = trim(I('name'));

        !is_mobile($phone) && $this->error('手机号格式错误');
        empty($name) && $this->error('请填写称呼');

        $course = M('Course')->where(array('id' => $course_id))->count();
        empty($course) && $this->error('课程不存在');

        // 是否预约
        $model = M('CourseReserve');
        $reserve = $model->where(array('wechat_id' => $this->user['id'], 'course_id' => $course_id, 'type' => 0))->count();
        $reserve && $this->error('你已经预约过该课程了');

        $data = array('course_id' => $course_id, 'name' => $name, 'phone' => $phone, 'wechat_id' => $this->user['id'], 'create_time' => time(), 'status' => 1);
        $model->add($data);
    }

    // 提交报名信息
    public function apply() {
        $data = ['name' => trim(I('name')), 'phone' => trim(I('phone')), 'company' => trim(I('company')), 'type' => 1, 'wechat_id' => $this->user['id'], 'uid' => 0, 'course_id' => intval(I('course_id')), 'create_time' => time(), 'status' => 0];

        if (empty($data['name']) || empty($data['phone']) || empty($data['company'])) {
            $this->error('请将数据填写完整');
        }elseif (!is_mobile($data['phone'])){
            $this->error('手机号格式错误');
        }

        $course = M('Course')->where(array('id' => $data['course_id'], 'status' => 1))->find();
        empty($course) && $this->error('课程编号错误');

        // 是否报名
        $model = D('Course');
        if ($model->isReserve($data['course_id'], 1)) {
            $this->error('你已经报过名了');
        }

        // 是否提交报名
        if ($model->isSubmitApply($data['course_id'])) {
            M('CourseReserve')->where(array('wechat_id' => $this->user['id'], 'course_id' => $data['course_id'], 'type' => 1))->save($data);
        } else {
            M('CourseReserve')->add($data);
        }

        $this->success('提交成功');
    }
}