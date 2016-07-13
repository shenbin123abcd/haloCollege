<?php
namespace Admin\Model;

class RbacModel {


	//默认配置
	private $config=array(
		'userAuthKey'   =>0,//会员编号
		'adminAuthKey'  =>0,//管理员编号（管理员无需认证）
		'userAuthType'  =>1,//认证类型 0:关闭认证,1:缓存认证,2:实时认证
		'saveAccessName'=>'save_access',//权限缓存名称（权限保存会话名称）
		'notAuthModule' =>array(),//无需验证操作，例array('Public'=>'*','Index'=>'index,main')
	);

	//获取配置
	public function __get($name){
		if(isset($this->config[$name])){
			return $this->config[$name];
		}
		return null;
	}

	//设置配置
	public function __set($name,$value){
		if(isset($this->config[$name])){
			$this->config[$name]=$value;
		}
	}

	//验证配置
	public function __isset($name){
		return isset($this->config[$name]);
	}

	/**
	 * 权限认证的过滤器方法
	 * @param string $app 项目名称
	 * @param string $module 模型名称
	 * @param string $action 操作名称
	 * @param bool 认证是否成功
	*/
	public function accessDecision($app, $module, $action) {
		//操作不区分大小写
		$action=strtolower($action);
		$access_name = $this->saveAccessName;
		//检测是否需要认证
		if($this->checkAccess($module,$action)){
			//没有登录认证失败，销毁权限缓存
			if(empty($this->userAuthKey)){
				session($access_name,null);
				return false;
			}else{
				//读取缓存权限
				if($this->userAuthType == 1){
					$access=session($access_name);
				}else{
					//获取权限列表
					$access=$this->getAccessList($app);
					//是否缓存权限
					$this->userAuthType == 1 && session($access_name,$access);
					//是否删除缓存权限
					$this->userAuthType == 2 && session($access_name,null);
				}
				//dump($access);exit;
				//权限检测
				return isset($access[$module]) && in_array($action,$access[$module]) ? true : false;
			}
		}
		return true;
	}

	/**
	 * 检查当前操作是否需要认证
	 * @param string $module 模型名称
	 * @param string $action 操作名称
	 * @param bool 是否需要认证
	*/
	private function checkAccess($module, $action) {
		//管理员无需认证
		if($this->userAuthType == 0){
			return false;
		}else if(!empty($this->adminAuthKey) && in_array($this->userAuthKey,$this->adminAuthKey)){
			return false;
		}else if(!empty($this->notAuthModule[$module])){
			$auth = strtolower($this->notAuthModule[$module]);
			//转换成权限数组
			$auth = (!is_array($auth) && $auth != '*') ? explode(',',$auth):$auth;
			//是否需要认证
			return ($auth == '*' || in_array($action,$auth)) ? false : true;
		}
		return true;
	}

	/**
	 * 获取用户访问权限
	 * @param string $app 项目名称
	 * @return array 权限列表
	*/
	public function getAccessList($app) {
		$node=M('Node')->where(array('status'=>1))->getField('id,pid,name');
		$role=M('Role')->where(array('status'=>1))->getField('id,name');
		$role_id=M('RoleUser')->where(array('user_id'=>$this->userAuthKey,'role_id'=>array('in',array_keys($role))))->getField('role_id');
		$access=M('Access')->where(array('role_id'=>array('in',$role_id)))->group('node_id')->select();
		
		//组合权限信息
		foreach($access as $value){
			$action=$node[$value['node_id']];
			$module=$node[$action['pid']];
			//$group=$node[$module['pid']];
			if(!empty($action)&&!empty($module)){
				$result[$module['name']][]=strtolower($action['name']);
			}
		}
		return $result;
	}

}

?>
