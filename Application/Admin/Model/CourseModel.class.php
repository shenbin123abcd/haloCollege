<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/11
 * Time: 17:08
 */

namespace Admin\Model;


class CourseModel extends CommonModel{
    //自动验证
    protected $_validate = array(
        array('title', 'require', '标题不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('cover_url', 'require', '请上传封面图！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('guest', 'checkGuest', '嘉宾不存在！', self::MUST_VALIDATE, 'callback', self:: MODEL_BOTH),
        array('cate_id', 'require', '请选择分类！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('city', 'require', '请填写城市！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('price', 'require', '请填写价格！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('place', 'require', '请填写场地！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('start_date', 'require', '请选择上课日期！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('content', 'require', '请填写内容！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
    );

    //自动完成
    protected $_auto = array(
        array('start_date', 'strtotime', self::MODEL_BOTH, 'function'),
        array('guest_id', 'getGuest', self::MODEL_BOTH, 'callback'),
        array('total', 'getTotal', self::MODEL_BOTH, 'callback'),
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_BOTH, 'function'),
        array('status', '1', self::MODEL_INSERT, 'string'),
        array('num', '0', self::MODEL_INSERT, 'string'),
    );

    // 检查嘉宾是否正确
    protected function checkGuest(){
        $_POST['guests_id'] = M('SchoolGuests')->where(array('title'=>$_POST['guests']))->getField('id');
        return empty($_POST['guests_id']) ? false :true;
    }

    protected function getGuest(){
        return $_POST['guests_id'];
    }

    protected function getTotal(){
        return $_POST['room_rows'] * $_POST['room_cols'];
    }

}