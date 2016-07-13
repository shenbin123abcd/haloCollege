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



}