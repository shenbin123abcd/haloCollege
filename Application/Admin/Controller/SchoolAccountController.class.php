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
	}

	
}