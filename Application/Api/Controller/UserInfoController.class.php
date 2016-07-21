<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/20
 * Time: 17:33
 */

namespace Admin\Controller;

use Think\Controller;
class UserInfoController extends CommonController{
    protected $module_auth = 0;
    protected $action_auth = array('userInfoInsert','getUserInfo');

    //用户信息添加
    public function userInfoInsert(){
        $uid = $this->user['uid'];
        $data['uid'] = $uid;
        $data['username'] = I('username');
        $data['sex'] = I('sex');
        $data['wechat'] = I('wechat');
        $data['province'] = I('province');
        $data['city'] = I('city');
        $data['region'] = I('region');
        $data['company'] = I('company');
        $data['position'] = I('position');
        $data['brief'] = I('brief');
        $model = D('UserInfo');
        if($model->create($data)){
            $id = $model->add();
            if($id){
                $this->success('用户信息添加成功！');
            }else{
                $this->error('用户信息添加失败！');
            }
        }else{
            $model->getError();
        }
    }

    // 获取个人信息
    public function getUserInfo(){
        $token = make_qiniu_token('crmpub',CONTROLLER_NAME,'http://koala-college.weddingee.com/public/qiniuUpload');
        $uid = $this->user['uid'];
        $where['uid'] = $uid;
        $where['status'] = 1;
        $info = D('UserInfo')->where($where)->find();
        if(empty($info)){
            $data['info'] = array();
            $this->success('个人信息为空，请去完善！');
        }
        $data['info'] = $info;
        $data['token'] = $token;
        $this->success('success',$data);

    }

    

}