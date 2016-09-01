<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/28
 * Time: 16:06
 */

namespace Api\Controller;

use Think\Controller;

class WeddingController extends CommonController {
    protected $module_auth = 0;
    protected $action_auth = array('commentPost','replyPost','reply','reportUser','praise','weddingPraise','favorite','cancelFavorite','cancelPraise'
    ,'weddingCancelPraise','myCommentDelete','myComments','myFavorites','myFavoritesDelete','actionPublish','myReply','myReplyDelete',);

    /**
     * 头条分类获取
    */
    public function category(){
        $category = M('SchoolWeddingCategory')->where("status=1")->field('id,name')->select();
        $data['category'] = !empty($category) ? array_values($category) : (object)$category;
        $this->success('success',$data);
    }


    /**
     * 婚礼头条列表
    */
    public function weddingList() {
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $category_id = I('category_id');
        $uid = $this->user['uid'];
        if (empty($category_id)) {
            $this->error('参数错误！');
        }
        $model = M('SchoolWedding');
        $where['wtw_school_wedding.status'] = 1;
        $where['wtw_school_wedding.category_id'] = $category_id;
        $list = $model->join('left join wtw_school_wedding_category on wtw_school_wedding.category_id=wtw_school_wedding_category.id')->where($where)
            ->order("wtw_school_wedding.sort desc,wtw_school_wedding.create_time desc")
            ->field("wtw_school_wedding.id,wtw_school_wedding_category.name,wtw_school_wedding.headline,wtw_school_wedding.brief,wtw_school_wedding.create_time")
            ->page($page, $per_page)->select();
        //&amp转换为&
        foreach ($list as $key=>$value){
            $str = preg_replace('/&amp;/','&',$value['headline']);
            $list[$key]['headline'] = $str;
        }
        if (empty($list)) {
            $data['list'] = array();
            $data['total'] = 0;
            $this->success('内容为空！', $data);
        }
        //获取头条cover
        foreach ($list as $key => $value) {
            $wedding_id[] = $value['id'];
        }
        if (!empty($wedding_id)) {
            $imgs_url = $this->get_imgs($wedding_id, 'cover');
            foreach ($list as $key_list => $value_list) {
                $list[$key_list]['imgs'] = array();
                foreach ($imgs_url as $key_img => $value_img) {
                    if ($value_list['id'] == $value_img['record_id']) {
                        $list[$key_list]['imgs'][] = $imgs_url[$key_img];
                    }
                }
            }
        }
        //获取访问总数、点赞总数、点赞状态、评论总数
        $visitCount = M('WeddingVisitcount')->where(array('wedding_id'=>array('in',$wedding_id),'status'=>1))->field('wedding_id,count')->select();
        $praiseCount = M('schoolWeddingWeddingpraise')->where(array('wedding_id'=>array('in',$wedding_id),'status'=>1))->group('wedding_id')->field('wedding_id,count(id) as count')->select();
        $status_praise = M('schoolWeddingWeddingpraise')->where(array('wedding_id'=>array('in',$wedding_id),'uid'=>$uid))->field('wedding_id,status')->select();
        $comment_count = M('schoolWeddingComment')->where(array('remark_id'=>array('in',$wedding_id),'status'=>1,))->group('remark_id')->field('remark_id as wedding_id,count(id) as count')->select();
        foreach ($list as $key=>$value){
            $list[$key]['visitCount'] = 0;
            $list[$key]['praiseCount'] = 0;
            $list[$key]['comment_count'] = 0;
            if(empty($uid)){
                $list[$key]['status_praise'] = -1;
            }else{
                $list[$key]['status_praise'] = 0;
            }
            if(!empty($visitCount)){
                foreach ($visitCount as $visit_key=>$visit_value){
                    if($value['id']==$visit_value['wedding_id']){
                        $list[$key]['visitCount'] = $visit_value['count'];
                    }
                }
            }
            if (!empty($praiseCount)){
                foreach ($praiseCount as $praise_key=>$praise_value){
                    if ($value['id']==$praise_value['wedding_id']){
                        $list[$key]['praiseCount'] = $praise_value['count'];
                    }
                }
            }
            if(!empty($comment_count)){
                foreach ($comment_count as $com_key=>$com_value){
                    if ($value['id']==$com_value['wedding_id']){
                        $list[$key]['comment_count'] = $com_value['count'];
                    }
                }
            }
            if(!empty($status_praise)){
                foreach ($status_praise as $status_key=>$status_value){
                    if ($value['id']==$status_value['wedding_id']){
                        $list[$key]['status_praise'] = $status_value['status'];
                    }
                }
            }
        }

        $total = $model->where($where)->count();
        $data['list'] = array_values($list);
        $data['total'] = intval($total);
        $this->success('success', $data);
    }




