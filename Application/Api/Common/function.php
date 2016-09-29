<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/9/29
 * Time: 16:53
 */

/**
 * 检查用户是否是会员
 * @param $uid
 * @return int
 */
function check_vip($uid){
    $end_time = M('SchoolMember')->where(array('uid'=>$uid, 'status'=>1))->getField('end_time');

    return !empty($end_time) && $end_time > time() ? 1 : 0;
}