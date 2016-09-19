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
        $token = substr($auth, 7);
        $data = jwt_decode($token);
        if ($data['iRet']){
            $count = M('Session')->where(array('uid'=>$data['data']['id'], 'token'=>md5($token)))->count();
            $data = $count ? $data : array();
        }
    } elseif (!empty($cookie)) {
        $data = jwt_decode($cookie);
    } else {
        return array(); 
    }

    return $data['iRet'] ? $data['data'] : array();
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0) {
    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time():0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = ''){
    $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data   = str_replace(array('-','_'),array('+','/'),$data);
    $mod4   = strlen($data) % 4;
    if ($mod4) {
       $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);
    $expire = substr($data,0,10);
    $data   = substr($data,10);

    if($expire > 0 && $expire < time()) {
        return '';
    }
    $x      = 0;
    $len    = strlen($data);
    $l      = strlen($key);
    $char   = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
* 对查询结果集进行排序
* @access public
* @param array $list 查询结果
* @param string $field 排序的字段名
* @param array $sortby 排序类型
* asc正向排序 desc逆向排序 nat自然排序
* @return array
*/
function list_sort_by($list,$field, $sortby='asc') {
   if(is_array($list)){
       $refer = $resultSet = array();
       foreach ($list as $i => $data)
           $refer[$i] = &$data[$field];
       switch ($sortby) {
           case 'asc': // 正向排序
                asort($refer);
                break;
           case 'desc':// 逆向排序
                arsort($refer);
                break;
           case 'nat': // 自然排序
                natcasesort($refer);
                break;
       }
       foreach ( $refer as $key=> $val)
           $resultSet[] = &$list[$key];
       return $resultSet;
   }
   return false;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order='id', &$list = array()){
    if(is_array($tree)) {
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if(isset($reffer[$child])){
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby='asc');
    }
    return $list;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

//基于数组创建目录和文件
function create_dir_or_files($files){
    foreach ($files as $key => $value) {
        if(substr($value, -1) == '/'){
            mkdir($value);
        }else{
            @file_put_contents($value, '');
        }
    }
}

function curl_get( $url, $header = array() ) {
    
    //初始化curl
    $ch = curl_init();
    //设置超时
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    if (!empty($header)) {
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    }
    curl_setopt( $ch, CURLOP_TIMEOUT, 30 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
    //运行curl，结果以json形式返回
    $res = curl_exec( $ch );
    curl_close( $ch );
    $data = json_decode( $res, true );

    return $data;
}

function curl_post( $url, $data, $header = array() ) {
    //初始化curl
    $ch = curl_init();
    //设置超时
    curl_setopt( $ch, CURLOP_TIMEOUT, 30 );
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt ($ch, CURLOPT_HTTPHEADER , $header );
    curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
    curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    //运行curl，结果以json形式返回
    $res = curl_exec( $ch );
    curl_close( $ch );
    $data = json_decode( $res, true );

    return $data;
}

function curl_put( $url, $data, $header = array() ) {

    //初始化curl
    $ch = curl_init();
    //设置超时
    curl_setopt( $ch, CURLOP_TIMEOUT, 30 );
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt ($ch, CURLOPT_HTTPHEADER , $header );
    curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
    curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    //运行curl，结果以json形式返回
    $res = curl_exec( $ch );
    curl_close( $ch );
    $data = json_decode( $res, true );

    return $data;
}


/**
 * 生成七牛上传凭证
 */
function make_qiniu_token_headimg($bucket, $module, $callbackUrl, $key) {
    $accessKey = C('QINIU_AK');
    $secretKey = C('QINIU_SK');

    $deadline = time()+1728000;
    $saveKey = $module . '/' . ($key ? $key : '$(year)$(mon)/${day}/$(etag)$(suffix)');
    $callbackBody = 'key=$(key)&w=$(imageInfo.width)&h=$(imageInfo.height)&fname=$(fname)&fsize=$(fsize)&filetype=${x:filetype}&code=${x:code}&module=' . $module;
    $bucket = $key ? $bucket . ':' . $saveKey : $bucket;
    $data =  array(
        'scope'=>$bucket,
        'deadline'=>$deadline,
        'saveKey'=>$saveKey,
        'callbackUrl'=>$callbackUrl,
        'callbackBody'=>$callbackBody
    );
    $data = json_encode($data);
    $find = array('+', '/');
    $replace = array('-', '_');
    $data = str_replace($find, $replace, base64_encode($data));
    $sign = hash_hmac('sha1', $data, $secretKey, true);
    $qiniu_mall_token = $accessKey . ':' . str_replace($find, $replace, base64_encode($sign)).':'.$data ;
    return $qiniu_mall_token;
}

//获取用户真实姓名
function getTrueName($uid){
    $where['uid'] = $uid;
    $where['status'] =1;
    $user = M('Userinfo')->where($where)->find();
    return $user;
}

/**
 * 认证码 编码&解码
 */
function authcode( $string, $operation = 'DECODE', $key = '', $expiry = 0 ) {
    $ckey_length = 4;
    $key = md5( $key ? $key : 'sUi$LBsf48M34jdXs#yir@90Yo0I' );
    $keya = md5( substr( $key, 0, 16 ) );
    $keyb = md5( substr( $key, 16, 16 ) );
    $keyc = $ckey_length ? ( $operation == 'DECODE' ? substr( $string, 0, $ckey_length ): substr( md5( microtime() ), -$ckey_length ) ) : '';

    $cryptkey = $keya.md5( $keya.$keyc );
    $key_length = strlen( $cryptkey );

    $string = $operation == 'DECODE' ? base64_decode( substr( $string, $ckey_length ) ) : sprintf( '%010d', $expiry ? $expiry + time() : 0 ).substr( md5( $string.$keyb ), 0, 16 ).$string;
    $string_length = strlen( $string );

    $result = '';
    $box = range( 0, 255 );

    $rndkey = array();
    for ( $i = 0; $i <= 255; $i++ ) {
        $rndkey[$i] = ord( $cryptkey[$i % $key_length] );
    }

    for ( $j = $i = 0; $i < 256; $i++ ) {
        $j = ( $j + $box[$i] + $rndkey[$i] ) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ( $a = $j = $i = 0; $i < $string_length; $i++ ) {
        $a = ( $a + 1 ) % 256;
        $j = ( $j + $box[$a] ) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr( ord( $string[$i] ) ^ ( $box[( $box[$a] + $box[$j] ) % 256] ) );
    }

    if ( $operation == 'DECODE' ) {
        if ( ( substr( $result, 0, 10 ) == 0 || substr( $result, 0, 10 ) - time() > 0 ) && substr( $result, 10, 16 ) == substr( md5( substr( $result, 26 ).$keyb ), 0, 16 ) ) {
            return substr( $result, 26 );
        } else {
            return '';
        }
    } else {
        return $keyc.str_replace( '=', '', base64_encode( $result ) );
    }

}

/**
 * 手机格式校验
 * @param $phone
 * @return int
 */
function is_mobile($phone){
    return preg_match("/^1[34578]\d{9}$/",$phone);
}

/**
 *  send_msg('13817061546',array('78232',10),1)
 * 发送模板短信
 *
 * @param to      手机号码集合,用英文逗号分开
 * @param datas   内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
 * @param unknown $tempId 模板Id
 */
function send_msg( $to, $datas, $tempId = 1, $appId='8a48b551488d07a80148a59dbb9609f2' ) {
    // 初始化REST SDK
    Vendor( 'Sms.CCPRestSDK' );

    //主帐号
    $accountSid= 'aaf98f89488d0aad0148a59d38880a0c';

    //主帐号Token
    $accountToken= '0af20c4166e0470cb61489bb896f9170';

    //请求地址，格式如下，不需要写https://
    //app.cloopen.com
    $serverIP='sandboxapp.cloopen.com';

    //请求端口
    $serverPort='8883';

    //REST版本号
    $softVersion='2013-12-26';

    $rest = new REST( $serverIP, $serverPort, $softVersion );
    $rest->setAccount( $accountSid, $accountToken );
    $rest->setAppId( $appId );

    // 发送
    $result = $rest->sendTemplateSMS( $to, $datas, $tempId );

    if ( $result == NULL ) {
        write_log( 'sendmsg_'.date( 'Ymd' ), 'result error!' );
        return false;
    }
    if ( $result->statusCode!=0 ) {
        write_log( 'sendmsg_'.date( 'Ymd' ), var_export( $result, 1 ) );
        return array('iRet'=>$result->statusCode, 'info'=>$result->statusMsg);
    }else {
        return array('iRet'=>$result->statusCode, 'info'=>$result->statusMsg);
    }
}

/**
 * 发送邮件
 *
 * @param [type]  $address 收件人地址
 * @param [type]  $subject 邮件主题
 * @param [type]  $body    邮件内容
 * @param boolean $html    是否是html
 * @return [type]
 */
function sendEmail( $address, $subject, $body, $html=true ) {
    vendor( 'PHPMailer.phpmailer' );
    $mail = new PHPMailer(); //new一个PHPMailer对象出来
    $mail->CharSet ='utf-8';//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP(); // 设定使用SMTP服务
    $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能

    $mail->Host       = C( 'EMAIL_HOST' );      // SMTP 服务器
    $mail->Port       = C( 'EMAIL_PORT' );                   // SMTP服务器的端口号
    $mail->Username   = C( 'EMAIL' );  // SMTP服务器用户名
    $mail->Password   = C( 'EMAIL_PASS' );            // SMTP服务器密码
    $mail->SetFrom( C( 'EMAIL' ), C( 'EMAIL_HONER' ) );//发件人信息
    $mail->AddReplyTo( C( 'EMAIL' ), C( 'EMAIL_HONER' ) );//回复信息
    $mail->Subject    = $subject;//邮件主题
    $html?$mail->MsgHTML( $body ):$mail->AltBody=$body;//邮件内容
    C( 'MALL_SSL' )&&$mail->SMTPSecure = "ssl";// 安全协议
    $mail->AddAddress( $address, '' );//增加收件人
    return $mail->Send();
}

/**
 * 写入自定义 log 文件
 */
function write_log( $tag, $msg ) {
    $filename = LOG_PATH .$tag. ".log";

    $handler = null;
    if ( ( $handler = fopen( $filename, 'ab+' ) ) !== false ) {
        fwrite( $handler, date( 'r' ) . "\t$msg\n" );
        fclose( $handler );
    }
}

function curl_request($url,$data,$method='PUT'){
    $ch = curl_init(); //初始化CURL句柄
    curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式

    curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'Content-Type:application/x-www-form-urlencoded;charset=UTF-8',
        "X-HTTP-Method-Override: $method"));//设置HTTP头信息
    !empty($data) && curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));//设置提交的字符串
    $document = curl_exec($ch);//执行预定义的CURL
    curl_close($ch);
    $data = json_decode( $document, true );

    return $data;
}