    /**
     * 婚礼头条详情
    */
    public function weddingDetail() {
        $wedding_id = I('wedding_id');
        $uid = $this->user['uid'];
        if (empty($wedding_id)) {
            $this->error('参数错误！');
        }
        $model_wedding = M('SchoolWedding');
        $model_comment_reply = M('SchoolWeddingComment');
        $whereWedding['wtw_school_wedding.status'] = 1;
        $whereWedding['wtw_school_wedding.id'] = $wedding_id;
        $detail = $model_wedding->join('left join wtw_school_wedding_category on wtw_school_wedding_category.id=wtw_school_wedding.category_id')
            ->where($whereWedding)
            ->field('wtw_school_wedding.id,wtw_school_wedding.headline,wtw_school_wedding.brief,wtw_school_wedding.content,wtw_school_wedding.create_time,wtw_school_wedding_category.name as category,wtw_school_wedding.share_url')
            ->find();
        //&amp替换为&
        $str = preg_replace('/&amp;/','&',$detail['headline']);
        $detail['headline'] = $str;
        //内容解析
        if (!empty($detail['content'])) {
            $detail['content'] = htmlspecialchars_decode($detail['content']);
        }
        //获取评论、回复总数
        $comment_count = $model_comment_reply->where(array('remark_id' => $wedding_id, 'status' => 1))->group('remark_id')->count();
        $detail['comment_count'] = intval($comment_count);
        //获取收藏状态
        $status_favorite = M('SchoolWeddingFavorites')->where(array('uid' => $uid, 'wedding_id' => $wedding_id))->field('status')->find();
        $detail['status_favorite'] = $status_favorite ? $status_favorite['status'] : -1;
        //获取分享页图片
        $imgs_url = $this->get_imgs($wedding[] = $wedding_id, 'cover');
        //获取访问总数、点赞总数和点赞状态
        $visitCount = M('WeddingVisitcount')->where(array('wedding_id'=>$wedding_id,'status'=>1))->field('wedding_id,count')->find();
        $praiseCount = M('schoolWeddingWeddingpraise')->where(array('wedding_id'=>$wedding_id,'status'=>1))->group('wedding_id')->field('wedding_id,count(id) as count')->find();
        $status_praise = M('schoolWeddingWeddingpraise')->where(array('wedding_id'=>$wedding_id,'uid'=>$uid))->field('wedding_id,status')->find();
        $detail['imgs'] = $imgs_url ? $imgs_url : array();
        $detail['visitCount'] = $visitCount['count'] ? intval($visitCount['count'])+1: 0;
        $detail['praiseCount'] = $praiseCount['count'] ? intval($praiseCount['count']) : 0;
        $detail['status_praise'] = $status_praise ? intval($status_praise['status']) : -1;
        $data['detail'] = $detail;
        $source['wedding_id'] = $wedding_id;
        $source['visit_ip'] = get_client_ip();
        $source['uid'] = $uid;
        $this->countVisits($source);
        $this->success('success', $data);

    }

    /**
     * 婚礼头条评论和回复列表
    */
    public function weddingComment() {
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $wedding_id = I('wedding_id');
        if(empty($wedding_id)){
            $this->error('参数错误！');
        }
        $uid = $this->user['uid'];
        $model_comment_reply = M('SchoolWeddingComment');
        $whereComment['status'] = 1;
        $whereComment['remark_id'] = $wedding_id;
        $comment = $model_comment_reply->where($whereComment)
            ->page($page, $per_page)->order('create_time desc')
            ->select();
        foreach ($comment as $key=>$value){
            $uid_arr[] =$value['uid'];
        }
        if(!empty($uid_arr)){
            $where['uid']=array('in',$uid_arr);
            $where['status'] =1;
            $position = M('Userinfo')->where($where)->field('uid,position')->select();
            foreach ($comment as $key_com=>$value_com){
                $comment[$key_com]['position'] ='';
                foreach ($position as $key_pos=>$value_pos)
                if($value_com['uid']==$value_pos['uid']){
                    $comment[$key_com]['position'] = $value_pos['position'];
                }
            }

        }
        //获取点赞状态
        $wherePraise = array();
        foreach ($comment as $key => $value) {
            $id_arr[] = $value['id'];
        }
        if (empty($id_arr)) {
            $data['comment'] = array();
            $this->success('success', $data);
        }
        $wherePraise['comment_id'] = array('in', $id_arr);
        $wherePraise['uid'] = $uid;
        $status_praise_arr = M('SchoolWeddingPraise')->where($wherePraise)->field('comment_id,status')->select();
        unset($wherePraise['uid']);
        $wherePraise['status'] = 1;
        //获取点赞数
        $praise_count = M('SchoolWeddingPraise')->where($wherePraise)->group('comment_id')->field('comment_id,count(id ) as count_praise')->select();
        //点赞状态绑定
        if (!empty($status_praise_arr)) {
            foreach ($comment as $key => $value) {
                $comment[$key]['status_praise'] = 0;
                foreach ($status_praise_arr as $key_praise => $value_praise) {
                    if ($value['id'] == $value_praise['comment_id']) {
                        $comment[$key]['status_praise'] = intval($value_praise['status']);
                    }
                }
                if ($value['type'] == 'reply') {
                    $parent_id[] = $value['parent_id'];
                }
            }
        } else {
            //登录和非登录情况
            if (empty($uid)) {
                foreach ($comment as $key => $value) {
                    $comment[$key]['status_praise'] = -1;
                    if ($value['type'] == 'reply') {
                        $parent_id[] = $value['parent_id'];
                    }
                }
            } else {
                foreach ($comment as $key => $value) {
                    $comment[$key]['status_praise'] = 0;
                    if ($value['type'] == 'reply') {
                        $parent_id[] = $value['parent_id'];
                    }
                }
            }
        }
        //点赞数绑定
        if (!empty($praise_count)) {
            foreach ($comment as $key => $value) {
                $comment[$key]['count_praise'] = 0;
                foreach ($praise_count as $key_praise_count => $value_praise_count) {
                    if ($value['id'] == $value_praise_count['comment_id']) {
                        $comment[$key]['count_praise'] = intval($value_praise_count['count_praise']);
                    }
                }
            }
        } else {
            foreach ($comment as $key => $value) {
                $comment[$key]['count_praise'] = 0;
            }
        }
        if (!empty($parent_id)) {
            $whereReply['id'] = array('in', $parent_id);
            $whereReply['status'] = 1;
            $reply = $model_comment_reply->where($whereReply)
                ->order('wtw_school_wedding_comment.create_time desc')->select();
            foreach ($reply as $key=>$value){
                $uid_arr[] =$value['uid'];
            }
            if(!empty($uid_arr)){
                $where['uid']=array('in',$uid_arr);
                $where['status'] =1;
                $position = M('Userinfo')->where($where)->field('uid,position')->select();
                foreach ($reply as $key_rep=>$value_rep){
                    $reply[$key_rep]['position'] ='';
                    foreach ($position as $key_pos=>$value_pos)
                        if($value_rep['uid']==$value_pos['uid']){
                            $reply[$key_rep]['position'] = $value_pos['position'];
                        }
                }

            }
            //回复和父节点回复绑定
            foreach ($comment as $key_comment => $value_comment) {
                $comment[$key_comment]['parent_reply'] = array();
                foreach ($reply as $key_reply => $value_reply) {
                    if ($value_comment['parent_id'] == $value_reply['id'] && $value_comment['type'] == 'reply') {
                        $comment[$key_comment]['parent_reply'][] = $reply[$key_reply];
                    }
                }
            }
        } else {
            foreach ($comment as $key_comment => $value_comment) {
                $comment[$key_comment]['parent_reply'] = array();
            }
        }
        $data['comment'] = array_values($comment);
        $this->success('success', $data);
    }


