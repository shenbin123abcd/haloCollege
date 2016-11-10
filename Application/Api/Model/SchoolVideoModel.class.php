<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/5
 * Time: 16:09
 */

namespace Api\Model;


use Think\Model;
use Think\Upload\Driver\Qiniu;

class SchoolVideoModel extends Model{
    /**
     * 获取视频列表
     * @param  array   $map   过滤条件
     * @param  string  $type  列表类型 new最新，hot热门，score评分
     * @param  integer $limit 获取数量
     * @return [type]         列表
     */
    public function getList( $map = array(), $type = 'new', $limit = 6 ) {
        $map['status'] = 1;
        // $page = min(intval(I('page')), 1);
        $page = I('page');
        $order = 'sort DESC,';
        switch ($type) {
            case 'index_new':
                $last_id = $this->order('id DESC')->getField('id');
                $map['id'] = array('gt', $last_id - 30);

                $cache = S('COLLEGE_NEW_LIST_SORT');
                if (empty($cache)) {
                    $cache = mt_rand(0x0,0x3FFF);
                    S('COLLEGE_NEW_LIST_SORT', $cache, 21600);
                }
                $order .= 'id ^ ' . $cache . ' DESC';
                break;
            case 'new':
                $order .= 'id DESC';
                break;
            case 'hot':
                $order .= 'views DESC';
                break;
            case 'score':
                $order .= 'score DESC';
                break;
            default:
                /*
                $cache = S('COLLEGE_LIST_SORT');
                if (empty($cache)) {
                    $cache = mt_rand(0x0,0x3FFF);
                    S('COLLEGE_LIST_SORT', $cache, 3600);
                }
                $order = 'id ^ ' . $cache . ' DESC';
                */
                $order .= 'id DESC';
                break;
        }
        $order = $order;

        $list = $this->where($map)->limit($limit)->page($page)->order($order)->field('id,title,cover_url,guests_id,views,times,cate1_title,cate2_title')->select();
        unset($map['id']);
        $total = $this->where($map)->count();
        return array('total'=>$total, 'list'=>empty($list) ? array() : $this->_format($list),'format'=>array('b'=>'!720x480','m'=>'!640x480','s'=>'!321x215','ss'=>'!240x160'));
    }

    /**
     * 获取视频列表(V2)
    */
    public function getListByCate($map=array(),$page,$per_page,$is_recommend){
        $map['status'] =1;
        $order = 'sort DESC,create_time DESC';
        if(!empty($is_recommend)){
            $map['is_recommend'] = 1;
            $list = $this->where($map)->limit($per_page)->order($order)->field('id,title,cover_url,guests_id,views,times,cate_title,is_vip,big_cover_url,category,charge_standard,create_time,company_id')->select();
        }else{
            $list = $this->where($map)->page($page,$per_page)->order($order)->field('id,title,cover_url,guests_id,views,times,cate_title,is_vip,big_cover_url,category,charge_standard,create_time,company_id')->select();
        }

        //获取公开课的和培训营课程的分类和收费信息
        $list = $this->get_course_info ($list);

        unset($map['is_recommend']);
        $total = $this->where($map)->count();
        return array('total'=>$total, 'list'=>empty($list) ? array() : $this->_format($list),'format'=>array('bb'=>'!1080x720','b'=>'!720x480','m'=>'!640x480','s'=>'!321x215','ss'=>'!240x160'));
    }

    /**
     * 获取精选课程的分类和收费信息
    */
    public function get_course_info ($list){
        $cate_title = M('SchoolCate')->where(array('status'=>1,'id'=>array('in',array(4,12))))->getField('id,title');
        foreach ($list as $key=>$value){
            $category_arr = empty($value['category']) ? array() : explode(',',$value['category']);
            $charge_standard_arr = empty($value['charge_standard']) ? array() : explode(',',$value['charge_standard']);
            //公开课和培训营视频获取收费类型
            if (in_array('4',$category_arr)){
                $list[$key]['course_title'] = $cate_title[4];
            }elseif (in_array('12',$category_arr)){
                $list[$key]['course_title'] = $cate_title[12];
            }elseif ($value['is_vip'] ==1){
                $list[$key]['course_title'] = '会员专享';
            }else{
                $list[$key]['course_title'] = '免费观看';
            }
            $list[$key]['standard'] = empty($charge_standard_arr) ? array() : $this->get_charge_standard($charge_standard_arr);

            $vid_arr[] = $value['id'];
        }
        //视频购买人数
        $buy_number = $this->get_buy_number($vid_arr);
        foreach ($list as $key=>$value){
            $list[$key]['buy_number'] = !empty($buy_number[$value['id']]) ? $buy_number[$value['id']] : 0;
        }
        return $list;

    }

    /**
     * 获取视频购买人数
    */
    public function get_buy_number($vid_arr){
        if (!empty($vid_arr)){
            $where['vid'] = array('in',$vid_arr);
            $where['status'] = 1;
            $buy_number = M('VideoBuyRecord')->where($where)->group('vid')->getField("vid,count('id')");
        }else{
            $buy_number = array();
        }

        return $buy_number;
    }



