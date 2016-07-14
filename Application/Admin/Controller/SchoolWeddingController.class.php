<?php

/**
 * Created by PhpStorm.
 * User: zhanghu
 * Date: 2016/6/28
 * Time: 16:55
 */
namespace Admin\Controller;

class SchoolWeddingController  extends CommonController {
    public function add(){
        $category = M('SchoolWeddingCategory')->where("status=1")->field('id,name')->select();
        $this->assign('category',$category);
        $this->display();

    }    
    
    public function _before_insert(){
        empty($_POST['headline']) && $this->error('请填写婚礼标题！');
        empty($_POST['brief']) && $this->error('请填写婚礼简介！');
        empty($_POST['category_id']) && $this->error('请选择头条分类！');
        empty($_POST['content']) && $this->error('请编辑头条内容！');
        $_POST['create_time']=time();
        $_POST['update_time']=time();
        $_POST['status']=1;
        $_POST['uid']=$this->user['id'];

    }


    /**
     * 默认编辑操作
     * @see CommonAction::edit()
     */
    public function edit(){
        $model = $this->model();
        $pk = $model->getPk();
        $data = $model->where(array($pk=>$_GET[$pk]))->find();
        empty($data) && $this->error('查询数据失败！');
        //$data['content'] = htmlspecialchars_decode($data['content']);
        $categoryName = M('SchoolWeddingCategory')->where(array('id'=>$data['category_id']))->field('name')->find();
        $where['status'] =1;
        $category = M('SchoolWeddingCategory')->where($where)->select();
        $this->assign('categoryName',$categoryName);
        $this->assign('category',$category);
        $this->assign('data',$data);
        $this->display();
    }





}