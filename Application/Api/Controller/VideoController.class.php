<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/18
 * Time: 16:16
 */

namespace Api\Controller;

use Think\Controller;
class VideoController extends CommonController {
    protected $module_auth = 0;
    protected $action_auth = array('getExpireDate','getUrl','commontSave','openMember','checkExpire','recordPlay','getRecord','favoritesAct','myFavorites'
    ,'delFavorites','myComments','delComment','videoPraise','videoCancelPraise');

    /**
     * 根据类型获取列表
     * @param  string  $type 类型
     * @param  integer $per_page 每页显示数量
     * @return [type]            [description]
     */
    public function getListByType($type, $per_page = 6) {
        if (!in_array($type, array('index_new', 'new', 'hot', 'score'))) {
            $this->error('参数错误');
        }
        $per_page = min(15, $per_page);
        $data = D('SchoolVideo')->getList(array(), $type, $per_page);
        $this->success('success', $data);
    }

    /**
     * 根据类型获取列表
     * @param  integer $cate 分类id
     * @param  integer $per_page 每页显示数量
     * @return [type]            [description]
     */
    public function getListByCate($cate, $per_page = 12) {
        $cate = I('cate');
        /*if ($cate <= 0 || $cate != 'all') {
            $this->error('参数错误');
        }*/
        // $per_page = min(24, $per_page);
        $map = $cate == 'all' ? array() : array('cate1' => $cate);
        $data = D('SchoolVideo')->getList($map, '', $per_page);
        $this->success('success', $data);
    }

    /**
     * 获取视频列表 （根据分类id）( V2)
     */
    public function getListVideo(){
        $is_hot = intval(I('is_hot'));
        $is_vip = intval(I('is_vip'));
        $cate = I('cate');
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        if(!empty($is_vip) && !empty($is_hot)){
            $this->error('参数错误！');
        }
        if (!empty($is_vip) && !empty($cate)){
            $this->error('参数错误！');
        }
        if (!empty($is_hot) && !empty($cate)){
            $this->error('参数错误！');
        }
        if (empty($is_hot) && empty($cate) && empty($is_vip)){
            $this->error('参数错误！');
        }
        if(!empty($is_hot)){
            $map['is_hot'] = is_int($is_hot);
        }elseif (!empty($is_vip)){
            $map['is_vip'] = is_int($is_vip);
        }else{
            $map['_string'] = 'FIND_IN_SET(' . $cate . ', category)';
        }

        $data = D('SchoolVideo')->getListByCate($map,$page,$per_page);
        $this->success('success', $data);
    }

    /**
     * 获取不同类型推荐视频列表（V2）
     */
    public function recommendVideoList(){
        $model = D('SchoolVideo');
        $member_list = $model->getListByCate(array('is_vip'=>1),$page=1,$per_page=2);
        $member['cate_id'] = 0;
        $member['cate_title'] = '会员专享';
        $member['list'] = $member_list['list'];
        $list[] = $member;
        $cate = M('SchoolCate')->getField('id,title');
        foreach ($cate as $key=>$value){
            $data_list = $model->getListByCate(array('_string'=>'FIND_IN_SET(' . $key . ', category)'),$page=1,$per_page=2,$is_recommend=1);
            $data['cate_id'] = $key;
            $data['cate_title'] = $value;
            $data['list'] = $data_list['list'];
            $list[] = $data;
        }
        $this->success('success', $list);
    }

    /**
     * 根据类型3获取列表
     * @param  integer $cate 分类id
     * @param  integer $per_page 每页显示数量
     * @return [type]            [description]
     */
    public function getListByCate2($cate, $per_page = 12) {
        $cate = intval(I('cate'));
        if (empty($cate)) {
            $this->error('error');
        }
        $map = array('_string' => 'FIND_IN_SET(' . $cate . ', cate3)');
        $data = D('SchoolVideo')->getList($map, '', $per_page);
        $this->success('success', $data);
    }

