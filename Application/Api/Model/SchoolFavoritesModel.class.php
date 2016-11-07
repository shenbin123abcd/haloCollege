<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/7
 * Time: 11:31
 */

namespace Api\Model;
use Think\Model;

class SchoolFavoritesModel extends Model{

    /**
     * 获取视频列表
     * @param  array   $map   过滤条件
     * @param  string  $type  列表类型 new最新，hot热门，score评分
     * @param  integer $limit 获取数量
     * @return [type]         列表
     */
    public function getList( $limit = 12 ,$user) {
        $page = I('page');
        $map = array('uid'=>$user['uid']);
        $total = $this->where($map)->count();
        $list = $this->where($map)->limit($limit)->page($page)->order('id DESC')->select();

        return array('total'=>$total, 'list'=>empty($list) ? array() : $this->_format($list));
    }
    

    private function _format($list){
        foreach ($list as $key => $value) {
            $vid[] = $value['vid'];
        }
        //$guests_id = array_unique($guests_id);
        //$guests = M('SchoolGuests')->where(array('id'=>array('in', $guests_id)))->getField('id, title, position');
        
        $video = D('SchoolVideo')->join('wtw_school_guests AS g ON g.id = wtw_school_video.guests_id')->where(array('wtw_school_video.id'=>array('in', $vid)))->getField('wtw_school_video.id AS video_id, wtw_school_video.title AS video_title, wtw_school_video.views AS video_views, wtw_school_video.cover_url AS video_cover, wtw_school_video.times AS video_times,wtw_school_video.cate_title,wtw_school_video.is_vip,wtw_school_video.big_cover_url,wtw_school_video.category,wtw_school_video.charge_standard,wtw_school_video.create_time,g.id AS guests_id,g.title AS guests_title,g.position AS guests_position,g.avatar_url AS guests_avatar');
        //获取公开课的和培训营课程的分类和收费信息
        $video = D('SchoolVideo')->get_course_info ($video);

        $temp = array();
        foreach ($list as $key => $value) {
            $video_temp = $video[$value['vid']];
            $video_temp['video_cover'] = C('IMG_URL') . $video_temp['video_cover'] . '!240x160';
            $video_temp['guests_avatar'] = C('IMG_URL') . $video_temp['guests_avatar'];
            $temp[] = $video_temp;
        }

        return array_values($temp);
    }

    // 收藏
    public function act($vid,$uid){
        $uid = $uid;
        $map = array('vid'=>$vid,'uid'=>$uid);

        // 检查
        $count = $this->where($map)->count();

        if($count){
            $this->where($map)->delete();
        }else{
            $this->add(array('vid'=>$vid, 'uid'=>$uid, 'create_time'=>time()));
        }
    }
}