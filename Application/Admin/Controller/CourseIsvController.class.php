<?php

namespace Admin\Controller;

class CourseIsvController extends CommonController {

    public function _before_add(){
        $this->token = $this->qiniu('crmpub', 'ke/isv/cover');
        $this->region = M('Region')->field('region_id,region_name,parent_id,level')->select();
    }

    public function _before_edit(){
        $this->_before_add();
    }

    public function _before_insert() {
        $_POST['province_title'] = M('Region')->where(array('region_id' => $_POST['province']))->getField('region_name');
        $_POST['city_title'] = M('Region')->where(array('region_id' => $_POST['city']))->getField('region_name');
        $_POST['district_title'] = M('Region')->where(array('region_id' => $_POST['region']))->getField('region_name');
    }

    public function _before_update(){
        $this->_before_insert();
    }
}