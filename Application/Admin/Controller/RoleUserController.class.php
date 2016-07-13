<?php
/**
 * 后台用户管理
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Controller;

class RoleUserController extends CommonController {
	/**
	 * 列表
	 * @see CommonAction::index()
	 */
	public function index(){
		cookie('__forward__', $_SERVER ['REQUEST_URI']);
		$member = M('RoleUser')->join(C('DB_PREFIX').'member AS m ON m.id='.C('DB_PREFIX').'role_user.user_id')->select();
		$role = M('Role')->getField('id,name');
		foreach ($member AS $key=>$value){
			$role_id = explode(',', $value['role_id']);
			foreach ($role_id AS $r_value){
				$member[$key]['role'] .= $role[$r_value].',';
			}
			$member[$key]['role'] = trim($member[$key]['role'],',');
		}
		$this->assign('list',$member);
		$this->display();
	}
	
	/**
	 * 新增前置操作
	 * @return array 角色
	 */
	public function _before_add(){
		// 获取角色
		$this->roles = M('Role')->where(array('status'=>1))->select();
	}
	
	/**
	 * 新增前置操作
	 * @return array 角色
	 */
	public function edit(){
		$model = $this->model();
		$data = $model->where(array('user_id'=>$_GET['id']))->find();
		empty($data) && $this->error('查询数据失败！');

		// 角色
		$this->_before_add();

		// 用户拥有的角色
		$this->user_role = M('Role')->where(array('id'=>array('in',$data['role_id'])))->select();
		$this->username = M('Member')->where(array('id'=>$data['user_id']))->getField('username');

		$this->assign('data',$data);
		$this->display();
	}

}