   /**
    * 婚礼头条评论提交接口
   */
    public function commentPost() {
        $model = D('SchoolWeddingComment');
        $data['parent_id'] = I('wedding_id');
        $data['uid'] = $this->user['uid'];
        $data['wsq_id'] = $this->user['wsq']->uid;
        $user = getTrueName($data['uid']);
        if(!empty($user)){
            $data['username'] = $user['truename'];
        }else{
            $data['username'] = $this->user['username'];
        }
        $data['headimg'] = $this->user['avatar'];
        $data['content'] = I('content');
        $data['type'] = 'comment';
        $data['remark_id'] = I('wedding_id');
        if (empty($data['parent_id'])) {
            $this->error('参数错误！');
        }
        if ($model->create($data)) {
            $id = $model->add();
            if ($id) {
                $this->success('评论成功！');
            } else {
                $this->error('评论失败！');
            }
        } else {
            $this->error($model->getError());
        }
    }

    /**
     * 婚礼头条回复接口
    */
    public function reply() {
        $model = D('SchoolWeddingComment');
        $parent_id = I('parent_id');
        if (empty($parent_id)) {
            $this->error('参数错误！');
        }
        $data = $model->where("id=$parent_id")->field('id as parent_id,username,headimg,content,create_time,remark_id as wedding_id')->find();
        if (empty($data)) {
            $this->error('该条记录不存在！');
        }
        $this->success('success', $data);
    }

    /**
     * 婚礼头条回复提交接口
    */
    public function replyPost() {
        //$object_push = A('Push');
        $model = D('SchoolWeddingComment');
        $data['parent_id'] = I('parent_id');
        $data['uid'] = $this->user['uid'];
        $data['wsq_id'] = $this->user['wsq']->uid;
        $user = getTrueName($data['uid']);
        if(!empty($user)){
            $data['username'] = $user['truename'];
        }else{
            $data['username'] = $this->user['username'];
        }
        $data['headimg'] = $this->user['avatar'];
        $data['type'] = 'reply';
        $data['remark_id'] = I('wedding_id');
        $data['content'] = I('content');
        if (empty($data['parent_id'])) {
            $this->error('参数错误！');
        }
        if ($model->create($data)) {
            $id = $model->add();
            if ($id) {
                /*$parent_data = $this->get_parent_data($data['parent_id']);
                $result = $object_push->pushMsgPersonal(array('uid'=>$parent_data['uid'],'content'=>$data['content'],'extra'=>array('username'=>$data['username'],'headimg'=>$data['headimg'],'parent_data'=>$parent_data)));
                $msg_id = $result->data->msg_id ? $result->data->msg_id : '';
                $this->success('回复成功！',array('msg_id'=>$msg_id));*/
                $this->success('回复成功！');
            } else {
                $this->error('回复失败！');
            }
        } else {
            $this->error($model->getError());
        }

    }


    /**
     * 用户举报
    */
    public function reportUser() {
        $data['comment_id'] = I('comment_id');
        $data['uid_be'] = I('uid_be');
        $data['uid_do'] = $this->user['uid'];
        $data['wsq_id'] = $this->user['wsq']->uid;
        $data['type_report'] = I('type');
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['status'] = 1;
        if (empty($data['uid_be']) || empty($data['type_report']) || empty($data['comment_id'])) {
            $this->error('参数错误！');
        }
        $modelComment = D('SchoolWeddingComment');
        $whereComment['uid'] = $data['uid_be'];
        $whereComment['id'] = $data['comment_id'];
        $modelReport = M('SchoolWeddingReport');
        $whereReport['uid_do'] = $data['uid_do'];
        $whereReport['comment_id'] = $data['comment_id'];
        $whereReport['status'] = 1;
        $whereReport['type_report'] = $data['type_report'];
        $comment = $modelComment->where($whereComment)->find();
        $report = $modelReport->where($whereReport)->find();
        if (!empty($report)) {
            $this->error('您已经对该条内容进行过该种类型的举报！');
        }
        if (empty($comment)) {
            $this->error('该条记录不存在！');
        }
        $id = M('SchoolWeddingReport')->add($data);
        if ($id) {
            $count = $modelReport->where(array('comment_id' => $data['comment_id']))->count('id');
            if ($count > 10) {
                $comment['status'] = 0;
                $result = $modelComment->save($comment);
                if ($result !== false) {
                    $this->success('举报成功！');
                } else {
                    $this->error('举报失败！');
                }
            } else {
                $this->success('您的举报已经提交！');
            }

        } else {
            $this->error('举报失败！');
        }
    }


    /**
     * 头条评论点赞
    */
    public function praise() {
        $data['comment_id'] = I('comment_id');
        $data['uid'] = $this->user['uid'];
        $data['wsq_id'] = $this->user['wsq']->uid;
        $user = getTrueName($data['uid']);
        if(!empty($user)){
            $data['username'] = $user['truename'];
        }else{
            $data['username'] = $this->user['username'];
        }
        $data['headimg'] = $this->user['avatar'];
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['status'] = 1;
        $comment = D('SchoolWeddingComment')->where(array('id' => $data['comment_id']))->find();
        if (empty($comment)) {
            $this->error('参数错误！');
        }
        $model = M('SchoolWeddingPraise');
        $where['uid'] = $data['uid'];
        $where['comment_id'] = $data['comment_id'];
        //$where['status'] =1;
        $praise = $model->where($where)->find();
        if (!empty($praise)) {
            if ($praise[status] == 1) {
                $this->error('您已经对该条内容点赞过了！');
            } else {
                $praise['status'] = 1;
                $praise['update_time'] = time();
                $result = $model->save($praise);
                if ($result !== false) {
                    $this->success('点赞成功！');
                } else {
                    $this->error('点赞失败！');
                }
            }
        }
        $id = $model->add($data);
        if ($id) {
            $this->success('点赞成功！');
        } else {
            $this->error('点赞失败！');
        }
    }