    /**
     * 根据类型获取列表
     * @param  integer $cate 分类id
     * @param  integer $per_page 每页显示数量
     * @return [type]            [description]
     */
    public function getListByTopic($cate, $per_page = 12) {
        // $per_page = min(24, $per_page);
        switch ($cate) {
            case 'all':
                $map = array();
                break;
            case 'wfc2015':
                $map = array('cate2' => array('in', array(5, 6)));
                break;
            case 'bear2015':
                $map = array('cate2' => array('in', array(7, 8)));
                break;
            case 'wfc2014':
                $map = array('cate2' => array('in', array(9, 10)));
                break;
            case 'wfc2013':
                $map = array('cate2' => array('in', array(11, 12)));
                break;
            case 'wfc2012':
                $map = array('cate2' => array('in', array(13, 14)));
                break;

            default:
                $map = array('cate2' => intval($cate));
                break;
        }

        $data = D('SchoolVideo')->getList($map, '', $per_page);
        $this->success('success', $data);
    }

    /**
     * 搜索
     * @param  string  $keyword 关键词
     * @param  integer $per_page [description]
     * @return [type]            [description]
     */
    public function search($keyword, $per_page = 12) {
        if (empty($keyword) && $keyword != '0') {
            $this->error('参数错误');
        }
        $map = array('title' => array('like', '%' . $keyword . '%'));
        $guests = M('SchoolGuests')->where(array('title' => array('like', '%' . $keyword . '%'), 'position' => array('like', '%' . $keyword . '%'), '_logic' => 'OR'))->getField('id,title');
        $guests_id = array_keys($guests);
        if ($guests_id) {
            $map = array('_string' => "title like '%{$keyword}%' OR guests_id IN(" . implode(',', $guests_id) . ')');
        }

        //$data = D('SchoolVideo')->getList($map, '', $per_page);
        $data = D('SchoolVideo')->getListByCate($map, '', $per_page);
        // 记录搜索关键词
        if ($data) {
            $tag = M('school_video_tag');
            $tag_id = $tag->where(array('tag' => $keyword))->getField('id');
            if ($tag_id) {
                $tag->where(array('id' => $tag_id))->setInc('num');
            } else {
                $tag->add(array('tag' => $keyword, 'num' => 1));
            }
        }

        $this->success('success', $data);
    }

    public function total() {
        $week = M()->query('SELECT count(*) AS count FROM wtw_school_video WHERE YEARWEEK(FROM_UNIXTIME(create_time,\'%Y-%m-%d\')) = YEARWEEK(now())');
        $month = M()->query("SELECT count(*) AS count FROM  wtw_school_video WHERE FROM_UNIXTIME(create_time,'%Y%m') = DATE_FORMAT( CURDATE() , '%Y%m' ) ");

        $this->success('success', array('week' => intval($week[0]['count']), 'month' => intval($month[0]['count'])));
    }

    /**
     * 视频详情
     */
    public function videoDetail($id) {
        $id = intval($id);
        empty($id) && $this->error('参数错误');
        $data = D('SchoolVideo')->getDetail($id);

        empty($data) ? $this->error('视频不存在') : $this->success('success', $data);
    }


    /**
     * 视频详情无需登录
     */
    public function videoDetailV2($id) {
        $id = intval($id);
        empty($id) && $this->error('参数错误');
        $data = D('SchoolVideo')->getDetail($id, 0);

        empty($data) ? $this->error('视频不存在') : $this->success('success', $data);
    }

    /**
     * 获取视频地址
     * @param  [type] $id 视频编号
     * @return [type]     视频地址
     */
    public function getUrl($vid) {
        $uid = $this->user['uid'];
        $video = M('SchoolVideo')->where(array('id'=>$vid,'status'=>1))->find();
        //判断是否需要登录观看视频
        if($video['auth'] ==1){
            if(empty($uid)){
                $this->error('该视频登录后才能观看！');
            } else{
                $url = $this->is_member($video,$uid,$vid);
            }
        }else{
            $url = $this->is_member($video,$uid,$vid);
        }

        $url ? $this->success('success', $url) : $this->error('视频不存在', $url);
    }

