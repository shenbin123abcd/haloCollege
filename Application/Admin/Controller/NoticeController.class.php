<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/12
 * Time: 17:46
 */

namespace Admin\Controller;

use Pingpp\Error\Api;
use Think\Controller;

class NoticeController extends Controller{
    /**
     * 系统消息推送
    */
    public function notice(){
        $data = $_POST;
        $extra = array();
        if (!empty($data['content'])){
            foreach ($data as $key=>$value){
                if (preg_match('/_extra/',$key)){
                    $name = explode('_',$key);
                    $extra[$name[0]] = $value;
                }
            }
            $msg_no = date("d") . rand(10,99) . implode(explode('.', microtime(1)));
            $extra['msg_type'] = $data['msg_type'];
            $extra['push_time'] = time();
            $extra['msg_no'] = $msg_no;
            $extra['redirect_url'] = '';
            $object_push = A('Push');
            $result = $object_push->pushMsgAlert(array('content'=>$data['content'],'extra'=>$extra,'object'=>$data['object']));
            //存储系统消息
            $users = M('UserLogin')->where(array('is_login'=>1,'token_exp'=>array('gt',time())))->field('uid')->select();
            foreach ($users as $key=>$value){
                $msg['from_uid'] = 0;
                $msg['from_username'] = '';
                $msg['to_uid'] = $value['uid'];
                $msg['content'] = $data['content'];
                $msg['detail_id'] = 0;
                $msg['msg_type'] = $data['msg_type'];
                $msg['push_time'] = time();
                $msg['extra'] = '';
                $msg['is_read'] = 0 ;
                $msg['remark_type'] = 1;
                $msg['msg_no'] = $msg_no;
                $msg['redirect_url'] = '';
                $push_msg = M('PushMsg')->add($msg);
            }
        }
        
    }

    /**
     * 通知首页
    */
    public function index(){
        $alert = M('PushCate')->where(array('type'=>0))->getField('push_id,title');
        $total = M('PushMsg')->where(array('remark_type'=>1))->count();
        import("ORG.Util.Page");
        $listRows =  50;
        $page       = new \Think\Page($total,$listRows,$_GET);
        //添加了3.1的$page->limit()
        $limit=$page->limit();
        $data = M('PushMsg')->where(array('remark_type'=>1))->order('id DESC')->limit($limit)->select();
        foreach ($data as $key=>$value){
            $data[$key]['type'] = $alert[$value['msg_type']];
            $data[$key]['push_time'] = date('Y-m-d H:i:s',$value['push_time']);
        }
        $this->assign('list',$data);
        $this->assign('page', $page->show());
        $this->display();
    }

    public function add(){
        $alert = M('PushCate')->where(array('type'=>0))->select();
        $this->assign('alert_cate',$alert);
        $this->display();
    }

    //获取上传TOKEN
    public function getToken(){
        $token = make_qiniu_token('crmpub',CONTROLLER_NAME,'http://college-koala.halobear.com/public/qiniuUpload');
        $this->ajaxReturn($token,'JSON');
    }

}