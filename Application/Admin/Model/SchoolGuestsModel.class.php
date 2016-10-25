<?php
namespace Admin\Model;
class SchoolGuestsModel extends CommonModel {

	//自动验证
	protected $_validate = array(
		array('title', '', '该嘉宾已存在！', self::MUST_VALIDATE, 'unique', self:: MODEL_BOTH),
		array('title', 'require', '嘉宾姓名不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
		array('position', 'require', '嘉宾职位不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
		//array('company', 'require', '嘉宾所在公司不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
		//array('company_id', 'require', '嘉宾所在公司不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
		array('content', 'require', '对嘉宾的描述不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
		// array('module', 'require', '模型名称不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_UPDATE),
		// array('record_id', 'require', '记录编号不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_UPDATE),
	);

	//自动完成
	protected $_auto = array(
		array('create_time', 'time', self::MODEL_INSERT, 'function'),
		array('update_time', 'time', self::MODEL_BOTH, 'function'),
		array('status', '1', self::MODEL_INSERT, 'string'),
	);
	

}

?>
