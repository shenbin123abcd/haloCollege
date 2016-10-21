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

    public function edit(){
        $model = $this->model();
        $pk = $model->getPk();
        $data = $model->where(array($pk=>$_GET[$pk]))->find();
        empty($data) && $this->error('查询数据失败！');
        $data['price_model'] = json_decode(html_entity_decode($data['price_model']), 1);
        $this->assign('data',$data);
        $this->display();
    }

    public function _before_insert(){
        $price_mod = '';
        foreach ($_POST['price_model'] as $key=>$item) {
            foreach ($item AS $k=>$v){
                if (intval($v) > 0){
                    $price_mod[$k][$key] = $v;
                }
            }
        }

        $_POST['price_model'] = $price_mod ? json_encode($price_mod) : '';
//        $this->error('error', $price_mod);
    }

    public function _before_update(){
        $this->_before_insert();
    }

}