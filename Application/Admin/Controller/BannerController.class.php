<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 15:29
 */

namespace Admin\Controller;
use Think\Controller;

class BannerController extends CommonController{
    public function _before_add(){
        $this->token = $this->qiniu('crmpub','Banner');

    }

    public function _before_insert(){
        empty($_POST['title']) && $this->error('请填写Banner标题！');
        empty($_POST['desc']) && $this->error('请填写Banner描述！');
        empty($_POST['type']) && $this->error('请填写Banner类型！');
        $_POST['create_time']=time();
        $_POST['update_time']=time();
        $_POST['status']=1;


    }

    public function _before_edit(){
        $this->_before_add();
        $attach = M('Attach')->where(array('record_id'=>I('id'), 'module'=>'Banner','status'=>1))->field('id,savename')->select();
        foreach ($attach as $key => $value) {
            $attach[$key]['src'] = 'http://7xopel.com2.z0.glb.clouddn.com/' . $value['savename'];
        }
        $this->attach = $attach;
    }

    //删除图片--封面
    public function attach_delete(){
        $model = M('Attach');
        $arrach_id = I('id');
        $where['id'] = $arrach_id;
        $where['module'] = 'Banner';
        $attach = $model->where($where)->find();
        if(empty($attach)){
            $this->error('该图片不存在了！');
        }
        if($attach['status']==0){
            $this->error('该图片已经被移除了！');
        }
        $attach['status'] =0;
        $result = $model->save($attach);
        if($result!==false){
            $this->success('图片移除成功！');
        }
    }

}