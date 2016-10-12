<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/11
 * Time: 17:08
 */

namespace Admin\Model;


class CourseIsvModel extends CommonModel{
    //自动验证
    protected $_validate = array(
        array('title', 'require', '服务商名不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('service_phone', 'require', '客服电话不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
    );

    //自动完成
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_BOTH, 'function'),
        array('status', '1', self::MODEL_INSERT, 'string'),
    );

}