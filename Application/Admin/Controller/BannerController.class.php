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
    public $banner_type = array('1'=>'H5页面','2'=>'全屏播放','3'=>'视频详情','4'=>'热文文章');

    public function _join(&$data){
        foreach ($data as $key=>$value){
            $url = 'http://7xopel.com2.z0.glb.clouddn.com/'.$value['banner_url'];
            $data[$key]['img'] = '<img'.' '.'style='.'width:300px;height:100px'.' '.'src='.$url.' '.'/>';
            $data[$key]['type'] = $this->banner_type[$value['type']];
        }


    }

    public function _before_add(){
        $this->type=$this->banner_type;
        $this->token = $this->qiniu('crmpub', 'college/banner');

    }

    public function _before_insert(){
        empty($_POST['banner_url']) && $this->error('请上传banner图片');
        empty($_POST['title']) && $this->error('请填写Banner标题！');
        empty($_POST['desc']) && $this->error('请填写Banner描述！');
        empty($_POST['type']) && $this->error('请填写Banner类型！');
        $_POST['type'] = intval( $_POST['type']);
        empty($_POST['redirect_url_id']) && $this->error('请填写跳转地址或者id！');
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