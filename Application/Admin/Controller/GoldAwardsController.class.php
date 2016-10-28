<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/27
 * Time: 18:43
 */

namespace Admin\Controller;


class GoldAwardsController extends CommonController{

    public function _join(&$data){

    }
    public function _before_edit(){

        $this->_before_add();
    }

    public function _before_add(){
        $this->token = $this->qiniu('crmpub', 'college/cover');

    }

    public function _before_insert(){
        empty($_POST['title']) && $this->error('请填写金熊奖标题');
        empty($_POST['cover_url']) && $this->error('请上传封面图');
        empty($_POST['brief']) && $this->error('请编辑金熊奖简介');
        empty($_POST['date']) && $this->error('请填写金熊奖日期');
        $_POST['date'] = strtotime($_POST['date']);
        $_POST['status'] = 1;

    }

    public function _before_update(){
        empty($_POST['title']) && $this->error('请填写金熊奖标题');
        empty($_POST['brief']) && $this->error('请编辑金熊奖简介');
        empty($_POST['date']) && $this->error('请填写金熊奖日期');
        $_POST['date'] = strtotime($_POST['date']);
    }

}