    /**
     * 判断是否为会员专享视频
    */
    public function is_member($video,$uid,$vid){
        $now = time();
        //判断是否获取VIP视频
        if ($video['is_vip'] ==1){
            $member = M('SchoolMember')->where(array('uid'=>$uid))->find();
            if ($member['end_time']<$now){
                $this->error('非会员不能观看！');
            }else{
                $url = D('SchoolVideo')->getUrl(intval($vid));
            }
        }else{
            $url = D('SchoolVideo')->getUrl(intval($vid));
        }
        return $url;
    }

    /**
     * 视频推荐
     * @param  [type] $vid 视频编号
     * @return [type]      推荐视频列表
     */
    public function videoRecommend($vid) {
        //$list = D('SchoolVideo')->getRecommend($vid);
        $list = D('SchoolVideo')->getRecommendList($vid);
        $this->success('success', $list);
    }

    /**
     * 评论
     * @return [type] [description]
     */
    public function commontSave() {
        if (!IS_POST) {
            $this->error('Access denied');
        }
        $model = D('SchoolComment');
        $_POST['uid'] = $this->user['uid'];
        //用户名用真实姓名
        $user = getTrueName($_POST['uid']);
        if(!empty($user)){
            $_POST['username'] = $user['truename'];
        }else{
            $_POST['username'] = $this->user['username'];
        }
        if ($model->create()) {
            $id = $model->add();
            $id ? $this->success('评论成功！') : $this->error('评论失败！');
        } else {
            $this->error($model->getError());
        }
    }

    public function commentList($vid, $per_page = 12) {
        $vid = intval($vid);

        if (empty($vid) || $vid < 0) {
            $this->error('参数错误');
        }

        $list = D('SchoolComment')->getList(array('vid' => $vid), $per_page);

        $this->success('success', $list);
    }

    //获取视频评论列表（最新）--包含职位信息
    public function getComments(){
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $vid = I('vid');
        $vid = intval($vid);
        if(empty($vid) || $vid < 0){
            $this->error('参数错误');
        }
        $list = D('SchoolComment')->getCommentList($page,$per_page,$vid);
        $this->success('success', $list);
    }

    /**
     * 视频会员开通（ 作废）
     */
    public function openMember(){
        $type = trim(I('cate'));
        if(empty($type)){
            $this->error('参数错误！');
        }
        $model_user = D('SchoolAccount');
        $data['uid'] = $this->user['uid'];
        //$data['username'] = $model_user->username;
        //$data['phone'] = $model_user->phone;
        switch ($type){
            case 1:
                $cycle_time = 12*30*24*60*60;
                break;
            case 2:
                $cycle_time = 6*30*24*60*60;
                break;
            case 3:
                $cycle_time = 3*30*24*60*60;
                break;
            case 4:
                $cycle_time = 30*24*60*60;
                break;
            default:
                $this->error('没有该类型的会员！');
        }
        //判断用户是否已经开通会员(若开通继续续费)
        $member = $this->continue_member($data['uid']);
        if(!empty($member)){
            //$data['start_time'] = $member['start_time'];
            $data['end_time'] = $member['end_time']+$cycle_time;
            //$data['type'] = trim($member['type']).','.$type;
        }else{
            $data['start_time'] = time();
            $data['type'] = $type;
            $data['end_time'] = time()+$cycle_time;
        }
        $data['is_expire'] = 0;
        $data['create_time'] =time();
        $data['update_time'] =time();
        $data['status'] =1;
        $id = M('SchoolMember')->add($data);
        if($id){
            $this->success('success');
        }else{
            $this->error('会员开通失败！');
        }
    }

    /**
     *会员叠加（作废）
     */
    public function continue_member($uid){
        $where['uid'] = $uid;
        $where['is_expire'] = 0;
        $where['status'] =1;
        $member = M('SchoolMember')->where($where)->find();
        if(!empty($member)){
            $member['is_expire'] = 1;
            $member['status'] = 0;
            $member['update_time'] = time();
            $result = M('SchoolMember')->save($member);
            return $member;
        }else{
            return array();
        }
    }

