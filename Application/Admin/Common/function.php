<?php

// ==================================================================
//
// 公共函数库
//
// ------------------------------------------------------------------

/**
 * 获取/设置用户信息
 *
 * @param array   $info 用户信息
 * @return array
 */
function member_info( $info = array() ) {
    if ( is_null( $info ) ) {
        session( C( 'USER_AUTH_KEY' ), null );
    }else {
        return ( empty( $info ) ) ? session( C( 'USER_AUTH_KEY' ) ) : session( C( 'USER_AUTH_KEY' ), $info ) ;
    }
}

/**
 * 生成图片缩略图
 *
 * @param type    $image      原始图片
 * @param type    $dst_file   缩略图名称
 * @param type    $new_width  图片宽度
 * @param type    $new_height 图片高度
 * @return string
 */
function image_thumb( $src_file, $new_width, $new_height ) {
    //转换本地路径
    $src_file = attach_server( $src_file, false );
    //本地处理地址
    $src_file = '.' . ltrim( $src_file, '.' );
    is_file( $src_file ) || $src_file = C( 'DEFAULT_IMAGE' );
    if ( is_file( $src_file ) && $new_width > 0 && $new_height > 0 ) {
        $pathinfo = pathinfo( $src_file );
        $dst_file = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $new_width . 'x' . $new_height . '.' . $pathinfo['extension'];
        if ( !is_file( $dst_file ) ) {
            import( "ORG.Util.Images" );
            Images::thumb( $src_file, $dst_file, $new_width, $new_height );
        }
        return attach_server( $dst_file );
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

///**
// * 系统加密方法
// *
// * @param [type]  $data   [description]
// * @param [type]  $key    [description]
// * @param integer $expire [description]
// * @return [type]          [description]
// */
//function think_encrypt( $data, $key, $expire = 0 ) {
//    $key = md5( $key );
//    $data = base64_encode( $data );
//    $x = 0;
//    $len = strlen( $data );
//    $l = strlen( $key );
//    $char = '';
//    for ( $i = 0; $i < $len; $i++ ) {
//        if ( $x == $l )
//            $x = 0;
//        $char .=substr( $key, $x, 1 );
//        $x++;
//    }
//    $str = sprintf( '%010d', $expire ? $expire + time() : 0 );
//    for ( $i = 0; $i < $len; $i++ ) {
//        $str .=chr( ord( substr( $data, $i, 1 ) ) + ( ord( substr( $char, $i, 1 ) ) ) % 256 );
//    }
//
//    return str_replace( '=', '', base64_encode( $str ) );
//}

///**
// * 系统解密方法
// *
// * @param string  $data 解密字符串
// * @param string  $key  密钥
// * @return string       原始数据
// */
//function think_decrypt( $data, $key ) {
//    $key = md5( $key );
//    $x = 0;
//    $data = base64_decode( $data );
//    $expire = substr( $data, 0, 10 );
//    $data = substr( $data, 10 );
//    if ( $expire > 0 && $expire < time() ) {
//        return '';
//    }
//    $len = strlen( $data );
//    $l = strlen( $key );
//    $char = $str = '';
//    for ( $i = 0; $i < $len; $i++ ) {
//        if ( $x == $l )
//            $x = 0;
//        $char .=substr( $key, $x, 1 );
//        $x++;
//    }
//    for ( $i = 0; $i < $len; $i++ ) {
//        if ( ord( substr( $data, $i, 1 ) ) < ord( substr( $char, $i, 1 ) ) ) {
//            $str .=chr( ( ord( substr( $data, $i, 1 ) ) + 256 ) - ord( substr( $char, $i, 1 ) ) );
//        } else {
//            $str .=chr( ord( substr( $data, $i, 1 ) ) - ord( substr( $char, $i, 1 ) ) );
//        }
//    }
//    return base64_decode( $str );
//}

/**
 * 字符串截取
 *
 * @param [type]  $string 字符串
 * @param [type]  $length 长度
 * @param integer $start  开始位置
 * @param bool    $html   是否过滤html
 * @return [type]          返回截取后的字符串
 */
function csubstr( $string, $length = 200, $start = 0, $html = true ) {
    $html && $string = strip_tags( $string );
    if ( strlen( $string )>$length ) {
        $str='';
        $len=$start+$length;
        $i = $start;
        while ( $i<$len ) {
            if ( ord( substr( $string, $i, 1 ) )>=128 ) {
                $str.=substr( $string, $i, 3 );
                $i = $i+ 3;
            } else {
                $str.=substr( $string, $i, 1 );
                $i ++;
            }
        }
        return $str .'...';
    } else {
        return $string;
    }
}

function file_request() {
    return I( 'upload_file_name' );
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

/**
 * 时间友好显示
 *
 * @param [type]  $time [description]
 * @return [type]       [description]
 */
function mdate( $time = NULL ) {
    $text = '';
    $time = $time === NULL || $time > time() ? time() : intval( $time );
    $t = time() - $time; //时间差 （秒）
    if ( $t == 0 )
        $text = '刚刚';
    elseif ( $t < 60 )
        $text = $t . '秒前'; // 一分钟内
    elseif ( $t < 60 * 60 )
        $text = floor( $t / 60 ) . '分钟前'; //一小时内
    elseif ( $t < 60 * 60 * 24 )
        $text = floor( $t / ( 60 * 60 ) ) . '小时前'; // 一天内
    elseif ( $t < 60 * 60 * 24 * 2 )
        $text = '昨天 ' . date( 'H:i', $time ); //两天内
    elseif ( $t < 60 * 60 * 24 * 3 )
        $text = '前天 ' . date( 'H:i', $time ); // 三天内
    elseif ( $t < 60 * 60 * 24 * 30 )
        $text = date( 'm月d日 H:i', $time ); //一个月内
    elseif ( $t < 60 * 60 * 24 * 365 )
        $text = date( 'm月d日', $time ); //一年内
    else
        $text = date( 'Y年m月d日', $time ); //一年以前
    return $text;
}

/**
 * sendcloud 邮件发送
 * @param  [type] $title   标题
 * @param  [type] $content 邮件内容
 * @param  [type] $email   接收邮箱
 * @return [type]          [description]
 */
function send_mail($title,$content,$email) {
    $ch = curl_init();
    $to_json = json_encode(array('to'=>array_values($email)));
    curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

    curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
    curl_setopt( $ch, CURLOPT_URL, 'https://sendcloud.sohu.com/webapi/mail.send.json' );

    curl_setopt( $ch, CURLOPT_POSTFIELDS,
        array( 'api_user' => 'postmaster@halobear.sendcloud.org',
            'api_key' => 'SZEPbkbnTcEcH2KP',
            'from' => 'service@xmail.halobear.com',
            'fromname' => '幻熊科技',
            'to' => implode(';', $email),
            'subject' => $title,
            'html' => $content,
            'x_smtpapi' => $to_json ) );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    $result = curl_exec( $ch );

    if ( $result === false ) { //请求失败
        write_log( 'sendmail_error', curl_error( $ch ) );
        return false;
    }

    curl_close( $ch );

    $result = json_decode($result,1);
    return $result['message'] == 'success' ? true : false;
}

/**
 * 通过邮件模板发送
 */
function send_mail_for_tpl($title,$to,$data,$tpl='halo_notify_tpl',$type = 0){
    $ch = curl_init();
    $to_json = json_encode(array('to'=>array_values($to),'sub'=>$data));
    $api_user = $type ? 'postmaster@halobear.sendcloud.org' : 'postmaster@halo.sendcloud.org';
    curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

    curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
    curl_setopt( $ch, CURLOPT_URL, 'https://sendcloud.sohu.com/webapi/mail.send_template.json' );

    curl_setopt( $ch, CURLOPT_POSTFIELDS,
        array( 'api_user' => $api_user,
            'api_key' => 'SZEPbkbnTcEcH2KP',
            'from' => 'service@email.halobear.com',
            'fromname' => '幻熊科技',
            'template_invoke_name'=>$tpl,
            'subject' => $title,
            'substitution_vars' => $to_json ) );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    $result = curl_exec( $ch );
    $result = json_decode($result,1);
    if ( $result === false || $result['message'] == 'error' ) { //请求失败
        write_log( 'sendmail_tpl_error', curl_error( $ch ) );
        return false;
    }

    curl_close( $ch );

    return $result['message'] == 'success' ? true : false;
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

function wcache($ck,$cv,$time){
    if (empty($ck)) {
        return false;
    }
    if (isset($cv)) {
        $count = M('Auth')->where(array('ck'=>$ck))->count();
        $cv = serialize($cv);
        if ($count) {
            M('Auth')->where(array('ck'=>$ck))->save(array('cv'=>$cv,'create_time'=>time()));
        }else{
            $ret = M('Auth')->add(array('ck'=>$ck,'cv'=>$cv,'create_time'=>time()));
        }
        return $ret;
    }else{
        $data = M('Auth')->where(array('ck'=>$ck))->getField('cv');

        return unserialize($data);
    }
}

function format_duration($time){
    if (empty($time)) {
        return '0:00';
    }

    $min = intval($time/60);
    $second = intval($time%60);

    return str_pad($min, 2, '0', STR_PAD_LEFT) . ':' . str_pad($second, 2, '0', STR_PAD_LEFT);
}

/*
*功能：php完美实现下载远程图片保存到本地
*参数：文件url,保存文件目录,保存文件名称，使用的下载方式
*当保存文件名称为空时则使用远程文件原来的名称
*/
function getImage($url,$save_dir='',$filename='',$type=1){
    if(trim($url)==''){
        return array('file_name'=>'','save_path'=>'','error'=>1);
    }
    if(trim($save_dir)==''){
        $save_dir='./';
    }
    if(trim($filename)==''){//保存文件名
        $ext=strrchr($url,'.');
        if(!in_array($ext, array('.gif', '.jpg', '.jpeg', '.png'))){
            return array('file_name'=>'','save_path'=>'','error'=>3);
        }
        $filename=basename($url);
    }
    if(0!==strrpos($save_dir,'/')){
        $save_dir.='/';
    }
    //创建保存目录
    if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
        return array('file_name'=>'','save_path'=>'','error'=>5);
    }

    // 判断是否存在
    if(file_exists($save_dir.$filename)){
        return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>7);
    }
    //获取远程文件所采用的方法
    if($type){
        $ch=curl_init();
        $timeout=5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $img=curl_exec($ch);
        curl_close($ch);
    }else{
        ob_start();
        readfile($url);
        $img=ob_get_contents();
        ob_end_clean();
    }
    //$size=strlen($img);
    //文件大小
    $fp2=@fopen($save_dir.$filename,'a');
    fwrite($fp2,$img);
    fclose($fp2);
    unset($img,$url);
    return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
}

/**
 * 创建目录
 * @param  [type]  $mkpath [description]
 * @param  integer $mode   [description]
 * @return [type]          [description]
 */
function  mkpath( $mkpath , $mode =0777){
    $path_arr = explode ( '/' , $mkpath );

    foreach( $path_arr as $value ){
        if (!empty( $value )){
            if ( empty( $path ))
                $path = $value;
            else
                $path .= '/' . $value;
            is_dir ( $path )  or   mkdir ( $path , $mode );
        }
    }

    if ( is_dir ( $mkpath )) return  true;

    return  false;
}

///**
// * jwt加密
// * @param  [type] $data 加密数据
// * @return [type]       [description]
// */
//function jwt_encode($data = array(), $exp = 2592000){
//    Vendor('jwt.JWT');
//    $data['exp'] = time() + $exp;
//    $key = C('AUTH_KEY');
//    $token = JWT::encode($data, $key);
//    return $token;
//}

///**
// * jwt解密
// * @param  [type] $token 解密数据
// * @return [type]       [description]
// */
//function jwt_decode($token){
//    Vendor('jwt.JWT');
//    $key = C('AUTH_KEY');
//    $decoded = '';
//    try{
//        $decoded = JWT::decode($token, $key, array('HS256'));
//    }catch(Exception $e){
//        return array('iRet'=>0, 'info'=>$e->getMessage());
//    }
//    return array('iRet'=>1, 'data'=>(array)$decoded);
//}

//function get_user(){
//    $header = getallheaders();
//    $auth = empty($header['Authorization']) ? $header['authorization'] : $header['Authorization'];
//    $cookie = cookie('halo_token');
//
//    if (!empty($auth)) {
//        $data = jwt_decode(substr($auth, 7));
//    } elseif (!empty($cookie)) {
//        $data = jwt_decode($cookie);
//    } else {
//        return array();
//    }
//
//    return $data['iRet'] ? $data['data'] : array();
//}
/**
 * 获取状态
 * @param int $status
 * @return type
 */
function get_status($status) {
    return '<b class="status status' . $status . '">' . $status . '</b>';
}


/**
 * 数据转成树型结构
 * @param  array  $data 数据
 * @param  integer $pid  父级ID
 * @param  booler $type   类型
 * @return array        树数据
 */
function to_tree($data, $pid = 0, $type = true) {
    static $result = array();
    $type && $result = array();
    foreach ($data as $key => $value) {
        if ($value['pid'] == $pid) {
            $result[]=$value;
            unset($data[$key]);
            to_tree($data, $value['id'], false);
        }
    }
    return $result;
}

/**
 * 生成七牛上传凭证
 */
function make_qiniu_token($bucket, $module, $callbackUrl, $key) {
    $accessKey = C('QINIU_AK');
    $secretKey = C('QINIU_SK');

    $deadline = time()+1728000;
    $saveKey = 'College/' . $module . '/' . ($key ? $key : '$(year)$(mon)/${day}/$(etag)$(suffix)');
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



?>