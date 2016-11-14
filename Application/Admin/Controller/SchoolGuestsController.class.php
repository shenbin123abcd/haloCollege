<?php
/**
 * SchoolGuests
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Controller;
class SchoolGuestsController extends CommonController {
	public function _join(&$data){
	//标识头像没上传的用户
		$data = $this->is_headimg($data);
	//获取公司logo
		$data = $this->get_company_logo($data);
    //微博地址链接
		$data = $this->micro_blog_link($data);
	}

	public function _before_edit(){
		$this->_before_add();
	}
	
	public function _before_add(){
		$this->token = $this->qiniu('crmpub', 'college/avatar');
	}
	

	/**
	 * 标识用户头像是否上传
	*/
	public function is_headimg($data){
		foreach ($data as $key=>$value){
			if($value['avatar_url']==''){
				$data[$key]['is_headimg'] = '<b style="color: red">未上传</b>';
			}else{
				$url = 'http://7xopel.com2.z0.glb.clouddn.com/'.$value['avatar_url'];
				$data[$key]['is_headimg'] = '<img'.' '.'style='.'width:100px;height:100px'.' '.'src='.$url.' '.'/>';
			}
			if ($value['position']==''){
				$data[$key]['position'] = '<b style="color: red">无职务</b>';
			}
		}

		return $data;
	}
	

	/**
	 * 公司搜索
	 */
	public function getCompanyList() {
		$name = I('company');
		$where = array('name' =>$name);
		$result = $this->company($where);
		if (!empty($result['data']['data'])) {
			foreach ($result['data']['data'] as $key => $value) {
				$list[] = $value;
			}
		} else {
			$list = array();
		}
		$data['data'] = $list;
		$data['message'][0] = empty($list) ? 'FORUM:searchforum.notfound' : 'FORUM:searchforum.success';
		$data['refresh'] = false;
		$data['state']  = empty($list) ? 'fail' : 'success';
		$this->ajaxReturn($data);
	}

	public function company($data) {
		$api = C('AUTH_API_URL') . 'company?' . http_build_query($data);
		$result = curl_get($api, $data);
		return $result;
	}

	//通过公司id获得公司详情
	public function company_id($company_id) {
		$api = C('AUTH_API_URL') . 'company/' .$company_id;
		$result = curl_get($api);
		return $result;
	}

	/**
	 * 获取公司logo及公司id未关联的做标记
	*/
	public function get_company_logo($data){
		foreach ($data as $key=>$value){
			$company_ids[]=$value['company_id'];
		}
		$company_ids = array_unique($company_ids);
		if (!empty($company_ids)){
			foreach ($company_ids as $value){
				$result = $this->company_id($value);
				$list[$value] =$result['data']['logo'][0]['file_path'];
			}
			foreach ($data as $key=>$value){
				$url = 'http://7ktsyl.com2.z0.glb.qiniucdn.com/';
				$data[$key]['company_logo'] = !empty($list[$value['company_id']]) ? '<img'.' '.'style='.'width:100px;height:100px'.' '.'src='.$url.$list[$value['company_id']].' '.'/>' : '<b style="color: red">未上传</b>';
			//	未关联公司id的公司名做标记
				$data[$key]['company'] = $value['company_id']==0 ? '<b'.' '.'style='.'color:red>'.$value['company'].'</b>' : $value['company'];
			}

		}

		return $data;
	}

	/**
	 * 微博地址超链接
	*/
	public function micro_blog_link($data){
		foreach ($data as $key=>$value){
			$url = $value['micro_blog_address'] ? $value['micro_blog_address'] : '';
			$data[$key]['micro_blog_address'] = $url ? '<a href='.$url.' '.'target=_blank'.'>'.$url.'</a>' : '';
		}

		return $data;
	}

}