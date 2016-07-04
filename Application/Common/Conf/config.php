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

    /* 模块相关配置 */
    'AUTOLOAD_NAMESPACE' => array('Addons' => ONETHINK_ADDON_PATH), //扩展模块列表
    'DEFAULT_MODULE'     => 'Home',
    'MODULE_DENY_LIST'   => array('Common','User','Admin','Install'),
    //'MODULE_ALLOW_LIST'  => array('Home','Admin'),

    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => 'iVFW$~gO"<M,K^!*h_Itm(/Q:cns.8`SGlrj[D#R', //默认数据加密KEY
    'DATA_CACHE_KEY'=> 'iVFW$~gO"<M,K^!*h_Itm(/Q:cns.8`SGlrj[D#R',

    /* 用户相关设置 */
    'USER_MAX_CACHE'     => 1000, //最大缓存用户数
    'USER_ADMINISTRATOR' => 1, //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 3, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

    /* 全局过滤配置 */
    'DEFAULT_FILTER' => '', //全局过滤函数

    /* 数据库配置 */
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => 'halocollege', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'wtw_', // 数据库表前缀

    /* 文档模型配置 (文档模型核心配置，请勿更改) */
    'DOCUMENT_MODEL_TYPE' => array(2 => '主题', 1 => '目录', 3 => '段落'),
);