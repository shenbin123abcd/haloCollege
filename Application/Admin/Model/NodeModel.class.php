<?php
namespace Admin\Model;

// 节点模型
class NodeModel extends CommonModel{
	//自动验证
	protected $_validate = array(
			array('title', 'require', '节点名称不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
			array('name', '/^\w+$/', '节点格式错误！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
			array('name', 'unique', '节点不能重复！', self::MUST_VALIDATE, 'callback', self:: MODEL_BOTH),
	);
	
	/**
	 * 验证重复
	 * @see CommonModel::unique()
	 */
	protected function unique() {
		$map = array('name' => $this->_data['name'], 'pid' => $this->_data['pid']);
		$pk = array($this->getPk() => $this->_data[$this->getPk()]);
		return parent::unique($map, $pk);
	}
}

?>