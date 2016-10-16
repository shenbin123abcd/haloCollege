<?php
/**
 * 课堂函数库
 * User: Kevin
 * Date: 2016/9/6
 * Time: 21:12
 */
function get_address($uid){
    // 检查是否已经存在
    $address = M('Wfc2016Address')->field('name,phone,province,province_title,city,city_title,region,region_title,address')->where(array('uid'=>$uid))->find();

    return $address;
}

/**
 * 新浪短网址
 * @param $url
 * @return mixed
 */
function get_url($url){
    $ret = curl_get('http://api.t.sina.com.cn/short_url/shorten.json?source=3271760578&url_long=' . urlencode($url));
    if (!empty($ret) && isset($ret[0]) && $ret[0]['type']==0){
        $url = $ret[0]['url_short'];
    }
    return $url;
}

function get_code(){
    $agents = cookie('agents');
    if (!empty($agents)){
        // 检查code的有效性
        $check = M('CourseAgents')->where(['code'=>$agents, 'status'=>1])->count();

        if ($check){
            return $agents;
        }
    }

    return '';
}