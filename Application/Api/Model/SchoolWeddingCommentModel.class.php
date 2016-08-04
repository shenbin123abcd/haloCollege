<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/28
 * Time: 18:34
 */

namespace Api\Model;

use Think\Model;

class SchoolWeddingCommentModel extends Model{
    //自动完成
    protected  $_auto =array(
        array('create_time','time',Model::MODEL_INSERT,'function'),
        array('update_time','time',Model::MODEL_BOTH,'function'),
        array('status','1',Model::MODEL_INSERT,'string')
    );

    //自动验证
    protected $_validate =array(
        array('content','require','请填写评论或回复内容！',Model::MUST_VALIDATE,'regex',Model::MODEL_BOTH),
    );

     //回复消息推送
    public function pushMsg($contents,$uid){
        // 推送的url地址
        $push_api_url = "http://im.halobear.com:2121/";
        $post_data = array(
            "type" => "publish",
            "content" => json_encode($contents),
            "to" => $uid,
        );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $push_api_url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
        $return = curl_exec ( $ch );
        curl_close ( $ch );
    }



}