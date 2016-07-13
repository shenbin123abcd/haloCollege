<?php
return array(
	//URL模式
	'URL_MODEL' => 2,	
	'URL_CASE_INSENSITIVE'	=>	1,
	'TMPL_TEMPLATE_SUFFIX'  => '.html',
	'URL_HTML_SUFFIX' => 'html|json',
	'APP_GROUP_MODE'	=>	1,
	'MODULE_ALLOW_LIST' =>    array('Home','Api','Admin'),
	'DEFAULT_MODULE'    =>    'Home',

    // 应用配置
    'AUTH_KEY'	=> 'sdDjkGpskdjflj3289324w98#@$%^',
    'VIDEO_URL' => 'http://7o4zdo.com2.z0.glb.qiniucdn.com/',
    'IMG_URL' => 'http://7xopel.com2.z0.glb.qiniucdn.com/',
    'AVATAR_URL' => 'http://7kttnj.com2.z0.glb.qiniucdn.com/',
    'AUTH_API_URL'=>'http://api.data.com/',

    'URL_ROUTER_ON'   => true, 
    'URL_ROUTE_RULES'=>array(
        '/^v1(?:\/(\w+))(?:\/(\w+))$/i' => 'Api/:1/:2',
        // '/^(\w+)(?:\/(\w+))$/' => 'Home/:1/:2',
    ),
	
	'APP_SUB_DOMAIN_DEPLOY'   =>    1, // 开启子域名配置
	'APP_SUB_DOMAIN_RULES'    =>    array(   
		'koala-college'        => 'Admin',
	),

    /* 数据库配置 */
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '112.124.16.5', // 服务器地址
    'DB_NAME'   => 'halocollege', // 数据库名
    'DB_USER'   => 'web_root', // 用户名
    'DB_PWD'    => 'halo2015bear',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'wtw_', // 数据库表前缀


);