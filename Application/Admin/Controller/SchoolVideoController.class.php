<?php
/**
 * SchoolVideo
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Controller;
class SchoolVideoController extends CommonController {
	public function filter(&$map){
		if(!empty($_REQUEST['category'])){
			$map['_string'] = 'FIND_IN_SET(' . $_REQUEST['category']. ',category)';
			unset($map['category']);
		}
		if (!empty($_REQUEST['guests'])){
			unset($map['guests']);
		}
	}

	public function _join(&$data){
		$bol =array('<b style="color: red">否</b>','<b style="color: green">是</b>','<b style="color: green">已上传</b>');
		foreach ($data as $key=>$value){
			$data[$key]['vip'] = $bol[$value['is_vip']];
			$data[$key]['recommend'] = $bol[$value['is_recommend']];
			$data[$key]['hot'] = $bol[$value['is_hot']];
			if(!empty($value['big_cover_url'])){
				$data[$key]['is_big_cover'] = $bol[2];
			}
		}
		method_exists($this, 'get_guest_name') && $this->get_guest_name($data);

	}
	
	public function _before_index(){
		$this->_before_add();
	}
	
	public function _before_edit(){

		$this->_before_add();
	}
	
	public function _before_add(){
		$this->token = $this->qiniu('crmpub', 'college/cover');
		$this->gudests = M('SchoolGuests')->where(array('status'=>1))->select();
		$this->category = M('SchoolCate')->where(array('type'=>1,'status'=>1))->select();
		$this->charge_standard = M('VideoChargeStandard')->where(array('status'=>1))->select();
		//公开课花絮列表
		$this->course = $this->get_course_list();
		//金熊奖花絮列表
		//$this->gold_match = $this->get_gold_award();
	    //金熊奖栏目信息列表
		$this->gold_awards = M('GoldAwards')->where(array('status'=>1))->select();

	}
	
	public function _before_insert(){
		empty($_POST['cover_url']) && $this->error('请上传封面图');
		empty($_POST['big_cover_url']) && $this->error('请上传封面大图');
		$this->_before_update();
		//$_POST['guests_id'] = M('SchoolGuests')->where(array('title'=>$_POST['guests']))->getField('id');
		//empty($_POST['guests_id']) && $this->error('嘉宾不存在');
		!empty($_POST['charge_standard']) ? $_POST['charge_standard'] : "";
		$str_cate_id = !empty($_POST['category']) ? $_POST['category'] : "";
		$_POST['cate_title'] = $this->get_cate_name($str_cate_id);
		$_POST['create_time'] = time();
		$_POST['update_time'] = time();
		$_POST['status'] = $_POST['conserve']==1 ? 0 : 1;

	}

	//公开课表单信息验证
	public function check_course_info(){
		empty($_POST['course_city']) && $this->error('公开课的城市不能为空！');
		empty($_POST['course_date']) ? $this->error('公开课的开课日期不能为空！') : strtotime($_POST['course_date']);
		empty($_POST['course_type']) && $this->error('请选择该视频是花絮还是子视频！');
		$_POST['match_date'] = 0;
		$_POST['match_type'] = 0;
		$_POST['gold_award_id'] = 0;
		$_POST['match_level'] = 0;
	}

	//金熊奖表单信息验证
	public function check_gold_award_info(){
		empty($_POST['match_date']) ? $this->error('金熊奖比赛的举办时间不能为空！') : strtotime($_POST['course_date']);
		empty($_POST['match_type']) && $this->error('请选择该视频是花絮还是子视频！');
		empty($_POST['gold_award_id']) && $this->error('请选择金熊奖基本信息！');
		if($_POST['match_type']==2){
			empty($_POST['match_level']) && $this->error('请为该金熊奖子视频选择比赛阶段！');
		}
		$_POST['course_city'] = '';
		$_POST['course_date'] = 0;
		$_POST['course_type'] = 0;
		$_POST['course_parent_id'] = 0;
	}

	//获取视频分类名称
	public function get_cate_name($str_cate_id){
		$str_name = "";
		if (!empty($str_cate_id)){
			$arr_cate_id = explode(',',$str_cate_id);
			$where['id'] =array('in',$arr_cate_id);
			$arr_name =M('SchoolCate')->where($where)->select();
			foreach ($arr_name as $key=>$value){
				$arr_cate_name[] = $value['title'];
			}
			$str_name = implode(',',$arr_cate_name);
		}

		return $str_name;
	}

	public function _before_update(){
		//公开课和金熊奖数据校验（根据类型，将非选中类型的数据清空）
		$this->checkData();
		!$this->_checkVideo($_POST['url']) && $this->error('视频不存在，请检查');
		//empty($_POST['guests_id']) && $this->error('请选择嘉宾');
		$down = $this->_privateDownloadUrl('http://7o4zdo.com2.z0.glb.qiniucdn.com/' . $_POST['url'] . '?avinfo');
		$ret = curl_get(str_replace(' ', '%20', $down));
		$_POST['times'] = format_duration($ret['format']['duration']);
		//公开课表单信息验证
		in_array(4,$_POST['category']) && $this->check_course_info();
		//金熊奖表单信息验证
		in_array(3,$_POST['category']) && $this->check_gold_award_info();
		(in_array(4,$_POST['category']) && in_array(3,$_POST['category'])) && $this->error('金熊奖和公开课不能同时选中！');
		$_POST['category'] = empty($_POST['category']) ? '' : implode(',', $_POST['category']);
		$_POST['charge_standard'] = empty($_POST['charge_standard']) ? '' : implode(',', $_POST['charge_standard']);
		$str_cate_id = !empty($_POST['category']) ? $_POST['category'] : "";
		$_POST['cate_title'] = $this->get_cate_name($str_cate_id);
		$_POST['update_time'] =time();
		$_POST['status'] = $_POST['conserve']==1 ? 0 : 1;
		!empty($_POST['course_date']) ? strtotime($_POST['course_date']) : 0;
		!empty($_POST['match_date']) ? strtotime($_POST['match_date']) : 0;

	}

	//公开课和金熊奖数据校验
	public function checkData(){
		if (in_array(3,$_POST['category'])) {
			!empty($_POST['course_city']) &&  $_POST['course_city'] = '';
			!empty($_POST['course_date']) &&  $_POST['course_date'] = 0;
			!empty($_POST['course_type']) &&  $_POST['course_type'] = 0;
			!empty($_POST['course_parent_id']) &&  $_POST['course_parent_id'] = 0;
		}elseif (in_array(4,$_POST['category'])){
			!empty($_POST['match_date']) &&  $_POST['match_date'] = 0;
			!empty($_POST['match_type']) &&  $_POST['match_type'] = 0;
			!empty($_POST['match_parent_id']) &&  $_POST['match_parent_id'] = 0;
			!empty($_POST['match_level']) &&  $_POST['match_level'] = 0;
			!empty($_POST['gold_award_id']) &&  $_POST['gold_award_id'] = 0;
		}
	}

	private function _privateDownloadUrl($baseUrl, $expires = 3600){
        $deadline = time() + $expires;

        $pos = strpos($baseUrl, '?');
        if ($pos !== false) {
            $baseUrl .= '&e=';
        } else {
            $baseUrl .= '?e=';
        }
        $baseUrl .= $deadline;

        $token = $this->sign($baseUrl);
        return "$baseUrl&token=$token";
    }

    protected function sign($data){
        $hmac = hash_hmac('sha1', $data, C('QINIU_SK'), true);
        return C('QINIU_AK') . ':' . $this->base64_urlSafeEncode($hmac);
    }

    protected function base64_urlSafeEncode($data){
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }

	private function _checkVideo($url){

		// 使用七牛获取资源信息(stat)方法 (http://developer.qiniu.com/docs/v6/api/reference/rs/stat.html)
		$bucket = 'halocollege';
		// EncodedEntryURI
		$entry = $bucket.':'.$url;
		$find = array('+', '/');
		$replace = array('-', '_');
		$encodedEntryURI = str_replace($find, $replace, base64_encode($entry));

		// access token
		$baseurl = '/stat/'.$encodedEntryURI;
		$token = $this->_qiniu_token($baseurl."\n");

		$header = array();
		$header[] = 'Authorization:'.'QBox '.$token;
		$result = curl_get('http://rs.qiniu.com'.$baseurl, $header);
		
		return $result['fsize'] > 0;
	}

	private function _qiniu_token($signingStr){
		$accessKey = 'm_bQ6vCqK-1n_myddynLMQxg0rxw3YqRptv5D7_i';
		$secretKey = 'EH7AQcudIK47egCwYGzrSFVnutvuCYedfr0Lyl3d';

		$find = array('+', '/');
		$replace = array('-', '_');
		$sign = hash_hmac('sha1', $signingStr, $secretKey, true);
		$encodedSign  = str_replace($find, $replace, base64_encode($sign));

		return $accessKey . ':' . $encodedSign;
	}


	public function search(){
		$guests = I('guests');

		$list = M('SchoolGuests')->where(array('title'=>array('like', '%'. $guests .'%')))->select();
		$data['data'] = $list;
		$data['message'][0] = empty($list) ? 'FORUM:searchforum.notfound' : 'FORUM:searchforum.success';
		$data['refresh'] = false;
		$data['state']  = empty($list) ? 'fail' : 'success';
		$this->ajaxReturn($data);
	}

    //获取嘉宾名字
	public function get_guest_name (&$data){
		foreach ($data as $key=>$value){
			$guests_id[] =$value['guests_id'];
		}
		if (!empty($guests_id)){
			$where['id'] = array('in',$guests_id);
			$guests_name = M('SchoolGuests')->where($where)->getField('id as guest_id,title as guest_name');
		}
		foreach ($data as $key=>$value){
			$data[$key]['guests'] = $guests_name[$value['guests_id']];
		}
	}

    //修改热门、会员、推荐状态
	public function change_status (){
		$id = I('id');
		$status = I('is_status');
		$remark  = I('remark');
		$video = M('SchoolVideo')->where(array('id'=>$id,'status'=>1))->find();
		if (empty($video) || $status==''){
			$this->error('参数错误！');
		}else{
			$change_status = $status==1 ? 0 : 1 ;
			switch ($remark){
				case 'is_hot':
					$video['is_hot'] = $change_status;
					break;
				case 'is_recommend':
					$video['is_recommend'] = $change_status;
					break;
				case 'is_vip':
					$video['is_vip'] = $change_status;
					break;
				default:
					$this->error('参数错误！');
					break;
			}
			$result = M('SchoolVideo')->save($video);
			if ($result!==false){
				$this->success('状态修改成功！');
			}else{
				$this->error('状态修改失败！');
			}
		}
	}

    //获取公开课视频花絮列表
	public function get_course_list(){
		$where['category'] = array('in',array(4));
		$where['course_type'] = 1;
		$where['status'] = 1;
		$where['id'] = array('neq',$_REQUEST['id']);
		$curses = M('SchoolVideo')->where($where)->field('id,title,course_city,course_date')->select();

		return $curses;
	}

	//获取金熊奖视频花絮列表
	public function get_gold_award(){
		$where['category'] = array('in',array(3));
		$where['course_type'] = 1;
		$where['status'] = 1;
		!empty($_REQUEST['id']) && $where['id'] = array('neq',$_REQUEST['id']);
		$gold_ward = M('SchoolVideo')->where($where)->field('id,title,course_city,course_date')->select();

		return $gold_ward;
	}
}