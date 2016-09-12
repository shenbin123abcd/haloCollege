<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/18
 * Time: 13:49
 */

namespace Admin\Controller;


class SchoolWeddingCommentController extends CommonController{
    public function _join(&$data){
        foreach ($data as $key=>$value){
            $wedding_id[] = $value['remark_id'];
        }
        $wedding_id =array_unique($wedding_id);
        $where['id'] = array('in',$wedding_id);
        $wedding = D('SchoolWedding')->where($where)->getField('id,headline');
        foreach ($data as $key=>$value){
            $data[$key]['wedding_title'] = $wedding[$value['remark_id']];
        }
        
    }
    


}