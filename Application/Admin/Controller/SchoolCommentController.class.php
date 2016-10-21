<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/20
 * Time: 17:49
 */

namespace Admin\Controller;


class SchoolCommentController extends CommonController{
    public function _join(&$data){
        $remark = array('评论','回复');
        foreach ($data as $key=>$value){
            $vids[] = $value['vid'];
        }
        $vids =array_unique($vids);
        $where['id'] = array('in',$vids);
        $videos = D('SchoolVideo')->where($where)->getField('id,title');
        foreach ($data as $key=>$value){
            $data[$key]['video_title'] = $videos[$value['vid']];
            $data[$key]['type'] = $remark[$value['remark']];
        }

    }

}