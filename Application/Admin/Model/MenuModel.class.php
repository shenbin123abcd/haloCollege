<?php
/**
 * 菜单模型
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Model;

class MenuModel extends CommonModel{
	//自动验证
	protected $_validate = array(
			array('name', 'require', '导航名称不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
			//array('name', 'unique', '名称不能重复！', self::MUST_VALIDATE, 'callback', self:: MODEL_BOTH),
			array('id', 'require', '标识不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
			array('id', 'unique', '标识不能重复！', self::MUST_VALIDATE, 'unique', self:: MODEL_BOTH),
			array('id', '/^\w+$/', '标识格式错误！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
	);
	
	
}

?>