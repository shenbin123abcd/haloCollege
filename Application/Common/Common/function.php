<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/6/28
 * Time: 13:12
 */

/**
 * 获取头像地址
 * @param        $uid
 * @param string $size
 * @param int    $is_url
 * @return string
 */
function get_avatar($uid, $size = 'middle', $is_url = 1) {
    $size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
    $name = md5($uid . 'halobear');
    if ($is_url) {
        $url = $is_url ? C('AVATAR_URL') . 'avatar/' : '';
        return $url . $name . '!' . $size;
    } else {
        return $name;
    }
}

/**
 * 把时间戳转换成多少分钟以前
 * @param $time
 * @return bool|string
 */
function word_time($time) {
    $time = (int)substr($time, 0, 10);
    $int = time() - $time;
    $str = '';
    if ($int <= 2) {
        $str = sprintf('刚刚', $int);
    } elseif ($int < 60) {
        $str = sprintf('%d秒前', $int);
    } elseif ($int < 3600) {
        $str = sprintf('%d分钟前', floor($int / 60));
    } elseif ($int < 86400) {
        $str = sprintf('%d小时前', floor($int / 3600));
    } elseif ($int < 2592000) {
        $str = sprintf('%d天前', floor($int / 86400));
    } else {
        $str = date('Y年m月d日', $time);
    }
    return $str;
}

/**
 * 加密函数
 * @param string data
 * @return string 加密后的字符串
 */
function encrypt($data, $key) {
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
    $iv = substr($key, 0, 16);
    mcrypt_generic_init($td, $key, $iv);
    $length = 32;
    $count = mb_strlen($data);
    $amont = $length - ($count % $length);
    if ($amont == 0)
        $amont = $length;
    $pad_char = chr($amont & 0xFF);
    $data = $data . str_repeat($pad_char, $amont);
    $encrypted = mcrypt_generic($td, $data);
    mcrypt_generic_deinit($td);

    return $encrypted;
}

/**
 * 解密函数
 * @param string data 加密过的字符串
 * @return string 解密后的字符串
 */
function decrypt($data, $key) {
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
    $iv = substr($key, 0, 16);
    mcrypt_generic_init($td, $key, $iv);
    $data = mdecrypt_generic($td, $data);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);

    return rtrim($data, substr($data, -1));
}

/**
 * jwt加密
 * @param  [type] $data 加密数据
 * @param  integer $exp 过期时间
 * @return string       加密数据
 */
function jwt_encode($data = array(), $exp = 2592000) {
    Vendor('jwt.JWT');
    $data['exp'] = time() + $exp;
    $key = C('AUTH_KEY');
    $token = JWT::encode($data, $key);
    return $token;
}

/**
 * jwt解密
 * @param $token 解密数据
 * @return array
 */
function jwt_decode($token) {
    Vendor('jwt.JWT');
    $key = C('AUTH_KEY');
    $decoded = '';
    try {
        $decoded = JWT::decode($token, $key, array('HS256'));
    } catch (Exception $e) {
        return array('iRet' => 0, 'info' => $e->getMessage());
    }
    return array('iRet' => 1, 'data' => (array)$decoded);
}

/**
 * 获取用户信息
 * @return array
 */
function get_user() {
    $header = getallheaders();
    $auth = empty($header['Authorization']) ? $header['authorization'] : $header['Authorization'];
    $cookie = cookie('halo_token');

    if (!empty($auth)) {
        $data = jwt_decode(substr($auth, 7));
    } elseif (!empty($cookie)) {
        $data = jwt_decode($cookie);
    } else {
        return array();
    }

    return $data['iRet'] ? $data['data'] : array();
}