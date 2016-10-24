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
		$where = array('name' =>$name,'filter[store_id]' => 0);
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

}