<?php
namespace Admin\Controller;

class MemberController extends CommonController {
	/**
	 * 根据表单生成查询条件和过滤
	 * @param array $map
	 */
	protected function filter(&$map = array()) {
		!empty($_GET['username']) && $map['username'] = array('like','%'.$_GET['username'].'%');
		!empty($_GET['email']) && $map['email'] = array('like','%'.$_GET['email'].'%');
		!empty($_GET['id']) && $map['id'] = $_GET['id'];
	}
}