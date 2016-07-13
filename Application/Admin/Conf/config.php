<?php
return array(
	'SYSTEM_NAME'			=> 'FAITH内容管理系统v1.0beta',
    'URL_CASE_INSENSITIVE'  => 0,
	//模板配置
	'LAYOUT_ON'				=>	1,
	'TMPL_ACTION_ERROR'		=> 'Public:success',
	'TMPL_ACTION_SUCCESS'	=> 'Public:success',
	//列表分页数
	'LIST_ROWS'				=>	50,
	//验证码长度
	'VERIFY_CODE_LENGTH'	=>	4,
	// 配置分组
	'CONFIG_GROUP'			=> array('网站配置','功能配置','用户配置','邮件配置','其他配置'),

	// 权限设置
	'USER_AUTH_TYPE'		=>	2,		//认证类型 0:关闭认证,1:缓存认证,2:实时认证
	'USER_AUTH_KEY'			=>	'member_admin',		//认证识别号
	'NOT_AUTH_MODULE'		=>	array('Public' => '*','Index' => '*'),		//无需认证模块
	'USER_AUTH_GATEWAY'		=>	'Public/login',		//认证网关
	'SAVE_ACCESS_NAME'		=>	'sys_access', //权限保存唯一名称

	'TAGLIB_PRE_LOAD'   => 'lists,form'
);
?>