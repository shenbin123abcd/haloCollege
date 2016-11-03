<?php
namespace Ke\Controller;

use Think\Controller;

class UcController extends CommonController {
    /**
     * 获取微信用户信息
     */
    /*protected function _getWechatUser(){

        $halobear = cookie('halobear');
        $this->user = $this->wcache($halobear);

        if (empty($this->user) && !in_array(ACTION_NAME, array('wechat', 'notifyn', 'booknotifyn', 'test', 'getWechat'))) {
            cookie('halobear', null, -86400);
            $url = 'http://ke.halobear.com/courses/wechat?url=' . urlencode('http://ke.halobear.com' . $_SERVER['REQUEST_URI']);
            if (IS_AJAX) {
                $this->ajaxReturn(array('iRet'=>-1,'info'=>'No authorization token was found', 'data'=>$url));
            }else{
                redirect($url);
            }
        }
    }*/

    public function book() {
        $this->assign('is_address', get_address($this->user['id']) ? 1 : 0);
        $this->display();
    }

    // 案例支付成功记录
    public function caseRecord(){
        $list = M('wfc2016_order_case')->where(array('record_id'=>$this->user['id'], 'status'=>1))->field('order_no,goods_name,goods_id,goods_subtitle,goods_cover,goods_url,type,price,module,spec,num,pay_time')->order('id DESC')->select();
        foreach ($list as $key => $value) {
            // 样片
            if ($value['module'] == 'book') {
                $list[$key]['type'] = '';
                $list[$key]['send_date'] = date('Y-m-d',$value['pay_time'] + 2*86400);
            }
        }

        $list = $list ? $list : array();
        $this->assign('list', json_encode($list));

        $this->display();
    }

    // 收货的地址
    public function addAddress(){
        // 检查是否有快递地址
        if (!get_address($this->user['id'])) {
            $data['uid'] = $this->user['id'];
            $data['name'] = I('name');
            $data['phone'] = I('phone');
            $data['province'] = I('province');
            $data['city'] = I('city');
            $data['region'] = I('region');
            $province = M('Region')->where(array('region_id'=>$data['province']))->field('region_name')->find();
            $city =  M('Region')->where(array('region_id'=>$data['city']))->field('region_name')->find();
            $region = M('Region')->where(array('region_id'=>$data['region']))->field('region_name')->find();
            $data['province_title']=$province['region_name'];
            $data['city_title']=$city['region_name'];
            $data['region_title']=$region['region_name'];
            $data['address'] = I('address');

            M('Wfc2016Address')->add($data);
        }

        $this->success([],'提交成功');
    }

    // 获取地区信息
    public function getRegion(){
        $city = intval(I('city'));
        $data = M('Region')->where(array('parent_id'=>$city))->field('region_id,region_name')->select();
        if (empty($data)) {
            $data = M('Region')->where(array('region_id'=>$city))->field('region_id,region_name')->select();
        }

        $this->success($data);
    }
}