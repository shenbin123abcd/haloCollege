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

/**
 * 检查用户是否购买视频
*/
function is_buy($uid,$vid){
    $record = M('VideoOrder')->where(array('status'=>1,'uid'=>$uid,'vid'=>$vid))->count();

    return empty($record) ? 0 : 1;

}

/**
 * 判断用户是否登录
*/
function is_login($uid){
    $login = M('UserLogin')->where(array('uid'=>$uid))->find();
    if ($login['is_login']==1 && $login['token_exp']>time()){
        $stauts = 1;
    }else{
        $stauts = 0;
    }
    
    return $stauts;
}
