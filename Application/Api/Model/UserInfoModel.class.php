<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/20
 * Time: 17:51
 */

namespace Api\Model;

use Think\Model;
class UserInfoModel extends Model{
    /**
     * 自动验证
     * @var $_validate
     */
    protected $_validate = array(

        array('username', 'require', '用户姓名不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('sex', 'require', '性别不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_INSERT),
        array('wechat', 'require', '微信不能为空！', self::VALUE_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('province', 'require', '用户地区不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('company', 'require', '单位不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
         array('position', 'require', '职务不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_INSERT),
         array('brief', 'require', '用户简介不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_INSERT),

    );

    /**
     * 自动完成
     * @var $_auto
     */
    protected $_auto = array(
        array('create_time', 'time', Model:: MODEL_INSERT, 'function'),
        array('update_time', 'time', Model::MODEL_BOTH, 'function'),
        array('status', '1', Model::MODEL_INSERT, 'string'),
        
    );

}