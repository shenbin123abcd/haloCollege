<?php
/**
 * 分类模型
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Model;
class CategoryModel extends CommonModel{
	/**
	 * 自动验证
	 * @var array
	 */
	protected $_validate = array(
			array('title', 'require', '分类名称不能为空！', self::VALUE_VALIDATE, 'regex', self:: MODEL_BOTH),
			array('name', '', '标识不能重复！', self::VALUE_VALIDATE, 'unique', self:: MODEL_BOTH),
			array('name', '/^[a-zA-Z]+$/', '标识格式错误！', self::VALUE_VALIDATE, 'regex', self:: MODEL_BOTH),
			array('name', 'require', '标识不能为空！', self::VALUE_VALIDATE, 'regex', self:: MODEL_BOTH),
	);
	
	/**
	 * 自动完成
	 * @var $_auto
	 */
	protected $_auto = array(
			array('name', 'strtolower', self::MODEL_BOTH, 'function'),
			array('status', '1', self::MODEL_INSERT, 'string'),
	);
}

?>