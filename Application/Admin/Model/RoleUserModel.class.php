<?php
/**
 * 后台用户管理
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Model;

class RoleUserModel extends CommonModel{
	/**
	 * 自动验证
	 * @var $_validate
	 */
	protected $_validate = array(
			array('username', 'require', '用户名不能为空', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
			array('username', 'checkUser', '该用户是非法用户或者该用户不存在', self::MUST_VALIDATE, 'callback', self:: MODEL_BOTH),
			array('username', 'userUnique', '该后台用户已经存在！', self::MUST_VALIDATE, 'callback', self:: MODEL_INSERT),
			array('role_id', 'checkRole', '角色不能为空', self::MUST_VALIDATE, 'callback', self:: MODEL_BOTH),
	);
	
	/**
	 * 自动完成
	 * @var $_auto
	 */
	protected $_auto = array(
			array('user_id', 'getUid', self:: MODEL_INSERT, 'callback'),
			array('role_id', 'getRole', self:: MODEL_BOTH, 'callback'),
	);
	
	/**
	 * 用户唯一
	 * @return boolean
	 */
	protected function userUnique(){
		if($this->where(array('user_id'=>$this->uid))->count()){
			return false;
		}
		return true;
	}
	
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
	 * 检查角色
	 * @return boolean
	 */
	protected function checkRole(){
		if(empty($_POST['role_id'])){
			return false;
		}
		return true;
	}
	
	/**
	 * 获取用户ID
	 */
	protected function getUid(){
		return $this->uid;
	}
	
	/**
	 * 获取用户角色
	 */
	protected function getRole(){
		return implode(',', $this->_data['role_id']);
	}
}

?>