    /**
     * 定时任务检测会员是否过期或判断用户是否开通会员（作废）
     */
    public function checkExpire(){
        $uid = $this->user['uid'];
        $where['uid'] = $uid;
        $where['status'] = 1;
        $where['is_expire'] = 0;
        $member = M('SchoolMember')->where($where)->find();
        if(!empty($member)){
            $now = time();
            $end_time = $member['end_time'];
            if($now>$end_time){
                $member['is_expire'] =1;
                $member['status'] = 0;
                $member['update_time'] =time();
                $result = M('SchoolMember')->save($member);
                $this->success('success',array('end_time'=>0));
            }else{
                $this->success('success',array('end_time'=>$end_time));
            }
        }else{
            $this->success('success',array('end_time'=>0));
        }
    }

    /**
     * 获取会员类型
     */
    public function getMemberCate(){
        $cate_id = I('cate_id');
        if(empty($cate_id)){
            $where['status'] =1;
        }else{
            $where['status'] =1;
            $where['id'] =$cate_id;
        }
        $member_cate = M('SchoolMemberCate')->where($where)->select();
        $data['list'] = $member_cate;
        $this->success('success',$data);
    }

    /**
     * 获取会员过期时间及开通的会员类型
     */
    public function getExpireDate(){

        $uid = $this->user['uid'];
        $where['uid'] = $uid;
        $where['status'] = 1;
        $member = M('SchoolMember')->where($where)->find();
        $open_member = $this->get_open_member($uid);
        if (!empty($open_member)){
            $open_member[0]['expire'] = $member['end_time'] ? $member['end_time'] : '';
        }
        $data[]= $open_member[0];
        $this->success('success',$data);
    }

    /**
     * 记录播放
     */
    public function recordPlay(){
        $data['vid'] = I('vid');
        if(empty($data['vid'])){
            $this->error('参数错误！');
        }
        $data['uid'] = $this->user['uid'];
        $data['progress'] = I('progress') ? I('progress') : '0%';
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['status'] = 1;
        $record = M('SchoolVideoRecord')->where(array('uid'=>$data['uid'],'vid'=>$data['vid'],'status'=>1))->find();
        if(!empty($record)){
            $record['progress'] = I('progress') ? I('progress') : $record['progress'];
            $record['update_time'] = time();
            $result = M('SchoolVideoRecord')->save($record);
            if($result!==false){
                $this->success('播放记录保存成功！');
            }else{
                $this->error(M('SchoolVideoRecord')->getError());
            }
        }
        $id = M('SchoolVideoRecord')->add($data);
        if($id){
            $this->success('播放记录添加成功！');
        }else{
            $this->error(M('SchoolVideoRecord')->getError());
        }
    }

    /**
     * 播放记录列表
     */
    public function getRecord(){
        $uid = $this->user['uid'];
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $where['uid'] = $uid;
        $where['status'] =1;
        $record = M('SchoolVideoRecord')->where($where)->field('vid,progress')->select();
        if(empty($record)){
            $this->success('success',array('record'=>array()));
        }
        foreach ($record as $key=>$value){
            $vid_arr[] = $value['vid'];
            $progress[$value['vid']] = $value['progress'];
        }
        $vid_arr = array_unique($vid_arr);
        $map['id'] = array('in',$vid_arr);
        $videos = D('SchoolVideo')->getListByCate($map,$page,$per_page);
        $videos = $videos['list'];
        foreach ($videos as $key=>$value){
            $videos[$key]['play_progress'] = $progress[$value['id']];
        }
        $data['videos'] = $videos;
        $this->success('success',$data);

    }

    /**
     * 登录状态记录
     */
    public function loginRecord(){

    }

    /**
     * 获取开通的会员类型
     */
    public function get_open_member($uid){
        $where['uid'] = $uid;
        $where['status'] = 1;
        $member = M('MemberOrder')->where($where)->field('uid,cate')->select();
        foreach ($member as $key=>$value){
            $member_arr[] = $value['cate'];
        }
        $min_cocunt = array_count_values($member_arr);
        $min_cate = min($member_arr);
        $open_member = M('SchoolMemberCate')->where(array('id'=>$min_cate,'status'=>1))->field('id as cate_id,title as cate_title')->select();
        if (!empty($open_member)){
            $open_member[0]['cate_count'] = $min_cocunt[$min_cate];
        }
        return $open_member;

    }

