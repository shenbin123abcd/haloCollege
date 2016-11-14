<?php
/**
 * 用户管理
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Controller;
class SchoolAccountController extends CommonController {
	/**
	 * 根据表单生成查询条件和过滤
	 * @param array $map
	 */
	protected function filter(&$map = array()) {
		!empty($_GET['username']) && $map['username'] = array('like','%'.$_GET['username'].'%');
		!empty($_GET['phone']) && $map['phone'] = array('like','%'.$_GET['phone'].'%');
		!empty($_GET['code']) && $map['code'] = array('like','%'.$_GET['code'].'%');
		!empty($_GET['id']) && $map['id'] = $_GET['id'];
		!empty($_GET['login_days']) && $map = $this->map_login_days($map);
	}

	/**
	 * 查询已经登录学院一段时期的用户
	*/
	public function map_login_days($map){
		$days = $_GET['login_days'];
		$times = $days*24*60*60;
		$deadline_time = time()-$times;
		$map['last_time'] = array(array('lt',$deadline_time),array('gt',0));

		return $map;
	}

	/**
	 * 群发短信
	*/
	public function sendMsg(){
		$ids = $_REQUEST['id'];
		empty($ids) && $this->error('请勾选要接收短信的用户！');
		$phones = M('SchoolAccount')->where(array('id'=>array('in',$ids)))->getField('id,phone');
		$content = 'hello';
		foreach ($phones AS $key=>$value){
			$to = $value;
			$ret = send_msg($to, array($content), 23351, '8a48b551488d07a80148a5a1ea330a06');
			
		}


	}




	
}