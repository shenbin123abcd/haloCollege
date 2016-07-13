<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/11
 * Time: 14:58
 */

namespace Admin\Model;


class FounderModel extends CommonModel{
    /**
     * 自动验证
     * @var $_validate
     */
    protected $_validate = array(
        array('username', '', '该用户已经被添加为创始人！', self::MUST_VALIDATE, 'unique', self:: MODEL_INSERT),
        array('username', 'checkUser', '该用户是非法用户或者该用户不存在', self::MUST_VALIDATE, 'callback', self:: MODEL_INSERT),
    );

    /**
     * 自动完成
     * @var $_auto
     */
    protected $_auto = array(
        array('uid', 'getUid', self:: MODEL_INSERT, 'callback'),
    );

    /**
     * 检查用户名
     * @return boolean
     */
    protected function checkUser(){
        if($this->uid = M('Member')->where(array('username'=>$_POST['username'],'status'=>1))->getField('id')){
            return true;
        }
        return false;
    }

    /**
     * 获取用户ID
     */
    protected function getUid(){
        return $this->uid;
    }

    /**
     * 获取创始人ID
     * @return arrry 创始人ID
     */
    public function getFounder(){
        $result = $this->getField('uid,username');
        return array_keys($result);
    }


}