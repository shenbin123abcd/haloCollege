<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/13
 * Time: 16:29
 */

namespace Admin\Model;


use Think\Model;

class MemberOrderModel extends Model{
    public function _after_insert($data,$options){
        // 会员开通时长
        $cate = M('SchoolMemberCate')->where(array('id'=>$data['cate']))->field('cycle,title')->find();
        
        $member = M('SchoolMember')->where(array('uid'=>$data['uid']))->find();
        $end_time = time() + $cate['cycle']*30*86400;
        if (empty($member)){
            M('SchoolMember')->add(array('uid'=>$data['uid'], 'end_time'=>$end_time, 'create_time'=>time(), 'update_time'=>time(), 'status'=>1));
        }else{
            // 是否过期
            if ($member['end_time'] > time()){
                $end_time = $member['end_time'] + $cate['cycle']*30*86400;
            }
            M('SchoolMember')->where(array('uid'=>$data['uid']))->save(array('end_time'=>$end_time, 'update_time'=>time()));
        }


        //会员购买成功后的消息推送
        $this->member_notice($data['uid'],$cate['title']);
    }

    //会员购买成功消息通知
    public function member_notice($uid,$content){
        $push = A('Push');
        $result = $push->pushMsgPersonal(array('uid'=>$uid,'content'=>'尊敬的会员'.','.'您已成功购买'.$content,'extra'=>array('push_time'=>time()),'type'=>3));
    }

}