<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/19
 * Time: 17:41
 */

namespace Admin\Controller;


class SchoolWeddingCategoryController extends CommonController{
    public function _before_insert(){
        empty($_POST['name']) && $this->error('请填写分类名称！');
        $_POST['create_time']=time();
        $_POST['update_time']=time();
        $_POST['status']=1;
    }}