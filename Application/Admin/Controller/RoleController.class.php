<?php
/**
 * 角色管理
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Controller;
class RoleController extends CommonController {
	/**
	 * 新增前置操作
	 */
	public function _before_add(){
		$list = D('Node')->getCacheList(0);
		foreach ($list AS $value){
			if($value['pid'] == 0){
				$new_list[] = $value;
			}
		}
		foreach ($new_list AS &$value){
			foreach ($list AS $l_value){
				if ($l_value['pid'] == $value['id']) {
					$value['items'][] = $l_value;
					continue;
				}
			}
		}
		$this->assign('node',$new_list);
	}
	
	/**
	 * 更新前置操作
	 */
	public function _before_edit(){
		$this->_before_add();
		
		// 权限
		$node = M('Access')->where(array('role_id'=>$_GET['id']))->getField('node_id,node_id');
		$this->access = array_keys($node);
	}
}