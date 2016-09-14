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

        empty($data) ? $this->error('课程不存在') : $this->success($data);
    }

    // 座位表请求
    public function seatInfo($course_id) {
        $course_id = intval($course_id);

        empty($course_id) && $this->error('课程编号错误');

        $model = D('Course');
        $seat = $model->getSeat($course_id);
        $user = $model->getUser($course_id);
        $course = $model->getInfo($course_id);

        $this->success(['seat' => $seat, 'user' => $user, 'course'=>$course]);
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
    public function applyStatus(){
        $course_id = intval(I('course_id'));

        $course = M('Course')->where(array('id'=>$course_id, 'status'=>1))->find();
        empty($course) && $this->error('课程编号错误');

        if ($course['step'] == 0){
            // 是否预约
            $model = M('CourseReserve');
            $reserve = $model->where(array('wechat_id'=>$this->user['id'], 'course_id'=>$course_id))->count();
            $ret = $reserve ? 2 : 1;
        }else{
            $order = M('CourseOrder')->where(array('wechat_id' => $this->user['id'], 'status' => 1, 'course_id'=>$course_id))->count();
            $ret = $order ? 4 : 3;

            if ($course['start_date'] < time()){
                $ret = 5;
            }
        }

        $this->success($ret);
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
            $guests = M('SchoolGuests')->where(array('id' => array('in', $guests_id)))->getField('id, CONCAT("http://7xopel.com2.z0.glb.qiniucdn.com/",avatar_url) AS avatar');
            $cate = C('KE.COURSE_CATE');
            foreach ($list AS $key => $value) {
                $list[$key]['cate'] = $cate[$value['cate_id']];
                $list[$key]['avatar_url'] = $guests[$value['guest_id']]['avatar'];
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

        $this->success(['list'=>$list, 'user'=>['username'=>$this->user['nickname'], 'avatar'=>$this->user['headimgurl']]]);
    }

    // 预约课程
    public function reserve(){
        $course_id = intval(I('course_id'));
        $phone = trim(I('phone'));
        $name = trim(I('name'));

        !is_mobile($phone) && $this->error('手机号格式错误');
        empty($name) && $this->error('请填写称呼');

        $course = M('Course')->where(array('id'=>$course_id))->count();
        empty($course) && $this->error('课程不存在');

        // 是否预约
        $model = M('CourseReserve');
        $reserve = $model->where(array('wechat_id'=>$this->user['id'], 'course_id'=>$course_id))->count();
        $reserve && $this->error('你已经预约过该课程了');

        $data = array('course_id'=>$course_id,'name'=>$name,'phone'=>$phone, 'wechat_id'=>$this->user['id'], 'create_time'=>time());
        $model->add($data);
    }


    public function test() {
        vendor('Pay.Payment');

        $pay = new Payment('alipay');
        $pay->setNotify('http://ke.halobear.com/course/notify');

        $sign = $pay->sign(['subject' => 'test12', 'body' => '测试', 'order_no' => '121111', 'price' => '0.02']);

        //$data = 'service="mobile.securitypay.pay"&_input_charset="utf-8"&notify_url="https%3A%2F%2Fapi.pingxx.com%2Fnotify%2Fcharges%2Fch_m5OyjLyLaznDyfn9WH90e5K0"&partner="2088611020356950"&out_trade_no="20160913221240344628"&subject="u3010u5e7bu718au5546u57ceu3011030u6d6au6f2bu795eu5723uff5cu5723u5149"&body="u3010u5e7bu718au5546u57ceu3011030u6d6au6f2bu795eu5723uff5cu5723u5149"&total_fee="499.00"&payment_type="1"&seller_id="2088611020356950"&it_b_pay="2016-09-14 22:12:44"&sign="sOfdpMLLjDqAqTK4%2F1gRkJCyxVNO4ceHJI6eKPWvm2LWdE0I5plE010Tc88ulD0myUugA%2B56t%2BbHwP%2BX2upWlCouPu0HuGYvB0Y1nDLXWSItIKgR26jTLQdII0zX7C6cJOO0%2B%2FcPtODp5s4CZN96fz%2BkPVaB4B3YGF%2BLMRpsJF0%3D"&sign_type="RSA"';

        $this->success($sign['data']);//$sign['data']
    }
}