<?php

namespace Admin\Model;

class ConfigModel extends CommonModel {

    //自动验证
    protected $_validate = array(
        array('name', 'require', '名称不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('name', 'unique', '名称已经存在！', self::MUST_VALIDATE, 'callback', self:: MODEL_BOTH),
        array('title', 'require', '标题不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('type', 'require', '类型不能为空！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    //自动完成
    protected $_auto = array(
        array('name', 'strtoupper', self::MODEL_BOTH, 'function'),
        array('value', 'value', self::MODEL_BOTH, 'callback'),
        array('status', '1', self::MODEL_INSERT, 'string'),
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_BOTH, 'function'),
    );

    //自动完成数据
    protected function value() {
        switch ($this->_data['type']) {
            case 'date':
                $value = strtotime($this->_data['value']);
                break;
            default:
                $value = $this->_data['value'];
                break;
        }
        return $value;
    }

    //验证重复（不同模型可以属性相同）
    protected function unique() {
        $map = array('name' => $this->_data['name']);
        $pk = array($this->getPk() => $this->_data[$this->getPk()]);
        return parent::unique($map, $pk);
    }

}

?>
