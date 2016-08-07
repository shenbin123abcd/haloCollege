<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/20
 * Time: 17:51
 */

namespace Api\Model;

use Think\Model;
class UserinfoModel extends Model{
    /**
     * 自动验证
     * @var $_validate
     */
    protected $_validate = array(
        array('truename', 'require', '用户姓名不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('sex', 'require', '性别不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('wechat', 'require', '微信不能为空！', self::VALUE_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('province', 'require', '用户地区不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
        array('company', 'require', '单位不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
         array('position', 'require', '职务不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
         //array('brief', 'require', '用户简介不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),

    );

    /**
     * 自动完成
     * @var $_auto
     */
    protected $_auto = array(
        array('create_time', 'time', self:: MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_BOTH, 'function'),
        array('status', '1', self::MODEL_INSERT, 'string'),
        
    );

    /**
     * 同步职位信息到微社区
     */
    public function positionToMicro($data,$access_token,$wsq_name,$gender){
        $ua = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($ua, 'iPhone') || strpos($ua, 'iPad')) {
            $ak = '56f113a167e58efb84002cad';
            $key = '4f41ebcae227fb2155bb2f26b47291e0';
            //$ak = C('IOS_AK');
            //$key = C('IOS_SECRET');
        } else {
            $ak = '56eb5e70e0f55a0a56001071';
            $key = 'cc00425db7f281c9110d618855b4ba67';
            //$ak = C('ANDROID_AK');
            //$key = C('ANDROID_SECRET');
        }

        //封装用户职位信息
        //$custom['company'] =$data['company'];
        $custom['position'] =$data['position'];
        //$custom['truename'] =$data['truename'];
        $custom_json = json_encode($custom);
        //参数封装成x-www-form-urlencoded格式
        $str="";
        $str.='custom='.$custom_json;
        $str.='&name='.$wsq_name;
        $str.='&gender='.$gender;
        $url = 'https://rest.wsq.umeng.com/0/user/update?ak=' . $ak.'&access_token='.$access_token;
        $result = curl_put($url,$str);
        return $result;
    }

}