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
function make_token($bucket, $module, $callbackUrl, $key) {
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


//生成七牛token --编辑器
function make_qiniu_token($bucket, $module, $returnUrl, $key) {
    $accessKey = C('QINIU_AK');
    $secretKey = C('QINIU_SK');

    $deadline = time()+1728000;
    $saveKey = 'College/' . $module . '/' . ($key ? $key : '$(year)$(mon)/${day}/$(etag)$(suffix)');
    $returnBody = 'key=$(key)&w=$(imageInfo.width)&h=$(imageInfo.height)&fname=&fsize=$(fsize)&filetype=${x:filetype}&code=${x:code}&module=' . $module;
    $bucket = $key ? $bucket . ':' . $saveKey : $bucket;
    $data =  array(
        'scope'=>$bucket,
        'deadline'=>$deadline,
        'saveKey'=>$saveKey,
        'returnUrl'=>$returnUrl,
        'returnBody'=>$returnBody
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