    /**
     * 评论取消取消点赞
    */
    public function cancelPraise() {
        $comment_id = I('comment_id');
        $uid = $this->user['uid'];
        if (empty($comment_id)) {
            $this->error('参数错误！');
        }
        $model = M('SchoolWeddingPraise');
        $where['uid'] = $uid;
        $where['comment_id'] = $comment_id;
        $where['status'] = 1;
        $praise = $model->where($where)->find();
        if (empty($praise)) {
            $this->error('您不曾对该内容点赞过！');
        }
        $praise['update_time'] = time();
        $praise['status'] = 0;
        $result = $model->save($praise);
        if ($result !== false) {
            $this->success('取消点赞成功！');
        } else {
            $this->error('取消点赞失败！');
        }
    }


   /**
    * 头条收藏
   */
    public function favorite() {
        $model = M('SchoolWeddingFavorites');
        $data['wedding_id'] = I('wedding_id');
        $data['uid'] = $this->user['uid'];
        $data['wsq_id'] = $this->user['wsq']->uid;
        $user = getTrueName($data['uid']);
        if(!empty($user)){
            $data['username'] = $user['truename'];
        }else{
            $data['username'] = $this->user['username'];
        }
        $data['headimg'] = $this->user['avatar'];
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['status'] = 1;
        if (empty($data['wedding_id'])) {
            $this->error('参数错误！');
        }
        $where['uid'] = $data['uid'];
        $where['wedding_id'] = $data['wedding_id'];
        $favorite = $model->where($where)->find();
        if (!empty($favorite)) {
            if ($favorite['status'] == 1) {
                $this->error('您已经收藏过该头条，不能重复收藏！');
            } else {
                $favorite['status'] = 1;
                $favorite['update_time'] = time();
                $result = $model->save($favorite);
                if ($result !== false) {
                    $this->success('收藏成功！');
                } else {
                    $this->error('收藏失败！');
                }
            }
        }
        $id = $model->add($data);
        if ($id) {
            $this->success('收藏成功！');
        } else {
            $this->error('收藏失败！');
        }
    }

    /**
     * 取消收藏
    */
    public function cancelFavorite() {
        $model = M('SchoolWeddingFavorites');
        $wedding_id = I('wedding_id');
        $uid = $this->user['uid'];
        if (empty($wedding_id)) {
            $this->error('参数错误！');
        }
        $where['uid'] = $uid;
        $where['wedding_id'] = $wedding_id;
        $where['status'] = 1;
        $favorite = $model->where($where)->find();
        if (empty($favorite)) {
            $this->error('您不曾收藏过该内容！');
        }
        $favorite['update_time'] = time();
        $favorite['status'] = 0;
        $result = $model->save($favorite);
        if ($result !== false) {
            $this->success('取消收藏成功！');
        } else {
            $this->error('取消收藏失败！');
        }

    }

    /**
     * 评论详情
    */
    public function commentDetail() {
        $comment_id = I('comment_id');
        if (empty($comment_id)) {
            $this->error('参数错误！');
        }
        $comentDetail = M('SchoolWeddingComment')->where("id=$comment_id")
            ->field('id as comment_id,uid,content,create_time,username,headimg,wsq_id')
            ->find();
        if (empty($comentDetail)) {
            $this->success('暂时没有内容！', (object)$comentDetail);
        }
        $whereUser['uid']=$comentDetail['uid'];
        $whereUser['status']=1;
        $user = M('Userinfo')->where($whereUser)->field('position,company')->find();
        $comentDetail['posiiton']=$user['position'] ? $user['position'] : '';
        $comentDetail['company']=$user['company'] ? $user['company'] : '';
        $data = $comentDetail;
        $this->success('success', $data);
    }

    /**
     * 点赞详情
    */
    public function praiseDetail() {
        $comment_id = I('comment_id');
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        if (empty($comment_id)) {
            $this->error('参数错误！');
        }
        $where['comment_id'] = $comment_id;
        $where['status'] = 1;
        $praiseDetail = M('SchoolWeddingPraise')
            ->where($where)->page($page,$per_page)->field('uid,username,headimg,wsq_id')
            ->select();
        if (empty($praiseDetail)) {
            $data['praiseDetail'] = array();
            $this->success('暂时没有内容！', $data);
        }
        foreach($praiseDetail as $key_praise=>$value_praise){
            $uid_arr[]=$value_praise['uid'];
        }
        $whereUser['uid']=array('in',$uid_arr);
        $whereUser['status']=1;
        $user = M('Userinfo')->where($whereUser)->field('uid,position,company')->select();
        foreach($praiseDetail as $key_pra=>$value_pra){
            $praiseDetail[$key_pra]['position']='';
            $praiseDetail[$key_pra]['company']='';
            foreach($user as $key_user=>$value_user){
                if($value_pra['uid']==$value_user['uid']){
                    $praiseDetail[$key_pra]['position']=$value_user['position'];
                    $praiseDetail[$key_pra]['company']=$value_user['company'];
                }
            }
        }
        $data['praiseDetail'] = $praiseDetail;
        $this->success('success', $data);
    }


