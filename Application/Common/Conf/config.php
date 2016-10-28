<?php
return array(
	//URL模式
	'URL_MODEL' => 2,	
	'URL_CASE_INSENSITIVE'	=>	1,
	'TMPL_TEMPLATE_SUFFIX'  => '.html',
	'URL_HTML_SUFFIX' => 'html|json',
	'APP_GROUP_MODE'	=>	1,
	'MODULE_ALLOW_LIST' =>    array('Home','Api','Admin', 'Ke', 'Wechat'),
	'DEFAULT_MODULE'    =>    'Home',
	
	'LOAD_EXT_CONFIG'=>array('KE'=>'ke', 'USER'=>'user'),

    // 应用配置
    'AUTH_KEY'	=> 'sdDjkGpskdjflj3289324w98#@$%^',
    'VIDEO_URL' => 'http://7o4zdo.com2.z0.glb.qiniucdn.com/',
    'IMG_URL' => 'http://7xopel.com2.z0.glb.qiniucdn.com/',
    'AVATAR_URL' => 'http://7kttnj.com2.z0.glb.qiniucdn.com/',
    'AUTH_API_URL'=>'http://api-test.weddingee.com/',
    'WECHAT_AUTH_KEY'=>'TEMmnbjsod223DD!@#4567',

    'URL_ROUTER_ON'   => true, 	
    'URL_ROUTE_RULES'=>array(
        '/^v1(?:\/(\w+))(?:\/(\w+))$/i' => 'Api/:1/:2',
        // '/^(\w+)(?:\/(\w+))$/' => 'Home/:1/:2',
    ),
	
	'APP_SUB_DOMAIN_DEPLOY'   =>    1, // 开启子域名配置
	'APP_SUB_DOMAIN_RULES'    =>    array(   
		'koala-college'        => 'Admin',
		'ke'        => 'Ke',
		'wechat'    => 'Wechat',
	),



    /* 数据库配置 */
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '112.124.16.5', // 服务器地址
    'DB_NAME'   => 'halocollege', // 数据库名
    'DB_USER'   => 'web_root', // 用户名
    'DB_PWD'    => 'halo2015bear',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'wtw_', // 数据库表前缀

	'QINIU_AK' => 'm_bQ6vCqK-1n_myddynLMQxg0rxw3YqRptv5D7_i',
	'QINIU_SK' => 'EH7AQcudIK47egCwYGzrSFVnutvuCYedfr0Lyl3d',

);