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
    protected $action_auth = array('commentPost','replyPost','reply');

    //婚礼头条列表
    public function weddingList(){
        $page = I('page') ? I('page') : 0;
        $per_page = I('per_page') ? I('per_page') : 10000;
        $model = M('SchoolWedding');
        $where['status']=1;
        //分页
        if($page!=0){
            $list = $model->where($where)->order("create_time desc")->field("id,headline,brief,cover_url,from_unixtime(start_time,'%Y-%m-%d') as start_time")->page($page,$per_page)->select();
        }else{
            $list = $model->where($where)->order("create_time desc")->field("id,headline,brief,cover_url,from_unixtime(start_time,'%Y-%m-%d') as start_time")->select();
        }
        if(empty($list)){
            $this->success('内容为空！');
        }
        $total = $model->where($where)->count();
        $data['list'] = array_values($list);
        $data['total'] = intval($total);
        $this->success('success',$data);
    }

    //婚礼头条详情
    public function weddingDetail(){
        $wedding_id = I('wedding_id');
        if(empty($wedding_id)){
            $this->error('参数错误！');
        }
        $model_wedding = M('SchoolWedding');
        $model_comment = M('SchoolWeddingComment');
        $model_reply = M('SchoolWeddingReply');
        $whereWedding['status']=1;
        $whereWedding['id']=$wedding_id;
        $whereComment['status']=1;
        $whereComment['wedding_id']=$wedding_id;
        $detail = $model_wedding->where($whereWedding)->find();
        $comment = $model_comment->where($whereComment)->order('create_time desc')->select();
        foreach ($comment as $key=>$value){
            $comment_id[] =$value['id'];
        }
        $whereReply['comment_id'] = array('in',$comment_id);
        $whereReply['status'] =1;
        $reply = $model_reply->where($whereReply)->order('create_time desc')->field('id as reply_id,comment_id,content,username,create_time')->select();
        //评论和回复绑定
        foreach ($comment as $key_comment=>$value_comment){
            foreach ($reply as $key_reply=>$value_reply){
                if($value_comment['id']==$value_reply['comment_id']){
                    $comment[$key_comment]['reply'][]=$reply[$key_reply];
                }
            }
        }
        $data['detail'] = $detail;
        $data['comment'] = array_values($comment);
        $this->success('success',$data);
    }

    //婚礼头条评论提交接口
    public function commentPost(){
        $model = D('SchoolWeddingComment');
        $data['wedding_id'] = I('wedding_id');
        $data['uid'] = $this->user['uid'];
        $data['username'] = $this->user['username'];
        $data['headimg'] = $this->user['avatar'];
        $data['content'] = I('content');
        if(empty($data['wedding_id'])){
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

     //婚礼头条回复接口
    public function reply(){
        $model = D('SchoolWeddingComment');
        $comment_id = I('comment_id');
        if(empty($comment_id)){
            $this->error('参数错误！');
        }
        $data = $model->where("id=$comment_id")->field('id as comment_id,username,headimg,content,create_time')->find();
        if(empty($data)){
            $this->error('该条评论不存在！');
        }
        $this->success('success',$data);
    }


     //婚礼头条回复提交接口
    public function replyPost(){
        $model = M('SchoolWeddingReply');
        $data['uid'] = $this->user['uid'];
        $data['username'] = $this->user['username'];
        $data['headimg'] = $this->user['avatar'];
        $data['comment_id'] = I('comment_id');
        $data['content'] = I('content');
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['status'] = 1;
        if(empty($data['comment_id'])){
            $this->error('参数错误！');
        }
        if(empty($data['content'])){
            $this->error('请填写回复内容！');
        }
        $id = $model->add($data);
        if($id){
            $this->success('success');
        }else{
            $this->error($model->getError());
        }
    }



}