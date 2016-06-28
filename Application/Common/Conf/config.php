<?php
return array(
	//URL模式
	'URL_MODEL' => 2,	
	'URL_CASE_INSENSITIVE'	=>	1,
	'TMPL_TEMPLATE_SUFFIX'  => '.html',
	'URL_HTML_SUFFIX' => 'html|json',
	'APP_GROUP_MODE'	=>	1,
	'MODULE_ALLOW_LIST' =>    array('Home','Api'),
	'DEFAULT_MODULE'    =>    'Home',

	//数据库配置
    'DB_TYPE' => 'mysqli',
    'DB_CHARSET' => 'utf8mb4',
    'DB_HOST' => '10.0.1.88',
    'DB_NAME' => 'faith',
    'DB_USER' => 'shenbin',
    'DB_PWD' => '123456',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'wtw_',

    // 应用配置
    'AUTH_KEY'	=> 'sdDjkGpskdjflj3289324w98#@$%^',
    'VIDEO_URL' => 'http://7o4zdo.com2.z0.glb.qiniucdn.com/',
    'IMG_URL' => 'http://7xopel.com2.z0.glb.qiniucdn.com/',
    'AVATAR_URL' => 'http://7kttnj.com2.z0.glb.qiniucdn.com/',
    'TMPL_ACTION_ERROR'=>'Public:error',
    'AUTH_API_URL'=>'http://api.data.com/',

	'URL_ROUTER_ON'   => true, 
	'URL_ROUTE_RULES'=>array(
	    '/^v1(?:\/(\w+))(?:\/(\w+))$/i' => 'Api/:1/:2',
	    // '/^(\w+)(?:\/(\w+))$/' => 'Home/:1/:2',
	),
);