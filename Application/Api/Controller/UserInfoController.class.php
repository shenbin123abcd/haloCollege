<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/20
 * Time: 17:33
 */

namespace Api\Controller;

use Think\Controller;
class UserInfoController extends CommonController{
    protected $module_auth = 0;
    protected $action_auth = array('userInfoInsert','getUserInfo');

    /**
     * 用户信息添加
    */
    public function userInfoInsert() {
        $model = D('Userinfo');
        $uid = $this->user['uid'];
        $wsq_id = $this->user['wsq']->uid;
        $wsq_name = $this->user['username'];
        $access_token = $this->user['wsq']->access_token;
        $where['uid'] = $uid;
        $where['status'] = 1;
        $info = $model->where($where)->find();
        if (!empty($info)) {
            $info['truename'] = I('truename');
            $info['sex'] = I('sex');
            $info['wechat'] = I('wechat');
            $info['province'] = I('province');
            $info['city'] = I('city');
            $info['region'] = I('region');
            $province = $info['province'];
            $city = $info['city'];
            $region = $info['region'];
            $province_title = M('Region')->where("region_id=$province")->field('region_id,region_name')->find();
            $city_title = M('Region')->where("region_id=$city")->field('region_id,region_name')->find();
            $region_title = M('Region')->where("region_id=$region")->field('region_id,region_name')->find();
            $info['province_title'] = $province_title['region_name'];
            $info['city_title'] = $city_title['region_name'];
            $info['region_title'] = $region_title['region_name'];
            $info['company'] = I('company');
            $info['position'] = I('position');
            $info['brief'] = I('brief');
            $info['update_time'] = time();
            if ($model->create($info)) {
                $result = $model->save($info);
                if ($result !== false) {
                    $result = $model->getMicroToken($info, $access_token, $wsq_name);
                    $this->success('个人信息保存成功！',array('wsq_id'=>$result['id']));
                } else {
                    $this->error('个人信息保存失败！');
                }
            } else {
                $this->error($model->getError());
            }
        }
        $data['uid'] = $uid;
        $data['wsq_id'] = $wsq_id;
        $data['truename'] = I('truename');
        $data['sex'] = I('sex');
        $data['wechat'] = I('wechat');
        $data['province'] = I('province');
        $data['city'] = I('city');
        $data['region'] = I('region');
        $data['company'] = I('company');
        $data['position'] = I('position');
        $data['brief'] = I('brief');
        $province = $data['province'];
        $city = $data['city'];
        $region = $data['region'];
        $province_title = M('Region')->where("region_id=$province")->field('region_id,region_name')->find();
        $city_title = M('Region')->where("region_id=$city")->field('region_id,region_name')->find();
        $region_title = M('Region')->where("region_id=$region")->field('region_id,region_name')->find();
        $data['province_title'] = $province_title['region_name'];
        $data['city_title'] = $city_title['region_name'];
        $data['region_title'] = $region_title['region_name'];
        if ($model->create($data)) {
            $id = $model->add();
            if ($id) {
                $result = $model->getMicroToken($data, $access_token, $wsq_name);
                $this->success('用户信息添加成功！',array('wsq_id'=>$result['id']));
            } else {
                $this->error('用户信息添加失败！');
            }
        } else {
            $this->error($model->getError());
        }
    }

    /**
     * 获取个人信息
    */
    public function getUserInfo(){
        $uid = $this->user['uid'];
        $where['uid'] = $uid;
        $where['status'] = 1;
        $info = D('Userinfo')->where($where)->find();
        if(empty($info)){
            $data['info'] = array();
            $this->success('个人信息为空，请去完善！',$data);
        }
        $data['info'] = $info;
        $this->success('success',$data);

    }

    

}