    /**
     * 统计收费标准
    */
    public function get_charge_standard($charge_standard_arr){
        $charge_standard = M('VideoChargeStandard')->where(array('status'=>1))->getField('id,title,price,note');
        foreach ($charge_standard_arr as $value){
            $charge[] = $charge_standard[$value];
        }

        return $charge;
    }

    /**
     * 视频详情
     * @param  [type] $id 视频编号
     * @return [type]     array('video'=>array(), 'guests'=>array())
     */
    public function getDetail($id){
        $user = get_user();
        // 访问数
       $result =  $this->where(array('id'=>$id, 'status'=>1))->setInc('views');

        $data['video'] = $this->where(array('id'=>$id, 'status'=>1))
            ->field('id,title,description,url,cover_url,guests_id,views,times,is_vip,auth,category,cate_title,charge_standard,company_id,course_city,course_date,gold_award_id')->find();
        if($data['video']){
            // 视频私有地址
            Vendor('Qiniu.Auth');
            $data['video']['url'] = $this->_getUrl($data['video']);
            $data['video']['share_url'] = 'http://college-api.halobear.com/video/detail?id=' . $id;

            // 判断用户是否收藏
            $data['video']['is_favorite'] = 0;
            if (!empty($user)) {
                $data['video']['is_favorite'] = M('SchoolFavorites')->where(array('vid'=>$id, 'uid'=>$user['id']))->count();
            }

            // 封面图
            $data['video']['cover_url'] = C('IMG_URL'). $data['video']['cover_url'];// . '!720x480'

            // 嘉宾信息
            $data['guests'] = M('SchoolGuests')->where(array('id'=>$data['video']['guests_id']))->field('title, position, avatar_url, content')->find();

            // 嘉宾头像
            $data['guests']['avatar_url'] = $data['guests']['avatar_url'] ? C('IMG_URL'). $data['guests']['avatar_url'] : '';

            //公司信息
            $data['company'] = $this->company_detail($data['video']['company_id']);

            //评论数
            $data['video']['count_comment'] = intval(M('SchoolComment')->where(array('vid'=>$id,'status'=>1))->count());

            //点赞状态
            if($user['id']){
                $status = M('SchoolVideoPraise')->where(array('uid'=>$user['id'],'vid'=>$id))->field('status')->find();
                $data['video']['status_praise'] = empty($status) ? 0 : intval($status['status']);
            }else{
                $data['video']['status_praise'] = -1;
            }

            //点赞数量
            $count_praise = M('SchoolVideoPraise')->where(array('vid'=>$id,'status'=>1))->count();
            $data['video']['count_praise'] = intval($count_praise) ? intval($count_praise) : 0;

           //播放进度
            if($user['id']){
                $play_time = M('SchoolVideoRecord')->where(array('uid'=>$user['id'],'vid'=>$id))->field('play_time')->find();
                $data['video']['play_time'] = empty($play_time) ? 0 : intval($play_time['play_time']);
            }else{
                $data['video']['play_time'] = -1;
            }
            //获取精选视频课程信息
            $course = $this->get_course_info (array($data['video']));
            $data['video'] = $course[0];

            //判断视频类型
            $data['video'] = $this->getVideoType($data['video'],$user['id']);

        }else{
            $data = array();
        }

        return $data;
    }

    /**
     * 根据id获取公司详情
    */
    public function company_detail($company_id){
        $url = 'http://7ktsyl.com2.z0.glb.qiniucdn.com/';
        if (!empty($company_id)){
            $company = company_id($company_id);
            $company_base_info['id'] =  $company['data']['id'];
            $company_base_info['name'] =  $company['data']['name'];
            $company_base_info['description'] =  $company['data']['description'];
            $company_base_info['logo'] =  $url.$company['data']['logo'][0]['file_path'];
        }else{
            $company_base_info = null;
        }

        return $company_base_info;
    }


    /**
     * 判断视频类型
    */
    public function getVideoType($video,$uid){
        $charge_standard_arr = empty($video['charge_standard']) ? array() : explode(',',$video['charge_standard']);
        if ($video['is_vip']==1 && empty($charge_standard_arr) && !check_vip($uid)){
            $remark = 2;
        }elseif (in_array('1',$charge_standard_arr) && !check_vip($uid) && !is_buy($uid,$video['id'])){
            $remark = 3;
        }elseif (in_array('2',$charge_standard_arr) && !is_buy($uid,$video['id'])){
            $remark = 4;
        }else{
            $remark = 1;
        }
        $video['remark_video_type'] = $remark;
        return $video;
    }

    /**
     * 获取嘉宾信息
    */
    public function getGuests($sub_videos){
        foreach ($sub_videos as $key=>$value){
            $guests_id[] = $value['guests_id'];
        }
        if (!empty($guests_id)){
            $guests = M('SchoolGuests')->where(array('id'=>array('in',$guests_id),'status'=>1))->getField('id,title,position');
        }else{
            $guests = array();
        }
        foreach ($sub_videos as $key=>$value){
            $sub_videos[$key]['guest'] = $guests[$value['guests_id']];
        }

        return  $sub_videos;
    }



