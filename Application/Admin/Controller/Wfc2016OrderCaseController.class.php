<?php
/**
 * Wfc2015Tickets
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Controller;
class Wfc2016OrderCaseController extends CommonController {
	public function _initialize(){
		parent::_initialize();
		
	}

	public function _join(&$data){
		foreach ($data as $key => $value) {
			$data[$key]['is_over'] = $value['is_over'] > 0 ? '<span style="color:red">已领取</span>' : '<span style="color:#ccc">未领取</span>';
			
			$data[$key]['status'] = $value['status'] == 1 ? '<span style="color:red">已支付</span>' : '<span style="color:#ccc">未支付</span>';
			$uid[] = $value['record_id'];
		}

		$user = M('wechat_auth')->where(array('id'=>array('in', $uid)))->getField('id, nickname');
		$address = M('wfc2016_address')->where(array('uid'=>array('in', $uid)))->getField('uid, name, phone,province_title,city_title,region_title,address');
		
		foreach ($data as $key => $value) {
			$data[$key]['nickname'] = $user[$value['record_id']];
			$data[$key]['name'] = $address[$value['record_id']]['name'];
			$data[$key]['phone'] = $address[$value['record_id']]['phone'];
			$data[$key]['province_title'] = $address[$value['record_id']]['province_title'];
			$data[$key]['city_title'] = $address[$value['record_id']]['city_title'];
			$data[$key]['region_title'] = $address[$value['record_id']]['region_title'];
			$data[$key]['address'] = $address[$value['record_id']]['address'];
		}
	}

	public function filter(&$where){
		$_GET['order_no'] && $where['order_no'] = array('like', '%' . $_GET['order_no'] . '%');
		$_GET['transaction_id'] && $where['transaction_id'] = array('like', '%' . $_GET['transaction_id'] . '%');

		
	}

	public function _before_index(){
		C('LIST_ROWS', 200);
	}

	public function _before_edit(){
		$uid = $this->model()->where(array('id'=>I('id')))->getField('record_id');
		$address = $this->get_address($uid);
		$this->address = $address['name']. ' ' . $address['phone']. ' ' . $address['province_title'] . $address['city_title'] . $address['region'] . ' ' . $address['address'];
		$this->phone = $address['phone'];
		$this->auth = M('wechat_auth')->where(array('id'=>array('in', $uid)))->find();
	}

	public function _before_update(){
		$express = I('express');
		$express_number = I('express_number');
		$id = I('id');
		$is_msg = I('is_msg');

		$is_sms = I('is_sms');
		$phone = I('phone');
		
		if ($is_sms == 0 && $is_msg) {
			send_msg( $phone, array($express, $express_number), 111600, '8a48b551488d07a80148a5a1ea330a06' );
			$this->model()->where(array('id'=>$id))->setField('is_sms', 1);
		}
	}

	function get_address($uid){
		// 检查是否已经存在
		$address = M('Wfc2016Address')->field('name,phone,province,province_title,city,city_title,region,region_title,address')->where(array('uid'=>$uid))->find();

		

		return $address;
	}

	public function test(){
		$list = $this->model()->where(array('status'=>1,'module'=>'daoju',))->field('goods_name,num,pay_time,price')->select();
		dump($list);
		$sum = 0;
		foreach ($list as $key => $value) {
			$sum += $value['price'];
		}
		echo $sum;
	}
}