    /**
     * 我的——评论
    */
    public function myComments() {
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $uid = $this->user['uid'];
        $model_comment_reply = M('SchoolWeddingComment');
        $whereComment['status'] = 1;
        $whereComment['uid'] = $uid;
        $whereComment['type'] = 'comment';
        $comment = $model_comment_reply->where($whereComment)
            ->page($page, $per_page)->order('wtw_school_wedding_comment.create_time desc')
            ->select();
        //获取职位
        foreach ($comment as $key=>$value){
            $uid_arr[] =$value['uid'];
        }
        if(!empty($uid_arr)){
            $where['uid']=array('in',$uid_arr);
            $where['status'] =1;
            $position = M('Userinfo')->where($where)->field('uid,position')->select();
            foreach ($comment as $key_com=>$value_com){
                $comment[$key_com]['position'] ='';
                foreach ($position as $key_pos=>$value_pos)
                    if($value_com['uid']==$value_pos['uid']){
                        $comment[$key_com]['position'] = $value_pos['position'];
                    }
            }

        }

        //获取点赞状态
        $wherePraise = array();
        foreach ($comment as $key => $value) {
            $id_arr[] = $value['id'];
        }
        if (empty($id_arr)) {
            $data['comment'] = array();
            $this->success('success', $data);
        }
        $wherePraise['comment_id'] = array('in', $id_arr);
        $wherePraise['uid'] = $uid;
        $status_praise_arr = M('SchoolWeddingPraise')->where($wherePraise)->field('comment_id,status')->select();
        unset($wherePraise['uid']);
        $wherePraise['status'] = 1;
        //获取点赞数
        $praise_count = M('SchoolWeddingPraise')->where($wherePraise)->group('comment_id')->field('comment_id,count(id ) as count_praise')->select();
        //点赞状态绑定
        foreach ($comment as $key => $value) {
            $comment[$key]['status_praise'] = 0;
            if (!empty($status_praise_arr)) {
                foreach ($status_praise_arr as $key_praise => $value_praise) {
                    if ($value['id'] == $value_praise['comment_id']) {
                        $comment[$key]['status_praise'] = intval($value_praise['status']);
                    }
                }
            }
        }
        //点赞数绑定
        if (!empty($praise_count)) {
            foreach ($comment as $key => $value) {
                $comment[$key]['count_praise'] = 0;
                foreach ($praise_count as $key_praise_count => $value_praise_count) {
                    if ($value['id'] == $value_praise_count['comment_id']) {
                        $comment[$key]['count_praise'] = intval($value_praise_count['count_praise']);
                    }
                }
            }
        } else {
            foreach ($comment as $key => $value) {
                $comment[$key]['count_praise'] = 0;
            }
        }
        foreach ($comment as $key => $value) {
            if ($value['type'] == 'reply') {
                $parent_id[] = $value['parent_id'];
            }
            if ($value['type'] == 'comment') {
                $parent_id_wedding[] = $value['parent_id'];
            }
        }
        if (!empty($parent_id)) {
            $whereReply['id'] = array('in', $parent_id);
            $whereReply['status'] = 1;
            $reply = $model_comment_reply->where($whereReply)
                ->order('wtw_school_wedding_comment.create_time desc')->select();
            //获取职位
            foreach ($reply as $key=>$value){
                $uid_arr[] =$value['uid'];
            }
            if(!empty($uid_arr)){
                $where['uid']=array('in',$uid_arr);
                $where['status'] =1;
                $position = M('Userinfo')->where($where)->field('uid,position')->select();
                foreach ($reply as $key_rep=>$value_rep){
                    $reply[$key_rep]['position'] ='';
                    foreach ($position as $key_pos=>$value_pos)
                        if($value_rep['uid']==$value_pos['uid']){
                            $reply[$key_rep]['position'] = $value_pos['position'];
                        }
                }

            }

            //回复和父节点回复绑定
            foreach ($comment as $key_comment => $value_comment) {
                $comment[$key_comment]['parent_reply'] = array();
                foreach ($reply as $key_reply => $value_reply) {
                    if ($value_comment['parent_id'] == $value_reply['id'] && $value_comment['type'] == 'reply') {
                        $comment[$key_comment]['parent_reply'][] = $reply[$key_reply];
                    }
                }
            }
        } else {
            foreach ($comment as $key_comment => $value_comment) {
                $comment[$key_comment]['parent_reply'] = array();
            }
        }
        $wedding_id = $model_comment_reply->where("uid=$uid")->group('remark_id')->field('remark_id')->select();
        foreach ($wedding_id as $key => $value) {
            $wedding_id_arr[] = $value['remark_id'];
        }
        if (!empty($wedding_id_arr)) {
            $wedding = M('SchoolWedding')->where(array('id' => array('in', $wedding_id_arr)))->field('id,headline,brief,create_time')->select();
            //头条和封面绑定
            $imgs_url = $this->get_imgs($wedding_id_arr, 'cover');
            foreach ($wedding as $key_wedding => $value_wedding) {
                $wedding[$key_wedding]['imgs'] = array();
                foreach ($imgs_url as $key_img => $value_img) {
                    if ($value_wedding['id'] == $value_img['record_id']) {
                        $wedding[$key_wedding]['imgs'][] = $imgs_url[$key_img];
                    }
                }
            }
            //获取头条评论数、访问量
            $visit_count = M('WeddingVisitcount')->where(array('wedding_id'=>array('in',$wedding_id_arr),'status'=>1))->field('wedding_id,count')->select();
            $comment_count = M('schoolWeddingComment')->where(array('remark_id'=>array('in',$wedding_id_arr),'status'=>1))->group('remark_id')->field('remark_id as wedding_id,count(id) as count')->select();
            foreach ($wedding as $key=>$value){
                $wedding[$key]['visitCount'] = 0;
                $wedding[$key]['comment_count'] = 0;
                if(!empty($visit_count)){
                    foreach ($visit_count as $visit_key=>$visit_value){
                        if($value['id']==$visit_value['wedding_id']){
                            $wedding[$key]['visitCount'] = $visit_value['count'];
                        }
                    }
                }
                if(!empty($comment_count)){
                    foreach ($comment_count as $com_key=>$com_value){
                        if ($value['id']==$com_value['wedding_id']){
                            $wedding[$key]['comment_count'] = $com_value['count'];
                        }
                    }
                }
            }

            //comment和wedding绑定
            foreach ($comment as $key_comment => $value_comment) {
                $comment[$key_comment]['parent_wedding'] = array();
                foreach ($wedding as $key_wedding => $value_wedding) {
                    if ($value_comment['remark_id'] == $value_wedding['id']) {
                        $comment[$key_comment]['parent_wedding'][] = $wedding[$key_wedding];
                    }
                }
            }
        }





        $data['comment'] = array_values($comment);
        $this->success('success', $data);
    }

