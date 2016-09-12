<?php
/**
 * 角色模型
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Model;

class RoleModel extends CommonModel{
	/**
	 * 自动验证
	 * @var $_validate
	 */
	protected $_validate = array(
			array('name', 'require', '角色名称不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
			array('name', '', '该角色名称已经存在！', self::MUST_VALIDATE, 'unique', self:: MODEL_BOTH),
	);
	
	/**
	 * 自动完成
	 * @var $_auto
	 */
	protected $_auto = array(
			array('status', '1', self::MODEL_INSERT, 'string'),
	);
	
	/**
	 * 保存权限
	 * @see Model::_after_insert()
	 */
	protected function _after_insert($data){
		$node_id = $_POST['auths']; //implode(',', $this->_data['auths']);
		if(empty($node_id)){
			return ;
		}
		
		foreach ($node_id AS $value){
			$dataList[] = array('role_id'=>$data['id'],'node_id'=>$value,'module'=>'');
		}
		M('Access')->addAll($dataList);
	}
	
	/**
	 * 更新权限
	 * @see Model::_after_insert()
	 */
	protected function _after_update($data){
		// 删除
		M('Access')->where(array('role_id'=>$data['id']))->delete();
		// 更新
		$this->_after_insert($data);
	}
}

?>