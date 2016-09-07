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
    public function detail($id){
        $id = intval($id);

        $data = D('Course')->detail($id);

        empty($data) ? $this->error('课程不存在') : $this->success($data);
    }

    // 座位表请求
    public function seatInfo($course_id){
        $course_id = intval($course_id);

        empty($course_id) && $this->error('课程编号错误');

        $model = D('Course');
        $seat = $model->getSeat($course_id);
        $user = $model->getUser($course_id);

        $this->success(['seat'=>$seat, 'user'=>$user]);
    }

    // 选座页
    public function seat($course_id){
        $course_id = intval($course_id);

        empty($course_id) && $this->error('课程编号错误');

        $model = D('Course');
        $seat = $model->getSeat($course_id);
        $course = $model->getInfo($course_id);

        $this->success(['seat'=>$seat, 'course'=>$course]);
    }

    // 选座
    public function selectSeat(){
        $course_id = intval(I('course_id'));
        $seat_no = I('seat_no');

        empty($course_id) && $this->error('课程编号错误');
        empty($seat_no) && $this->error('座位号号错误');

        // 检查用户是否报名
        $apply = M('CourseOrder')->where(array('course_id'=>$course_id, 'wechat_id'=>$this->user['id'], 'status'=>1))->count();
        empty($apply) && $this->error('抱歉，你还没有报名');

        // 检查是否选座
        $select = M('CourseRecord')->where(array('wechat_id'=>$this->user['id'], 'course_id'=>$course_id))->count();
        $select && $this->error('你已经选过坐了');

        // 检查座位是否已选
        $select = M('CourseRecord')->where(array('seat_no'=>$seat_no, 'course_id'=>$course_id))->count();
        $select && $this->error('该座位已经被选了');

        // 增加选座记录
        $data = array('uid'=>0, 'wechat_id'=>$this->user['id'], 'course_id'=>$course_id,'seat_no'=>$seat_no,'create_time'=>time());
        $ret = M('CourseRecord')->add($data);

        $ret ? $this->success('选座成功') : $this->error('选座失败');
    }



    public function test(){
        vendor('Pay.Payment');

        $pay = new Payment('wechat');
        $pay->setNotify('http://ke.halobear.com/course/notify');

        echo $sign = $pay->sign(['subject'=>'test', 'body'=>'body', 'order_no'=>'121111', 'amount'=>'0.01']);

        //$this->success($sign);
    }
}