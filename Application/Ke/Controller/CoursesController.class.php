<?php
namespace Ke\Controller;


class CoursesController extends CommonController {
    public function _initialize()
    {
        $halobear = cookie('wx_auth');
//        $this->user = $this->wcache($halobear);
        $this->user = $this->getUser($halobear);
        if (!in_array(ACTION_NAME, ['index', 'detail', 'applyStatus', 'getWechat', 'getAgents'])){
            $this->_checkCode();
//            $this->_getWechatUser();
            $this->_getWechatUserByLocal();
        }
    }

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
            $data['tel'] = '4000258717';
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

        // 临时封坐
        if ((time() > strtotime('2016-10-19 20:00') && time() < strtotime('2016-10-20 10:00')) && $course_id == 12){
            $temp = explode(',', $seat_no);
            if ($temp[0] <= 10){
                $this->error('抱歉，前十排选座暂未开放');
            }
        }

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

        if (empty($this->user)){
            $this->success($course['step'] == 0 ? 1 : 3, 'ads');
        }

        if ($course['step'] == 0) {
            // 是否预约
            $model = M('CourseReserve');
            $reserve = $model->where(array('wechat_id' => $this->user['id'], 'course_id' => $course_id, 'type' => 0))->count();
            $ret = $reserve ? 2 : 1;
        } else {
            $order = M('CourseOrder')->where(array('wechat_id' => $this->user['id'], 'status' => 1, 'course_id' => $course_id))->count();
            $ret = $order ? 4 : 3;

            if ($ret == 4) {
                $count = M('CourseRecord')->where(array('wechat_id' => $this->user['id'], 'course_id' => $course_id))->count();
                $ret = $count ? 41 : 40;
            }

            if ($course['start_date'] < time()) {
                $ret = 5;
            }
        }

        $result = D('Course')->_getPrice($course['price'], $course['price_model']);
        $this->success($ret, $result['date']);
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
                    $end_date = $value['start_date'] + ($value['day'] - 1) * 86400;
                    $list[$key]['start_date'] = date('Y年m月d', $value['start_date']) . '-' . date('d日', $end_date);
                } else {
                    $list[$key]['start_date'] = date('Y年m月d', $value['start_date']);
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

        $data = array('course_id' => $course_id, 'name' => $name, 'phone' => $phone, 'wechat_id' => $this->user['id'], 'create_time' => time(), 'status' => 1, 'remark'=>'', 'is_notice'=>0);
        $model->add($data);

        $this->success([],'预约成功');
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

    // 申请代理商
    public function agentsApply(){
        $phone = I('phone');
        $code = I('code');

        if (empty($phone) || strlen($code) != 6){
            $this->error('请填写正确的手机号和验证码');
        }

        $ret = M('phone')->where(array('phone'=>I('phone'), 'code'=>$code, 'create_time'=>array('gt', time() - 7200)))->count();
        !$ret && $this->error('验证码错误或已失效');

        $agents = M('CourseAgents')->where(['phone'=>$phone])->find();
        if ($agents){
            $this->error('你已经提交申请了，请耐心等待');
        }else{
            M('CourseAgents')->add(['phone'=>$phone, 'create_time'=>time(), 'update_time'=>time(), 'code'=>uniqid()]);
            $this->success('', '提交成功，工作人员会在一个工作日内与您联系');
        }
    }

    // 获取申请代理商验证码
    public function agentsCode(){
        $to = I('phone');

        if (strlen($to) != 11){
            $this->error('请输入正确的手机号');
        }

        $data = M('phone')->where(array('phone' => $to))->find();

        $phone_code = $data['code'];
        if (!empty($phone_code) && time() - $data['create_time'] < 60) {
            $this->error('发送过于频繁，请一分钟后再试');
        }

        $code = rand(100001, 999999);
        $ret = send_msg($to, array($code), 23351, '8a48b551488d07a80148a5a1ea330a06');
        if ($ret['iRet'] == 0) {
            M('phone')->where(array('phone' => $to))->delete();
            $ret = M('phone')->add(array('phone' => $to, 'code' => $code, 'create_time' => time()));

            $this->success('短信发送成功，请注意查收！');
        } elseif ($ret['iRet'] == 160040) {
            $this->error('该号码发送过于频繁，请明天再来！');
        } else {
            $this->error('网络繁忙，请稍候再试！');
        }
    }

    // 获取代理商信息
    public function getAgents(){
        $code = cookie('agents');
        $agents = M('CourseAgents')->where(['code'=>$code])->find();
        $user = [];
        if (!empty($agents)){
            $user = M('WechatAuth')->where(['openid'=>$agents['openid']])->field('nickname, headimgurl')->find();
        }

        if(empty($user)){
            cookie('agents', null, time()-86400);
            $this->error();
        }else{
            $this->success($user);
        }
    }

    public function setLogin(){
        $auth = cookie('wx_auth');
        $this->getUser($auth);
        $this->success();
    }

    public function test(){
        write_log('test', var_export($this->user, 1));

    }
}