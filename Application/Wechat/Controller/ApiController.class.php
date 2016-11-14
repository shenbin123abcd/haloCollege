<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/11/11
 * Time: 14:54
 */

namespace Wechat\Controller;

class ApiController extends CommonController
{
    public function getUser(){
        $openid = I('openid');
        $unionid = I('unionid');

        $where = [];

        if (!empty($openid)){
            $where['openid'] = $openid;
        }elseif (!empty($unionid)){
            $where['unionid'] = $unionid;
        }

        $user = M('WechatAuth')->where($where)->field('ck,cv', 1)->find();

        $user ? $this->success($user) : $this->error();
    }
}