    /**
     * 我的——删除评论
    */
    public function myCommentDelete() {
        $comment_id = I('comment_id');
        $where['id'] = $comment_id;
        $model = M('SchoolWeddingComment');
        $comment = $model->where($where)->find();
        if (empty($comment_id)) {
            $this->error('参数错误！');
        }
        if (empty($comment)) {
            $this->error('不存在该条记录！');
        }
        if ($comment['status'] == 0) {
            $this->success('该条评论已经被删除！');
        }
        $comment['status'] = 0;
        $comment['update_time'] = time();
        $result = $model->save($comment);
        if ($result !== false) {
            $this->success('删除评论成功！');
        } else {
            $this->error('删除评论失败！');
        }
    }

    /**
     * 我的——收藏
    */
    public function myFavorites() {
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $uid = $this->user['uid'];
        $where['wtw_school_wedding_favorites.uid'] = $uid;
        $where['wtw_school_wedding_favorites.status'] = 1;
        $where['a.status'] = 1;
        $list = M('SchoolWeddingFavorites')->join('left join wtw_school_wedding as a on wtw_school_wedding_favorites.wedding_id=a.id')
            ->where($where)->field('a.id,a.headline,a.brief,a.create_time,wtw_school_wedding_favorites.wsq_id')->page($page, $per_page)->order('wtw_school_wedding_favorites.update_time desc')->select();
        $total = M('SchoolWeddingFavorites')->join('left join wtw_school_wedding as a on wtw_school_wedding_favorites.wedding_id=a.id')
            ->where($where)->field('a.id,a.headline,a.brief,a.create_time')->count();
        if (empty($list)) {
            $data['list'] = array();
            $this->success('内容为空！', $data);
        }
        //获取头条cover
        foreach ($list as $key => $value) {
            $wedding_id[] = $value['id'];
        }
        if (!empty($wedding_id)) {
            $imgs_url = $this->get_imgs($wedding_id, 'cover');
            foreach ($list as $key_list => $value_list) {
                $list[$key_list]['imgs'] = array();
                foreach ($imgs_url as $key_img => $value_img) {
                    if ($value_list['id'] == $value_img['record_id']) {
                        $list[$key_list]['imgs'][] = $imgs_url[$key_img];
                    }
                }
            }
            //获取头条评论数、访问量
            $visit_count = M('WeddingVisitcount')->where(array('wedding_id'=>array('in',$wedding_id),'status'=>1))->field('wedding_id,count')->select();
            $comment_count = M('schoolWeddingComment')->where(array('remark_id'=>array('in',$wedding_id),'status'=>1))->group('remark_id')->field('remark_id as wedding_id,count(id) as count')->select();
            foreach ($list as $key=>$value){
                $list[$key]['visitCount'] = 0;
                $list[$key]['comment_count'] = 0;
                if(!empty($visit_count)){
                    foreach ($visit_count as $visit_key=>$visit_value){
                        if($value['id']==$visit_value['wedding_id']){
                            $list[$key]['visitCount'] = $visit_value['count'];
                        }
                    }
                }
                if(!empty($comment_count)){
                    foreach ($comment_count as $com_key=>$com_value){
                        if ($value['id']==$com_value['wedding_id']){
                            $list[$key]['comment_count'] = $com_value['count'];
                        }
                    }
                }
            }

        }
        $data['list'] = array_values($list);
        $data['total'] = intval($total);
        $this->success('success', $data);
    }

    /**
     * 我的——删除收藏
    */
    public function myFavoritesDelete() {
        $wedding_id = I('wedding_id');
        $uid = $this->user['uid'];
        $where['uid'] = $uid;
        $where['wedding_id'] = $wedding_id;
        $model = M('SchoolWeddingFavorites');
        $favorite = $model->where($where)->find();
        if (empty($wedding_id)) {
            $this->error('参数错误！');
        }
        if (empty($favorite)) {
            $this->error('不存在该条收藏记录！');
        }
        if ($favorite['status'] == 0) {
            $this->success('您已经取消了对该头条的收藏！');
        }
        $favorite['status'] = 0;
        $favorite['update_time'] = time();
        $result = $model->save($favorite);
        if ($result !== false) {
            $this->success('取消收藏成功！');
        } else {
            $this->error('取消收藏失败！');
        }
    }

    /**
     * 发布活动
    */
    public function actionPublish() {
        $model = D('SchoolWeddingAction');
        $uid = $this->user['uid'];
        $data['uid'] = $uid;
        $data['headline'] = I('headline');
        $data['visitor_count'] = I('visitor_count');
        $data['address'] = I('address');
        $data['brief'] = I('brief');
        $data['conection'] = I('conection');
        $data['start_time'] = I('start_time');
        $data['end_time'] = I('end_time');
        if ($model->create($data)) {
            $id = $model->add();
            if ($id) {
                $this->success('活动发布成功！');
            } else {
                $this->error('活动发布失败！');
            }
        } else {
            $this->error($model->getError());
        }
    }

