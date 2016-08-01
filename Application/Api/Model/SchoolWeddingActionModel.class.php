<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 14:58
 */

namespace Api\Model;


use Think\Model;

class SchoolWeddingActionModel extends Model{
    //自动验证
    protected $_validate = array(
        array('headline','require','活动主题不能为空！',self::MUST_VALIDATE,'regex',self::MODEL_BOTH),
        array('visitor_count','require','参加人数不能为空！',self::MUST_VALIDATE,'regex',self::MODEL_BOTH),
        array('address','require','活动地点不能为空！',self::MUST_VALIDATE,'regex',self::MODEL_BOTH),
        array('brief','require','活动简介不能为空！',self::MUST_VALIDATE,'regex',self::MODEL_BOTH),
        array('conection','require','联系方式不能为空！',self::MUST_VALIDATE,'regex',self::MODEL_BOTH),
        array('start_time','require','活动开始时间不能为空！',self::MUST_VALIDATE,'regex',self::MODEL_BOTH),
        array('end_time','require','活动结束时间不能为空！',self::MUST_VALIDATE,'regex',self::MODEL_BOTH),
    );

    // 自动完成
    protected $_auto = array(
        array('create_time','time',self::MODEL_INSERT,'function'),
        array('update_time','time',self::MODEL_BOTH,'function'),
        array('status','1',self::MODEL_INSERT,'string'),
    );

}