    /**
     * 获取视频播放地址
     * @param $video
     * @return string
     */
    private function _getUrl($video){
        $user = get_user();
        $url = '';

        //获取收费标准的数组形式
        $video = $this->get_charge_arr($video);

        // 是否需要登录、会员免费、必须购买
        if (($video['auth'] == 1 && !empty($user)) || ($video['is_vip'] == 1 && check_vip($user['id'])) || ($video['auth'] == 0 && $video['is_vip'] == 0 && !in_array('1',$video['charge_arr']) && !in_array('2',$video['charge_arr'])) || (in_array('1',$video['charge_arr']) && (is_buy($user['id'],$video['id']) || check_vip($user['id']))) || (in_array('2',$video['charge_arr']) && is_buy($user['id'],$video['id']))){
            $url = $this->privateDownloadUrl(C('VIDEO_URL') . $video['url']);
        }

        return $url;
    }

    /**
     * 获取视频收费标准数组数据
    */
    public function get_charge_arr($video){
        $charge_arr = empty($video['charge_standard']) ? array() : explode(',',$video['charge_standard']);
        $video['charge_arr'] = $charge_arr;

        return $video;
    }

    /**
     * 获取视频地址
     * @param  [type] $id 视频id
     * @return [type]     视频地址
     */
    public function getUrl($id){
        $url = $this->where(array('id'=>$id, 'status'=>1))->getField('url');

        return $url ? $this->privateDownloadUrl(C('VIDEO_URL') . $url) : '';
    }
    

    public function privateDownloadUrl($baseUrl, $expires = 3600){
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


    public function getRecommend($vid, $limit = 5){
        $data = $this->where(array('status'=>1, 'id'=>$vid))->field('guests_id, cate1, cate2')->find();

        if (empty($data)) {
            return array();
        }
        $order = 'views DESC,score DESC,id DESC';
        // 获取相关嘉宾的视频
        $list = $this->where(array('status'=>1, 'guests_id'=>$data['guests_id']))->order($order)->limit($limit)->field('id,title,guests_id,views,cover_url,times')->select();
        foreach ($list as $key => $value) {
            if ($value['id'] == $vid) {
                unset($list[$key]);
            }
        }
        $length = count($list);
        $sub_list = array();
        if ($length < $limit) {
            $sub_list = $this->where(array('status'=>1, 'cate2'=>$data['cate2'], 'cate1'=>$data['cate1'], 'id'=>array('neq', $vid)))->order($order)->limit($limit - $length)->field('id,title,guests_id,views,cover_url,times')->select();
        }
        $list = array_merge($list, $sub_list);

        return $this->_format($list);
    }
    
    /**
     * 获取推荐列表（V2）
    */
    public function getRecommendList($vid, $limit = 5){
        $data = $this->where(array('status'=>1, 'id'=>$vid))->field('guests_id,category')->find();
        if (empty($data)) {
            return array();
        }
        $order = 'views DESC,score DESC,id DESC';
        // 获取相关嘉宾的视频
       $list = $this->where(array('status'=>1, 'guests_id'=>$data['guests_id']))->order($order)->limit($limit)->field('id,title,cover_url,guests_id,views,times,cate_title,is_vip,big_cover_url,category,charge_standard,create_time')->select();
        foreach ($list as $key => $value) {
            if ($value['id'] == $vid) {
                unset($list[$key]);
            }
        }
        $length = count($list);
        $sub_list = array();
        if ($length < $limit) {
            $category = explode(',',$data['category']);
            foreach ($category as $key=>$value){
                $sub_list = $this->where(array('status'=>1, '_string'=>'FIND_IN_SET(' . $value. ',category)','id'=>array('neq', $vid)))
                    ->order($order)->limit($limit - $length)->field('id,title,cover_url,guests_id,views,times,cate_title,is_vip,big_cover_url,category,charge_standard,create_time')->select();
                if(!empty($sub_list)){
                    $list = array_merge($list, $sub_list);
                }
            }
        }
        //获取公开课的和培训营课程的分类和收费信息
        $list = $this->get_course_info ($list);
        return $this->_format($list);
    }


 
    public function _format($list){
        foreach ($list as $key => $value) {
            $guests_id[] = $value['guests_id'];
        }
        $guests_id = array_unique($guests_id);
        $guests = M('SchoolGuests')->where(array('id'=>array('in', $guests_id)))->getField('id, title, position');

        foreach ($list as $key => $value) {
            $list[$key]['guests'] = $guests[$value['guests_id']];
            $list[$key]['cover_url'] = C('IMG_URL') . $value['cover_url'];
            $list[$key]['big_cover_url'] = C('IMG_URL') . $value['big_cover_url'];
        }

        return $list;
    }

}