    public function qiniuUpload() {

        $this->ajaxReturn(array('url' => C('AVATAR_URL') . $_POST['key'], 'width' => $_POST['w'], 'height' => $_POST['h']));
    }

    /**
     * 热门标签
     */
    public function hotTag() {
        $tag = array('信仰', '潘珍玉', '金熊奖', '蔡易瑾', '花艺设计');

        $this->success('success', $tag);
    }

    /**
     * 幻灯
     * @param  string $title 标题
     * @param  string $url 地址
     * @param  string $type text静态文本,link网页,video视频
     * @return
     */
    public function banner() {
        $data = array(array('title' => '2015中国婚礼行业高峰论坛', 'desc' => '官方回顾视频', 'img' => 'http://7xopel.com2.z0.glb.qiniucdn.com/college/banner/2015.jpg', 'url' => 'http://7s1t37.com2.z0.glb.qiniucdn.com/WFCHJ/2015WFC.mp4', 'type' => 'video'), array('title' => '2014中国婚礼行业高峰论坛', 'desc' => '官方回顾视频', 'img' => 'http://7xopel.com2.z0.glb.qiniucdn.com/college/banner/2014.jpg', 'url' => 'http://7s1t37.com2.z0.glb.qiniucdn.com/WFCHJ/2014WFC.mp4', 'type' => 'video'), array('title' => '2013中国婚礼行业高峰论坛', 'desc' => '官方回顾视频', 'img' => 'http://7xopel.com2.z0.glb.qiniucdn.com/college/banner/2013.jpg', 'url' => 'http://7s1t37.com2.z0.glb.qiniucdn.com/WFCHJ/2013WFC.mp4', 'type' => 'video'), array('title' => '2012中国婚礼行业高峰论坛', 'desc' => '官方回顾视频', 'img' => 'http://7xopel.com2.z0.glb.qiniucdn.com/college/banner/2012.jpg', 'url' => 'http://7s1t37.com2.z0.glb.qiniucdn.com/WFCHJ/2012WFC.mp4', 'type' => 'video'),);

        $this->success('success', $data);
    }

    // 收藏
    public function favoritesAct() {

        $vid = intval(I('vid'));
        if (empty($vid)) {
            $this->error('参数错误');
        }
        $uid = $this->user['uid'];
        $count = M('SchoolVideo')->where(array('id' => $vid, 'status' => 1))->count();
        if ($count == 0) {
            $this->error('视频不存在');
        }

        D('SchoolFavorites')->act($vid,$uid);
        $this->success('操作成功');
    }

    // 我的收藏
    public function myFavorites() {
        $per_page = I('per_page');
        $user = $this->user;
        $list = D('SchoolFavorites')->getList($per_page ? $per_page : 12,$user);
        $this->success('success', $list);
    }

    // 取消收藏
    public function delFavorites() {

        $uid = $this->user['uid'];
        $vids = I('vids');
        if (empty($vids)) {
            $this->error('参数错误');
        }

        $ret = D('SchoolFavorites')->where(array('uid' => $uid, 'vid' => array('in', $vids)))->delete();

        $ret ? $this->success('操作成功') : $this->error('操作失败');
    }

    // 我的评论
    public function myComments() {
        $per_page = I('per_page');
        $list = D('SchoolComment')->my($per_page ? $per_page : 12, 1);
        $this->success('success', $list);
    }

    // 删除评论
    public function delComment() {
        $uid = $this->user['uid'];
        $ids = I('id');
        if (empty($ids)) {
            $this->error('参数错误');
        }

        $ret = D('SchoolComment')->where(array('uid' => $uid, 'id' => array('in', $ids)))->delete();
        $ret ? $this->success('删除成功') : $this->error('删除失败');
    }

