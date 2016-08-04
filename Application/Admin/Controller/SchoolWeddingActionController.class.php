<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/24
 * Time: 10:33
 */

namespace Admin\Controller;


class SchoolWeddingActionController extends CommonController{
    /**
     * 默认编辑操作
     * @see CommonAction::edit()
     */
    public function edit(){
        $model = $this->model();
        $pk = $model->getPk();
        $data = $model->where(array($pk=>$_GET[$pk]))->find();
        empty($data) && $this->error('查询数据失败！');
        $data['start_time'] = date("Y/m/d H:i",$data['start_time']);
        $data['end_time'] = date("Y/m/d H:i",$data['end_time']);
        $this->assign('data',$data);
        $this->display();
    }

    public function _before_insert(){
        empty($_POST['headline']) && $this->error('请填写活动主题！');
        empty($_POST['brief']) && $this->error('请填写活动简介！');
        empty($_POST['address']) && $this->error('请填写活动地点！');
        empty($_POST['visitor_count']) && $this->error('请填写参加活动的人数！');
        empty($_POST['conection']) && $this->error('请填写联系方式 ！');
        empty($_POST['start_time']) && $this->error('请填写活动开始时间！');
        empty($_POST['end_time']) && $this->error('请填写活动结束时间！');
        $_POST['create_time'] = time();
        $_POST['update_time'] = time();
        $_POST['status'] =1;
        $_POST['uid'] = $this->user['id'];
       $this->_before_update();
    }

     public function _before_update(){
         $_POST['start_time'] = strtotime($_POST['start_time']);
         $_POST['end_time'] = strtotime($_POST['end_time']);
         $_POST['update_time'] =time();
     }

}