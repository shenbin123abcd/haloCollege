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