    /**
     * 获取分类接口
     */
    public function getCate() {
        $list = M('SchoolCate')->where(array('status' => 1))->select();
        $this->success('success', $list);
    }

    /**
     * 视频点赞
     */
    public function videoPraise(){
        $model = D('SchoolAccount');
        $data['vid'] = I('vid');
        $data['uid'] = $this->user['uid'];
        $user = getTrueName($data['uid']);
        if(!empty($user)){
            $data['username'] = $user['truename'];
        }else{
            $data['username'] = $this->user['username'];
        }
        $data['headimg'] = get_avatar($data['uid']);
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['status'] = 1;
        $video = D('SchoolVideo')->where(array('id' => $data['vid']))->find();
        if (empty($video)) {
            $this->error('参数错误！');
        }
        $model_praise = M('SchoolVideoPraise');
        $where['uid'] = $data['uid'];
        $where['vid'] = $data['vid'];
        //$where['status'] =1;
        $praise = $model_praise->where($where)->find();
        if (!empty($praise)) {
            if ($praise[status] == 1) {
                $this->error('您已经对该条内容点赞过了！');
            } else {
                $praise['status'] = 1;
                $praise['update_time'] = time();
                $result = $model_praise->save($praise);
                if ($result !== false) {
                    $this->success('点赞成功！');
                } else {
                    $this->error('点赞失败！');
                }
            }
        }
        $id = $model_praise->add($data);
        if ($id) {
            $this->success('点赞成功！');
        } else {
            $this->error('点赞失败！');
        }
    }

    /**
     * 视频取消点赞
     */
    public function videoCancelPraise(){
        $model = D('SchoolAccount');
        $vid = I('vid');
        $uid = $this->user['uid'];
        if (empty($vid)) {
            $this->error('参数错误！');
        }
        $model_praise = M('SchoolVideoPraise');
        $where['uid'] = $uid;
        $where['vid'] = $vid;
        $where['status'] = 1;
        $praise = $model_praise->where($where)->find();
        if (empty($praise)) {
            $this->error('您不曾对该内容点赞过！');
        }
        $praise['update_time'] = time();
        $praise['status'] = 0;
        $result = $model_praise->save($praise);
        if ($result !== false) {
            $this->success('取消点赞成功！');
        } else {
            $this->error('取消点赞失败！');
        }
    }

    //分类迁移
    public function changeCate(){
        $videos = M('SchoolVideo')->select();
        foreach ($videos as $key=>$value){
            $category = '';
            if (empty($value['category'])){
                $category.= $value['cate1'];
                if (!empty($value['cate3'])){
                    $cate3_arr = explode(',',$value['cate3']);
                    foreach ($cate3_arr as $key_cate3=>$value_cate3){
                        switch ($value_cate3){
                            case '1':
                                $category.=','.'5';
                                break;
                            case '2':
                                $category.=','.'6';
                                break;
                            case '3':
                                $category.=','.'7';
                                break;
                            case '4':
                                $category.=','.'8';
                                break;
                            default:

                        }
                    }

                }
                $videos[$key]['category'] = $category;
                $data = $videos[$key];
                $result = M('SchoolVideo')->save($data);
                if ($result==false){
                    $this->error($data);
                }

            }

        }

    }

    //分类名称迁移
    public function changeName(){
        $cate = M('SchoolCate')->getField('id,title');
        $videos = M('SchoolVideo')->select();
        foreach ($videos as $key=>$value){
            if(empty($value['cate_title'])){
                if(!empty($value['category'])){
                    $cate_arr = explode(',',$value['category']);
                    foreach ($cate_arr as $key_cate=>$value_cate){
                        $cate_title_arr[] = $cate[$value_cate];
                    }
                    $cate_title = implode(',',$cate_title_arr);
                    unset($cate_title_arr);
                    $videos[$key]['cate_title'] = $cate_title;
                    $result = M('SchoolVideo')->save($videos[$key]);
                    if ($result==false){
                        $this->error($videos[$key]);
                    }
                }
            }
        }
    }





}