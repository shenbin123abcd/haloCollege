<?php

namespace Admin\Controller;

class CourseController extends CommonController {
    public function _join(&$data){
        foreach ($data as $item) {
            $isv_id[] = $item['isv_id'];
        }
        $isv = M('CourseIsv')->where(['id'=>['in', $isv_id]])->getField('id, title');
        foreach ($data as $key=>$item) {
            $data[$key]['isv_id'] = $isv[$item['isv_id']];
        }
    }

    public function _before_add(){
        $this->cate = C('KE.COURSE_CATE');
        $this->token = $this->qiniu('crmpub', 'ke/cover');
        $this->isv = M('CourseIsv')->where(['status'=>1])->select();
    }

    public function _before_edit(){
        $this->_before_add();

        $guset_id = $this->model()->where(array('id'=>I('id')))->getField('guest_id');

        $this->guest_name = M('SchoolGuests')->where(array('id'=>$guset_id))->getField('title');
    }


}