    /**
     * 我的消息——回复
    */
    public function myReply() {
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $model = M('SchoolWeddingComment');
        $uid = $this->user['uid'];
        $myWhere['status'] = 1;
        $myWhere['uid'] = $uid;
        $myWhere['type'] = 'reply';
        $myReply = $model->where($myWhere)
           ->page($page, $per_page)->order('wtw_school_wedding_comment.create_time desc')
            ->select();
        if(empty($myReply)){
            $data['myReply'] = array();
            $this->success('内容为空！', $data);
        }
        $user = $user = M('Userinfo')->where("uid=$uid and status=1")->find();

        foreach ($myReply as $key => $value) {
            $parent_id_arr[] = $value['parent_id'];
            if(empty($user)){
                $myReply[$key]['position']= '';
            }else{
                $myReply[$key]['position']= $user['position'];
            }
        }

        $parentWhere['id'] = array('in', $parent_id_arr);
        $parentReply = $model->where($parentWhere)->select();
        //获取职位
        foreach ($parentReply as $key=>$value){
            $uid_arr[] =$value['uid'];
        }
        if(!empty($uid_arr)){
            $where['uid']=array('in',$uid_arr);
            $where['status'] =1;
            $position = M('Userinfo')->where($where)->field('uid,position')->select();
            foreach ($parentReply as $key_rep=>$value_rep){
                $parentReply[$key_rep]['position'] ='';
                foreach ($position as $key_pos=>$value_pos)
                    if($value_rep['uid']==$value_pos['uid']){
                        $parentReply[$key_rep]['position'] = $value_pos['position'];
                    }
            }

        }

        //我的回复和父节点绑定
        foreach ($myReply as $key_reply => $value_reply) {
            foreach ($parentReply as $key_parent => $value_parent) {
                if ($value_reply['parent_id'] == $value_parent['id']) {
                    if ($value_parent['status'] == 1) {
                        $myReply[$key_reply]['parent'] = $parentReply[$key_parent];
                    } else {
                        $myReply[$key_reply]['parent'] = (object)array();
                    }

                }
            }
        }
        $data['myReply'] = array_values($myReply);
        $this->success('success', $data);
    }

    public function myReply1(){
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $model = M('SchoolWeddingComment');
        $uid = $this->user['uid'];
        $Where['uid'] = $uid;
        $user = M('Userinfo')->where("uid=$uid and status=1")->find();
        $Reply = $model->where($Where)->select();
        if(empty($Reply)){
            $data['myReply'] = array();
            $this->success('内容为空！', $data);
        }
        //获取我的评论或回复的id，并添加职位
        foreach ($Reply as $key=>$value){
            $Reply_id[] = $value['id'];
            $Reply[$key]['position']= $user['position'];
        }
        $myWhere['parent_id'] = array('in',$Reply_id);
        $myWhere['status'] =1;
        $myWhere['type']='reply';
        $myReply = $model->where($myWhere)->page($page, $per_page)->order('create_time desc')->select();
        if(empty($myReply)){
            $data['myReply'] = array();
            $this->success('内容为空！', $data);
        }

        //获取职位
        foreach ($myReply as $key=>$value){
            $uid_arr[] =$value['uid'];
        }
        if(!empty($uid_arr)){
            $where['uid']=array('in',$uid_arr);
            $where['status'] =1;
            $position = M('Userinfo')->where($where)->field('uid,position')->select();
            foreach ($myReply as $key_rep=>$value_rep){
                $myReply[$key_rep]['position'] ='';
                foreach ($position as $key_pos=>$value_pos)
                    if($value_rep['uid']==$value_pos['uid']){
                        $myReply[$key_rep]['position'] = $value_pos['position'];
                    }
            }

        }

        //我的回复和父节点绑定
        foreach ($myReply as $key_reply => $value_reply) {
            foreach ($Reply as $key_parent => $value_parent) {
                if ($value_reply['parent_id'] == $value_parent['id']) {
                    if ($value_parent['status'] == 1) {
                        $myReply[$key_reply]['parent'] = $Reply[$key_parent];
                    } else {
                        $myReply[$key_reply]['parent'] = (object)array();
                    }

                }
            }
        }


        $data['myReply'] = array_values($myReply);
        $this->success('success', $data);
    }

