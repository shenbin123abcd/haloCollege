<?php

namespace Admin\Controller;

class CourseController extends CommonController {

    public function _before_add(){
        $this->cate = C('KE.COURSE_CATE');
        $this->token = $this->qiniu('crmpub', 'ke/cover');
    }

    public function _before_edit(){
        $this->_before_add();

        $guset_id = $this->model()->where(array('id'=>I('id')))->getField('guest_id');

        $this->guest_name = M('SchoolGuests')->where(array('id'=>$guset_id))->getField('title');
    }


}