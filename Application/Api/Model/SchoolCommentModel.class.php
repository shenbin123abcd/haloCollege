<?php
namespace Api\Model;
use Think\Model;
// 视频评论模型
class SchoolCommentModel extends Model {
	//自动验证
	protected $_validate = array(
		array('vid', 'require', '视频id错误！', self::MUST_VALIDATE, 'regex', self:: MODEL_INSERT),
		array('vid', 'checkVid', '视频id错误！', self::MUST_VALIDATE, 'callback', self:: MODEL_INSERT),
		array('content', 'require', '评论内容不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_INSERT),
		array('score', 'checkScore', '分数值错误！', self::MUST_VALIDATE, 'callback', self:: MODEL_INSERT)
	);

	//自动完成
	protected $_auto = array(
		array('create_time', 'time', self::MODEL_INSERT, 'function'),
		array('update_time', 'time', self::MODEL_BOTH, 'function'),
		array('status', '1', self::MODEL_INSERT, 'string'),
		array('ip', 'get_client_ip', self::MODEL_INSERT, 'function'),
		array('uid', 'getUid', self::MODEL_INSERT, 'callback'),
		array('username', 'getUsername', self::MODEL_INSERT, 'callback'),
	);

	// 检查视频ID的合法性
	protected function checkVid(){
		$vid = I('vid');

		return M('SchoolVideo')->where(array('id'=>$vid, 'status'=>1))->count() ? true : false;
	}

	// 检查分数合法性
	protected function checkScore(){
		$score = I('score');

		return ($score <= 5 && $score > 0) ? true : false;
	}

	// 获取用户
	protected function getUid(){
		return D('SchoolAccount')->id;
	}

	// 获取用户
	protected function getUsername(){
		return D('SchoolAccount')->username;
	}


	/**
	 * 获取列表
	 * @param  array   $map   过滤条件
	 * @param  integer $limit 获取数量
	 * @return [type]         列表
	 */
	public function getList( $map = array(), $limit = 12 ) {
		$map['status'] = 1;
		$page = intval(I('page'));
		// $page = min(intval(I('page')), 1);
		$total = $this->where($map)->count();
		//联表查询个人信息，查询条件格式要变化
		$whereMap['wtw_school_comment.vid'] =$map['vid'];
		$whereMap['wtw_school_comment.status'] =$map['status'];
		$list = $this->where($whereMap)->join('left join wtw_userinfo on wtw_school_comment.uid=wtw_userinfo.uid')->limit($limit)->page($page)->order('id DESC')
			->field('wtw_school_comment.id,wtw_school_comment.uid,wtw_school_comment.username,wtw_school_comment.content,wtw_school_comment.score,wtw_school_comment.create_time,wtw_userinfo.position')->select();
		foreach ($list as $key => $value) {
			$list[$key]['avatar'] = get_avatar($value['uid']);
		}
		return array('total'=>$total, 'list'=>empty($list) ? array() : $list);
	}

	/**
	 * 获取评论列表（最新）
	 */
	public function getCommentList($page,$per_page,$vid){
		$url = 'http://college-api.halobear.com/v1/public/getUserInfo';
		$where['status']=1;
		$where['vid']=$vid;
		$total = $this->where($where)->count();
		$list = $this->where($where)->page($page,$per_page)->order('id DESC')->field('id,uid,username,content,score,create_time')->select();
		foreach ($list as $key => $value) {
			$list[$key]['avatar'] = get_avatar($value['uid']);
		}
		//获取用户职位等信息
		foreach ($list as $key=>$value){
			$uid_arr[] = $value['uid'];
		}
		$uid_arr_unique = array_unique($uid_arr);

		if(!empty($uid_arr_unique)){
			$uid = json_encode($uid_arr_unique);
			$data =array(
				'uid'=>$uid,
			);
			$result = curl_post($url,$data);
			$userInfo = $result['data']['userInfo'];
			foreach ($list as $key_list=>$value_list){
				$list[$key_list]['position'] = '';
				foreach ($userInfo as $key_userInfo=>$value_userInfo){
					if($value_list['uid']==$value_userInfo['uid']){
						$list[$key_list]['position'] = $value_userInfo['position'];
					}
				}
			}
		}
		return array('total'=>$total, 'list'=>empty($list) ? array() : $list);
	}

	protected function _after_insert($data, $option){
		// 计算评分
		$score = $this->where(array('vid'=>$data['vid']))->avg('score');
		M('SchoolVideo')->where(array('id'=>$data['vid']))->setField('score', $score);
	}

	// 我的评论
	public function my($limit = 12, $is_page = 0){
		$page = I('page');
		// $page = !empty($page) ?　$page : 1;
		$map = array('uid'=>$this->getUid());
		$total = $this->where($map)->count();
		$list = $this->where($map)->order('id DESC')->page($page)->limit($limit)->select();

		foreach ($list as $key => $value) {
			$vid[] = $value['vid'];
		}

		$guests_id = array_unique($guests_id);
        $guests = M('SchoolGuests')->where(array('id'=>array('in', $guests_id)))->getField('id, title, position');

        $video = D('SchoolVideo')->join('wtw_school_guests AS g ON g.id = wtw_school_video.guests_id')->where(array('wtw_school_video.id'=>array('in', $vid)))->getField('wtw_school_video.id AS video_id, wtw_school_video.title AS video_title, wtw_school_video.views AS video_views, wtw_school_video.cover_url AS video_cover, wtw_school_video.times AS video_times,g.id AS guests_id,g.title AS guests_title,g.position AS guests_position,g.avatar_url AS guests_avatar');

        $temp = array();
        foreach ($list as $key => $value) {
            $video_temp = $video[$value['vid']];
            $video_temp['video_cover'] = C('IMG_URL') . $video_temp['video_cover'] . '!240x160';
            $video_temp['guests_avatar'] = C('IMG_URL') . $video_temp['guests_avatar'];
            $video_temp['comment_id'] = $value['id'];
            $video_temp['comment_content'] = $value['content'];
            $video_temp['comment_score'] = $value['score'];
            $video_temp['comment_time'] = $value['create_time'];
            $temp[] = $video_temp;
        }

        if ($is_page) {
        	return array('total'=>$total, 'list'=>empty($temp) ? array() : array_values($temp));
        }else{
        	return array_values($temp);
        }
	}
}

?>