    /**
     * 我的消息——删除我的回复
    */
    public function myReplyDelete() {
        $model = M('SchoolWeddingComment');
        $reply_id = I('reply_id');
        if (empty($reply_id)) {
            $this->error('参数错误！');
        }
        $where['id'] = $reply_id;
        $reply = $model->where($where)->find();
        if (empty($reply)) {
            $this->error('该记录不存在！');
        }
        if ($reply['status'] == 0) {
            $this->success('该记录已经被删除！');
        }
        $reply['status'] = 0;
        $reply['update_time'] = time();
        $result = $model->save($reply);
        if ($result !== false) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 头条点赞
     */
    public function weddingPraise() {
        $data['wedding_id'] = I('wedding_id');
        $data['uid'] = $this->user['uid'];
        $data['wsq_id'] = $this->user['wsq']->uid;
        $user = getTrueName($data['uid']);
        if(!empty($user)){
            $data['username'] = $user['truename'];
        }else{
            $data['username'] = $this->user['username'];
        }
        $data['headimg'] = $this->user['avatar'];
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['status'] = 1;
        $wedding = D('SchoolWedding')->where(array('id' => $data['wedding_id']))->find();
        if (empty($wedding)) {
            $this->error('参数错误！');
        }
        $model = M('SchoolWeddingWeddingpraise');
        $where['uid'] = $data['uid'];
        $where['wedding_id'] = $data['wedding_id'];
        //$where['status'] =1;
        $praise = $model->where($where)->find();
        if (!empty($praise)) {
            if ($praise[status] == 1) {
                $this->error('您已经对该条内容点赞过了！');
            } else {
                $praise['status'] = 1;
                $praise['update_time'] = time();
                $result = $model->save($praise);
                if ($result !== false) {
                    $this->success('点赞成功！');
                } else {
                    $this->error('点赞失败！');
                }
            }
        }
        $id = $model->add($data);
        if ($id) {
            $this->success('点赞成功！');
        } else {
            $this->error('点赞失败！');
        }
    }

    /**
     * 头条取消点赞
     */
    public function weddingCancelPraise() {
        $wedding_id = I('wedding_id');
        $uid = $this->user['uid'];
        if (empty($wedding_id)) {
            $this->error('参数错误！');
        }
        $model = M('SchoolWeddingWeddingpraise');
        $where['uid'] = $uid;
        $where['wedding_id'] = $wedding_id;
        $where['status'] = 1;
        $praise = $model->where($where)->find();
        if (empty($praise)) {
            $this->error('您不曾对该内容点赞过！');
        }
        $praise['update_time'] = time();
        $praise['status'] = 0;
        $result = $model->save($praise);
        if ($result !== false) {
            $this->success('取消点赞成功！');
        } else {
            $this->error('取消点赞失败！');
        }
    }

    /**
     * 获取用户信息（根据wsq_id）
    */
    public function getWsqUser(){
        $wsq_id =I('wsq_id');
        if(empty($wsq_id)){
            $this->error('参数错误！');
        }
        $where['wsq_id'] = $wsq_id;
        $where['status'] =1;
        $user = M('Userinfo')->where($where)->field('wsq_id,truename,company,position')->find();
        $data['user'] = $user;
        $this->countHomepage($wsq_id);
        $this->success('success',$data);
    }

    /**
     * 获取个人主页（根据uid）
    */
    public function getPersonalHome(){
        $uid =I('uid');
        if(empty($uid)){
            $this->error('参数错误！');
        }
        $where['uid'] = $uid;
        $where['status'] =1;
        $user = M('Userinfo')->where($where)->find();
        if(empty($user['region_title'])){
            $user['region_title']= "";
        }
        $data['user'] = $user;
        $this->count_personal_home($uid);
        $this->success('success',$data);
    }


    /**
     * 公司搜索
    */
    public function getCompanyList() {
        $name = I('company');
        $region_1 = I('province');
        $region_2 = I('city');
        $region_3 = I('country');
        if (empty($name) || empty($region_1)) {
            $this->error('请选择地区并填写公司名');
        }
        $data = array('name' => $name, 'filter[region_id_1]' => $region_1, 'filter[store_id]' => 0);

        if (!empty($region_3)) {
            $data['filter[region_id_2]'] = $region_2;
        }
        $result = $this->company($data);
        if (!empty($result['data']['data'])) {
            foreach ($result['data']['data'] as $key => $value) {
                $list[] = array('id' => $value['id'], 'name' => $value['name'], 'address' => $value['address']);
            }
        } else {
            $list = array();
        }
        $company_list['company'] = $name;
        $company_list['list'] = array_values($list);
        $result['iRet'] == 1 ? $this->success('success', $company_list) : $this->error($result['info']);
    }


    public function company($data) {
        $api = C('AUTH_API_URL') . 'company?' . http_build_query($data);
        $result = curl_get($api, $data);
        return $result;
    }

    /**
     * 获取头条图片
    */
    public function get_imgs($wedding_id = array(), $remark = '') {
        if ($remark == 'cover') {
            $where['module'] = "SchoolWeddingCover";
        } elseif ($remark == 'detail') {
            $where['module'] = "SchoolWedding";
        }
        $base_url = 'http://7xopel.com2.z0.glb.clouddn.com/';
        $where['status'] = 1;
        $where['record_id'] = array('in', $wedding_id);
        $imgs_url = M('Attach')->where($where)->field('record_id,id as attach_id,savename as url')->select();
        foreach ($imgs_url as $key => $value) {
            $imgs_url[$key]['url'] = $base_url . $value['url'];
        }
        return $imgs_url;
    }

    /**
     * 头条访问量统计及访问记录
     */
    public function countVisits($source = array()) {
        $model_visits = M('SchoolWeddingVisits');
        $model_visitcount = M('WeddingVisitcount');
        $data['wedding_id'] = $source['wedding_id'];
        $data['visit_ip'] = $source['visit_ip'];
        $data['uid'] = $source['uid'] ? $source['uid'] : 0;
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['status'] = 1;
        $model_visits->add($data);
        $where['wedding_id'] = $source['wedding_id'];
        $where['status'] = 1;
        $count = $model_visitcount->where($where)->find();
        if (!empty($count)) {
            $count['count'] += 1;
            $count['update_time'] = time();
            $model_visitcount->save($count);
        } else {
            $count['count'] = 0;
            $count['wedding_id'] = $source['wedding_id'];
            $count['count'] += 1;
            $count['create_time'] = time();
            $count['update_time'] = time();
            $count['status'] = 1;
            $model_visitcount->add($count);
        }
    }


    /**
     * 个人主页访问统计(根据wsq_id)
    */
    public function countHomepage($wsq_id){
        $model = M('HomepageVisitcount');
        $where['wsq_id'] =$wsq_id;
        $where['status'] = 1;
        $visits = $model->where($where)->find();
        if(empty($visits)){
            $visits['visits_count'] =0;
            $visits['visits_count']+=1;
            $visits['wsq_id'] = $wsq_id;
            $visits['create_time'] =time();
            $visits['update_time'] =time();
            $visits['status'] =1;
            $model->add($visits);
        }else{
            $visits['visits_count']+=1;
            $visits['update_time'] =time();
            $model->save($visits);
        }
    }

    /**
     * 个人主页访问统计（根据uid）
    */
    public function count_personal_home($uid){
        $model = M('HomepageVisitcount');
        $where['uid'] =$uid;
        $where['status'] = 1;
        $visits = $model->where($where)->find();
        if(empty($visits)){
            $visits['visits_count'] =0;
            $visits['visits_count']+=1;
            $visits['uid'] = $uid;
            $visits['create_time'] =time();
            $visits['update_time'] =time();
            $visits['status'] =1;
            $model->add($visits);
        }else{
            $visits['visits_count']+=1;
            $visits['update_time'] =time();
            $model->save($visits);
        }
    }

    /**
     * 获取父评论
     */
    public function get_parent_data($parent_id){
        $where['id'] = $parent_id;
        $parent_data = M('schoolWeddingComment')->where($where)->field('uid,content,username,headimg')->find();
        return $parent_data;
    }




    







}