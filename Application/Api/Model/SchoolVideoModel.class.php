<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/5
 * Time: 16:09
 */

namespace Api\Model;

use Think\Model;

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
        $order = 'sort DESC';
        if(!empty($is_recommend)){
            $map['is_recommend'] = 1;
            $list = $this->where($map)->limit(2)->order($order)->field('id,title,cover_url,guests_id,views,times,cate_title,is_vip,big_cover_url')->select();
        }else{
            $list = $this->where($map)->page($page,$per_page)->order($order)->field('id,title,cover_url,guests_id,views,times,cate_title,is_vip,big_cover_url')->select();
        }
        unset($map['is_recommend']);
        $total = $this->where($map)->count();
        return array('total'=>$total, 'list'=>empty($list) ? array() : $this->_format($list),'format'=>array('bb'=>'!1080x720','b'=>'!720x480','m'=>'!640x480','s'=>'!321x215','ss'=>'!240x160'));
    }

    /**
     * 视频详情
     * @param  [type] $id 视频编号
     * @return [type]     array('video'=>array(), 'guests'=>array())
     */
    public function getDetail($id, $auth = 1){

        // 访问数
       $result =  $this->where(array('id'=>$id, 'status'=>1))->setInc('views');

        $data['video'] = $this->where(array('id'=>$id, 'status'=>1))->field('id,title,url,cover_url,guests_id,views,is_vip')->find();
        if($data['video']){
            // 视频私有地址
            Vendor('Qiniu.Auth');
            $user = get_user();
            if(!empty($user) || $auth == 0){
                $data['video']['url'] = $this->privateDownloadUrl(C('VIDEO_URL') . $data['video']['url']);
            }else{
                $data['video']['url'] = '';
            }
            $data['video']['share_url'] = 'http://college.halobear.com/lectureDetail/' . $id;

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

        }else{
            $data = array();
        }

        return $data;
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
        $list = $this->where(array('status'=>1, 'guests_id'=>$data['guests_id']))->order($order)->limit($limit)->field('id,title,guests_id,views,cover_url,times')->select();
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
                    ->order($order)->limit($limit - $length)->field('id,title,guests_id,views,cover_url,times')->select();
                if(!empty($sub_list)){
                    $list = array_merge($list, $sub_list);
                }
            }
        }

        return $this->_format($list);
    }
 
    private function _format($list){
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