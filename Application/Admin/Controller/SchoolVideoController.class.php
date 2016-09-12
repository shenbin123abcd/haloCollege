<?php
/**
 * SchoolVideo
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Controller;
class SchoolVideoController extends CommonController {
	public function _join_search(&$map,&$val){
		if($val=='category'){
			$category = $_REQUEST [$val];
			$map['_string'] = 'FIND_IN_SET(' . $category. ',category)';
			unset($map[$val]);
		}
	}

	public function _join_video(&$data){
		$bol =array('否','是');
		foreach ($data as $key=>$value){
			$data[$key]['vip'] = $bol[$value['is_vip']];
			$data[$key]['recommend'] = $bol[$value['is_recommend']];
			$data[$key]['hot'] = $bol[$value['is_hot']];
		}

	}
	
	public function _before_index(){
		$this->_before_add();
	}
	
	public function _before_edit(){		
		$this->_before_add();
	}
	
	public function _before_add(){
		$this->token = $this->qiniu('crmpub', 'college/cover/');
		$this->gudests = M('SchoolGuests')->where(array('status'=>1))->select();
		$this->category = M('SchoolCate')->where(array('type'=>1))->select();
		//$this->cate1 = M('SchoolCate')->where(array('type'=>1))->select();
		//$this->cate2 = M('SchoolCate')->where(array('type'=>2))->select();
	}
	
	public function _before_insert(){
		empty($_POST['cover_url']) && $this->error('请上传封面图');
		$this->_before_update();
		$_POST['guests_id'] = M('SchoolGuests')->where(array('title'=>$_POST['guests']))->getField('id');
		empty($_POST['guests_id']) && $this->error('嘉宾不存在');
		$str_cate_id = !empty($_POST['category']) ? $_POST['category'] : "";
		$_POST['cate_title'] = $this->get_cate_name($str_cate_id);
		$_POST['create_time'] = time();
		$_POST['update_time'] = time();
		$_POST['status'] =1;
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
		!$this->_checkVideo($_POST['url']) && $this->error('视频不存在，请检查');
		//(empty($_POST['cate1']) || empty($_POST['cate2'])) && $this->error('请选择分类');
		empty($_POST['guests_id']) && $this->error('请选择嘉宾');
		$down = $this->_privateDownloadUrl('http://7o4zdo.com2.z0.glb.qiniucdn.com/' . $_POST['url'] . '?avinfo');
		$ret = curl_get(str_replace(' ', '%20', $down));
		$_POST['times'] = format_duration($ret['format']['duration']);
		$_POST['category'] = empty($_POST['category']) ? '' : implode(',', $_POST['category']);
		$str_cate_id = !empty($_POST['category']) ? $_POST['category'] : "";
		$_POST['cate_title'] = $this->get_cate_name($str_cate_id);
		$_POST['update_time'] =time();
		//$_POST['cate3'] = empty($_POST['cate3']) ? '' : implode(',', $_POST['cate3']);
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
}