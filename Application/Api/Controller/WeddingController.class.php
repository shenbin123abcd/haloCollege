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
    protected $action_auth = array('commentPost','replyPost','reply','reportUser','praise','favorite','cancelFavorite','cancelPraise'
    ,'myCommentDelete','myComments','myFavorites','myFavoritesDelete');

    //头条分类
    public function category(){
        $category = M('SchoolWeddingCategory')->where("status=1")->field('id,name')->select();
        $data['category'] = !empty($category) ? array_values($category) : (object)$category;
        $this->success('success',$data);
    }

    //婚礼头条列表
    public function weddingList(){
        $page = I('page') ? I('page') : 0;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $category_id = I('category_id');
        if(empty($category_id)){
            $this->error('参数错误！');
        }
        $model = M('SchoolWedding');
        $where['wtw_school_wedding.status']=1;
        $where['wtw_school_wedding.category_id']=$category_id;
        //分页
        if($page!=0){
            $list = $model->join('left join wtw_school_wedding_category on wtw_school_wedding.category_id=wtw_school_wedding_category.id')->where($where)->order("wtw_school_wedding.create_time desc")
                ->field("wtw_school_wedding.id,wtw_school_wedding_category.name,wtw_school_wedding.headline,wtw_school_wedding.brief,wtw_school_wedding.create_time")
                ->page($page,$per_page)->select();
        }else{
            $list = $model->join('left join wtw_school_wedding_category on wtw_school_wedding.category_id=wtw_school_wedding_category.id')->where($where)->order("wtw_school_wedding.create_time desc")
                ->field("wtw_school_wedding.id,wtw_school_wedding_category.name,wtw_school_wedding.headline,wtw_school_wedding.brief,wtw_school_wedding.create_time")->select();

        }
        if(empty($list)){
            $this->success('内容为空！',(object)$list);
        }
        //获取头条cover
        foreach ($list as $key=>$value){
            $wedding_id[] = $value['id'];
        }
        if(!empty($wedding_id)){
            $imgs_url = $this->get_imgs($wedding_id,'cover');
                foreach ($list as $key_list=>$value_list){
                    $list[$key_list]['imgs'] = array();
                    foreach ($imgs_url as $key_img=>$value_img){
                        if($value_list['id']==$value_img['record_id']){
                            $list[$key_list]['imgs'][]=$imgs_url[$key_img];
                        }
                    }
                }
        }
        $total = $model->where($where)->count();
        $data['list'] = array_values($list);
        $data['total'] = intval($total);
        $this->success('success',$data);
    }


    //婚礼头条详情
    public function weddingDetail(){
        $wedding_id = I('wedding_id');
        $uid = $this->user['uid'];
        if(empty($wedding_id)){
            $this->error('参数错误！');
        }
        $model_wedding = M('SchoolWedding');
        $model_comment_reply = M('SchoolWeddingComment');
        $whereWedding['status']=1;
        $whereWedding['id']=$wedding_id;
        $detail = $model_wedding->where($whereWedding)->field('id,headline,brief,content,create_time')->find();
        //内容解析
        if(!empty($detail['content'])){
            $detail['content'] = htmlspecialchars_decode($detail['content']);
        }
        //获取评论、回复总数
        $comment_count = $model_comment_reply->where(array('remark_id'=>$wedding_id,'status'=>1))->group('remark_id')->count();
        $detail['comment_count'] = intval($comment_count);
        //获取收藏状态
        $status_favorite = M('SchoolWeddingFavorites')->where(array('uid'=>$uid,'wedding_id'=>$wedding_id))->field('status')->find();
        $detail['status_favorite'] = $status_favorite ? $status_favorite['status'] : -1;
        //获取头条详情图片
        $imgs_url = $this->get_imgs($wedding[]=$wedding_id,'detail');
        $detail['imgs'] = $imgs_url ? $imgs_url : array();
        $data['detail'] = $detail;
        $this->success('success',$data);
        
    }

    //婚礼头条评论和回复
    public function weddingComment(){
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $wedding_id = I('wedding_id');
        $uid = $this->user['uid'];
        $model_comment_reply = M('SchoolWeddingComment');
        $whereComment['status']=1;
        $whereComment['remark_id']=$wedding_id;
        $comment = $model_comment_reply->where($whereComment)->page($page,$per_page)->order('create_time desc')->select();
        //获取点赞状态
        $wherePraise = array();
        foreach ($comment as $key=>$value){
            $id_arr[] = $value['id'];
        }
        if(!empty($id_arr)){
            $wherePraise['comment_id'] = array('in',$id_arr);
        }

        $wherePraise['uid'] = $uid;
        $status_praise_arr = M('SchoolWeddingPraise')->where($wherePraise)->field('comment_id,status')->select();
        unset($wherePraise['uid']);
        $wherePraise['status'] = 1;
        //获取点赞数
        $praise_count = M('SchoolWeddingPraise')->where($wherePraise)->group('comment_id')->field('comment_id,count(id ) as count_praise')->select();
        //点赞状态绑定
        if(!empty($status_praise_arr)){
            foreach ($comment as $key=>$value){
                $comment[$key]['status_praise'] = 0;
                foreach ($status_praise_arr as $key_praise=>$value_praise){
                    if($value['id']==$value_praise['comment_id']){
                        $comment[$key]['status_praise'] = intval($value_praise['status']);
                    }
                }
                if($value['type']=='reply'){
                    $parent_id[] =$value['parent_id'];
                }
            }
        }else{
            foreach ($comment as $key=>$value){
                $comment[$key]['status_praise'] = -1;
                if($value['type']=='reply'){
                    $parent_id[] =$value['parent_id'];
                }
            }

        }
        //点赞数绑定
        if(!empty($praise_count)){
            foreach ($comment as $key=>$value){
                $comment[$key]['count_praise'] = 0;
                foreach ($praise_count as $key_praise_count=>$value_praise_count){
                    if($value['id']==$value_praise_count['comment_id']){
                        $comment[$key]['count_praise'] = intval($value_praise_count['count_praise']);
                    }
                }
            }
        }else{
            foreach ($comment as $key=>$value) {
                $comment[$key]['count_praise'] = 0;
            }
        }
        if(!empty($parent_id)){
            $whereReply['id'] = array('in',$parent_id);
            $whereReply['status'] =1;
            $reply = $model_comment_reply->where($whereReply)->order('create_time desc')->select();
            //回复和父节点回复绑定
            foreach ($comment as $key_comment=>$value_comment){
                $comment[$key_comment]['parent_reply'] = array();
                foreach ($reply as $key_reply=>$value_reply){
                    if($value_comment['parent_id']==$value_reply['id'] && $value_comment['type']=='reply'){
                        $comment[$key_comment]['parent_reply'][]=$reply[$key_reply];
                    }
                }
            }
        }else{
            foreach ($comment as $key_comment=>$value_comment){
                $comment[$key_comment]['parent_reply'] = array();
            }
        }
        $data['comment'] = array_values($comment);
        $this->success('success',$data);
    }


    //婚礼头条评论提交接口
    public function commentPost(){
        $model = D('SchoolWeddingComment');
        $data['parent_id'] = I('wedding_id');
        $data['uid'] = $this->user['uid'];
        $data['username'] = $this->user['username'];
        $data['headimg'] = $this->user['avatar'];
        $data['content'] = I('content');
        $data['type'] = 'comment';
        $data['remark_id'] = I('wedding_id');
        if(empty($data['parent_id'])){
            $this->error('参数错误！');
        }
        if($model->create($data)){
            $id = $model->add();
            if($id){
                $this->success('评论成功！');
            }else{
                $this->error('评论失败！');
            }
        }else{
            $this->error($model->getError());
        }
    }

    //婚礼头条回复接口（对评论或回复进行回复）
    public function reply(){
        $model = D('SchoolWeddingComment');
        $parent_id = I('parent_id');
        if(empty($parent_id)){
            $this->error('参数错误！');
        }
        $data = $model->where("id=$parent_id")->field('id as parent_id,username,headimg,content,create_time,remark_id as wedding_id')->find();
        if(empty($data)){
            $this->error('该条记录不存在！');
        }
        $this->success('success',$data);
    }

    //婚礼头条回复提交接口
    public function replyPost(){
        $model = D('SchoolWeddingComment');
        $data['parent_id'] = I('parent_id');
        $data['uid'] = $this->user['uid'];
        $data['username'] = $this->user['username'];
        $data['headimg'] = $this->user['avatar'];
        $data['type'] = 'reply';
        $data['remark_id'] = I('wedding_id');
        $data['content'] = I('content');
        if(empty($data['parent_id'])){
            $this->error('参数错误！');
        }
        if($model->create($data)){
            $id = $model->add();
            if($id){
                $this->success('回复成功！');
            }else{
                $this->error('回复失败！');
            }
        }else{
            $this->error($model->getError());
        }

    }

    //用户举报
    public function reportUser(){
        $data['comment_id'] = I('comment_id');
        $data['uid_be'] = I('uid_be');
        $data['uid_do'] = $this->user['uid'];
        $data['type_report'] = I('type');
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['status'] = 1;
        if(empty($data['uid_be']) || empty($data['type_report']) ||empty($data['comment_id'])){
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
        if(!empty($report)){
            $this->error('您已经对该条内容进行过该种类型的举报！');
        }
        if(empty($comment)){
            $this->error('该条记录不存在！');
        }
        $id = M('SchoolWeddingReport')->add($data);
        if($id){
            $count = $modelReport->where(array('comment_id'=>$data['comment_id']))->count('id');
            if($count>10){
                $comment['status'] =0;
                $result = $modelComment->save($comment);
                if($result!==false){
                    $this->success('举报成功！');
                }else{
                    $this->error('举报失败！');
                }
            }else{
                $this->success('您的举报已经提交！');
            }

        } else{
            $this->error('举报失败！');
        }
    }


    //用户点赞
    public function praise(){
        $data['comment_id'] = I('comment_id');
        $data['uid'] = $this->user['uid'];
        $data['username'] = $this->user['username'];
        $data['headimg'] = $this->user['avatar'];
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['status'] =1;
        $comment = D('SchoolWeddingComment')->where(array('id'=>$data['comment_id']))->find();
        if(empty($comment)){
            $this->error('参数错误！');
        }
        $model = M('SchoolWeddingPraise');
        $where['uid'] = $data['uid'];
        $where['comment_id'] = $data['comment_id'];
        //$where['status'] =1;
        $praise = $model->where($where)->find();
        if(!empty($praise)){
            if($praise[status]==1){
                $this->error('您已经对该条内容点赞过了！');
            }else{
                $praise['status']=1;
                $praise['update_time'] =time();
                $result = $model->save($praise);
                if($result!==false){
                    $this->success('点赞成功！');
                }else{
                    $this->error('点赞失败！');
                }
            }
        }
        $id = $model->add($data);
        if($id){
            $this->success('点赞成功！');
        }else{
            $this->error('点赞失败！');
        }
    }

    //取消点赞
    public function cancelPraise(){
        $comment_id = I('comment_id');
        $uid = $this->user['uid'];
        if(empty($comment_id)){
            $this->error('参数错误！');
        }
        $model = M('SchoolWeddingPraise');
        $where['uid'] = $uid;
        $where['comment_id'] = $comment_id;
        $where['status'] = 1;
        $praise = $model->where($where)->find();
        if(empty($praise)){
            $this->error('您不曾对该内容点赞过！');
        }
        $praise['update_time'] = time();
        $praise['status'] =0;
        $result = $model->save($praise);
        if($result!==false){
            $this->success('取消点赞成功！');
        }else{
            $this->error('取消点赞失败！');
        }
    }


    //头条收藏
    public function favorite(){
        $model = M('SchoolWeddingFavorites');
        $data['wedding_id'] = I('wedding_id');
        $data['uid'] = $this->user['uid'];
        $data['username'] = $this->user['username'];
        $data['headimg'] = $this->user['avatar'];
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['status'] = 1;
        if(empty($data['wedding_id'])){
             $this->error('参数错误！');
         }
        $where['uid'] =$data['uid'];
        $where['wedding_id'] = $data['wedding_id'];
        $favorite = $model->where($where)->find();
        if(!empty($favorite)){
            if($favorite['status']==1){
                $this->error('您已经收藏过该头条，不能重复收藏！');
            }else{
                $favorite['status']=1;
                $favorite['update_time'] =time();
                $result = $model->save($favorite);
                if($result!==false){
                    $this->success('收藏成功！');
                }else{
                    $this->error('收藏失败！');
                }
            }
        }
        $id = $model->add($data);
        if($id){
            $this->success('收藏成功！');
        }else{
            $this->error('收藏失败！');
        }
    }

    //取消收藏
    public function cancelFavorite(){
        $model = M('SchoolWeddingFavorites');
        $wedding_id = I('wedding_id');
        $uid = $this->user['uid'];
        if(empty($wedding_id)){
            $this->error('参数错误！');
        }
        $where['uid'] =$uid;
        $where['wedding_id'] = $wedding_id;
        $where['status'] = 1;
        $favorite = $model->where($where)->find();
        if(empty($favorite)){
            $this->error('您不曾收藏过该内容！');
        }
        $favorite['update_time'] = time();
        $favorite['status'] = 0;
        $result = $model->save($favorite);
        if($result!==false){
            $this->success('取消收藏成功！');
        }else{
            $this->error('取消收藏失败！');
        }

    }

    //评论详情
    public function commentDetail(){
        $comment_id = I('comment_id');
        if(empty($comment_id)){
            $this->error('参数错误！');
        }
        $comentDetail = M('SchoolWeddingComment')->where("id=$comment_id")->field('id as comment_id,content,create_time,username,headimg')->find();
        if(empty($comentDetail)){
            $this->success('暂时没有内容！',(object)$comentDetail);
        }
        $data = $comentDetail;
        $this->success('success',$data);
    }

    //点赞详情
        public function praiseDetail(){
        $comment_id = I('comment_id');
        if(empty($comment_id)){
            $this->error('参数错误！');
        }
        $where['comment_id'] = $comment_id;
        $where['status'] = 1;
        $praiseDetail = M('SchoolWeddingPraise')->where($where)->field('uid,username,headimg')->select();
        if(empty($praiseDetail)){
            $data['praiseDetail'] = array();
            $this->success('暂时没有内容！',$data);
        }
        $data['praiseDetail'] = $praiseDetail;
        $this->success('success',$data);
    }

    //个人主页
    public function personalPage(){
        $uid = I('uid');
        if(empty($uid)){
            $this->error('参数错误！');
        }

    }

    //我的——评论
    public function myComments(){
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $uid = $this->user['uid'];
        $model_comment_reply = M('SchoolWeddingComment');
        $whereComment['status']=1;
        $whereComment['uid']=$uid;
        $comment = $model_comment_reply->where($whereComment)->page($page,$per_page)->order('create_time desc')->select();
        //获取点赞状态
        $wherePraise = array();
        foreach ($comment as $key=>$value){
            $id_arr[] = $value['id'];
        }
        if(!empty($id_arr)){
            $wherePraise['comment_id'] = array('in',$id_arr);
        }

        $wherePraise['uid'] = $uid;
        $status_praise_arr = M('SchoolWeddingPraise')->where($wherePraise)->field('comment_id,status')->select();
        unset($wherePraise['uid']);
        $wherePraise['status'] = 1;
        //获取点赞数
        $praise_count = M('SchoolWeddingPraise')->where($wherePraise)->group('comment_id')->field('comment_id,count(id ) as count_praise')->select();
        //点赞状态绑定
            foreach ($comment as $key=>$value){
                $comment[$key]['status_praise'] = 0;
                foreach ($status_praise_arr as $key_praise=>$value_praise){
                    if($value['id']==$value_praise['comment_id']){
                        $comment[$key]['status_praise'] = intval($value_praise['status']);
                    }
                }
            }
        //点赞数绑定
        if(!empty($praise_count)){
            foreach ($comment as $key=>$value){
                $comment[$key]['count_praise'] = 0;
                foreach ($praise_count as $key_praise_count=>$value_praise_count){
                    if($value['id']==$value_praise_count['comment_id']){
                        $comment[$key]['count_praise'] = intval($value_praise_count['count_praise']);
                    }
                }
            }
        }else{
            foreach ($comment as $key=>$value) {
                $comment[$key]['count_praise'] = 0;
            }
        }
        foreach ($comment as $key=>$value){
            if($value['type']=='reply'){
                $parent_id[] =$value['parent_id'];
            }
            if($value['type']=='comment'){
                $parent_id_wedding[] =$value['parent_id'];
            }
        }
        if(!empty($parent_id)){
            $whereReply['id'] = array('in',$parent_id);
            $whereReply['status'] =1;
            $reply = $model_comment_reply->where($whereReply)->order('create_time desc')->select();
            //回复和父节点回复绑定
            foreach ($comment as $key_comment=>$value_comment){
                $comment[$key_comment]['parent_reply'] = array();
                foreach ($reply as $key_reply=>$value_reply){
                    if($value_comment['parent_id']==$value_reply['id'] && $value_comment['type']=='reply'){
                        $comment[$key_comment]['parent_reply'][]=$reply[$key_reply];
                    }
                }
            }
        }else{
            foreach ($comment as $key_comment=>$value_comment){
                $comment[$key_comment]['parent_reply'] = array();
            }
        }
        if(!empty($parent_id_wedding)){
            $whereWedding['id'] = array('in',$parent_id_wedding);
            $whereWedding['status'] = 1;
            $wedding = M('SchoolWedding')->where($whereWedding)->field('id,headline,brief,create_time')->select();
            //头条和封面绑定
            $imgs_url = $this->get_imgs($parent_id_wedding,'cover');
            foreach ($wedding as $key_wedding=>$value_wedding){
                $wedding[$key_wedding]['imgs'] = array();
                foreach ($imgs_url as $key_img=>$value_img){
                    if($value_wedding['id']==$value_img['record_id']){
                        $wedding[$key_wedding]['imgs'][]=$imgs_url[$key_img];
                    }
                }
            }
            //comment和wedding绑定
            foreach ($comment as $key_comment=>$value_comment){
                $comment[$key_comment]['parent_wedding'] =array();
                foreach ($wedding as $key_wedding=>$value_wedding){
                    if($value_comment[parent_id]==$value_wedding['id'] && $value_comment['type']=='comment'){
                        $comment[$key_comment]['parent_wedding'][] =$wedding[$key_wedding];
                    }
                }
            }
        }else{
            foreach ($comment as $key_comment=>$value_comment){
                $comment[$key_comment]['parent_wedding'] = array();
            }
        }
        $data['comment'] = array_values($comment);
        $this->success('success',$data);
    }

     //我的--删除评论
    public function myCommentDelete(){
        $comment_id = I('comment_id');
        $where['id'] = $comment_id;
        $model =M('SchoolWeddingComment');
        $comment = $model->where($where)->find();
        if(empty($comment_id)){
            $this->error('参数错误！');
        }
        if(empty($comment)){
            $this->error('不存在该条记录！');
        }
        if($comment['status']==0){
            $this->success('该条评论已经被删除！');
        }
        $comment['status']=0;
        $comment['update_time']=time();
        $result = $model->save($comment);
        if($result!==false){
            $this->success('删除评论成功！');
        }else{
            $this->error('删除评论失败！');
        }
    }

     //我的--收藏
    public function myFavorites(){
        $page = I('page') ? I('page') : 1;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $uid = $this->user['uid'];
        $where['wtw_school_wedding_favorites.uid'] =$uid;
        $where['wtw_school_wedding_favorites.status'] =1;
        $where['a.status'] =1;
        $list = M('SchoolWeddingFavorites')->join('left join wtw_school_wedding as a on wtw_school_wedding_favorites.wedding_id=a.id')->where($where)
                    ->field('a.id,a.headline,a.brief,a.create_time')->page($page,$per_page)->select();
        $total = M('SchoolWeddingFavorites')->join('left join wtw_school_wedding as a on wtw_school_wedding_favorites.wedding_id=a.id')->where($where)
            ->field('a.id,a.headline,a.brief,a.create_time')->count();
        if(empty($list)){
            $this->success('内容为空！',(object)$list);
        }
        //获取头条cover
        foreach ($list as $key=>$value){
            $wedding_id[] = $value['id'];
        }
        if(!empty($wedding_id)){
            $imgs_url = $this->get_imgs($wedding_id,'cover');
            foreach ($list as $key_list=>$value_list){
                $list[$key_list]['imgs'] = array();
                foreach ($imgs_url as $key_img=>$value_img){
                    if($value_list['id']==$value_img['record_id']){
                        $list[$key_list]['imgs'][]=$imgs_url[$key_img];
                    }
                }
            }
        }
        $data['list'] = array_values($list);
        $data['total'] = intval($total);
        $this->success('success',$data);


    }

     //我的——删除收藏
    public function myFavoritesDelete(){
        $wedding_id = I('wedding_id');
        $uid = $this->user['uid'];
        $where['uid'] = $uid;
        $where['wedding_id'] = $wedding_id;
        $model = M('SchoolWeddingFavorites');
        $favorite = $model->where($where)->find();
        if(empty($wedding_id)){
            $this->error('参数错误！');
        }
        if(empty($favorite)){
            $this->error('不存在该条收藏记录！');
        }
        if($favorite['status']==0){
            $this->success('您已经取消了对该头条的收藏！');
        }
        $favorite['status'] =0;
        $favorite['update_time'] =time();
        $result = $model->save($favorite);
        if($result!==false){
            $this->success('取消收藏成功！');
        }else{
            $this->error('取消收藏失败！');
        }
    }


    //获取头条图片
    public function get_imgs($wedding_id=array(),$remark=''){
        if($remark=='cover'){
            $where['module'] = "SchoolWeddingCover";
        }elseif ($remark=='detail'){
            $where['module'] = "SchoolWedding";
        }
        $base_url = 'http://7xopel.com2.z0.glb.clouddn.com/';
        $where['status']=1;
        $where['record_id'] = array('in',$wedding_id);
        $imgs_url = M('Attach')->where($where)->field('record_id,id as attach_id,savename as url')->select();
        foreach ($imgs_url as $key=>$value){
            $imgs_url[$key]['url']=$base_url.$value['url'];
        }
        return $imgs_url;
    }







}