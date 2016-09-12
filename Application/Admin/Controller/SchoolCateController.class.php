<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/12
 * Time: 11:21
 */

namespace Admin\Controller;


class SchoolCateController extends CommonController{
    public function _before_insert(){
        empty($_POST['title']) && $this->error('请填写分类名称！');
        $_POST['create_time']=time();
        $_POST['update_time']=time();
        $_POST['status']=1;
    }

}