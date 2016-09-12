<?php
/**
 * 公开的操作
 * @author wtwei
 * @version $Id$
 */
namespace Api\Controller;

use Think\Controller;

class PublicController extends CommonController {

    /**
     * 七牛上传回调 --非编辑器
     */
    public function qiniuUploadCallback() {
        $data['key'] = $_POST['filetype'];
        $data['name'] = $_POST['fname'];
        $data['size'] = $_POST['fsize'];
        $data['module'] = $_POST['module'];
        $data['savename'] = $_POST['key'];
        $data['create_time'] = time();
        $data['width'] = $_POST['w'];
        $data['height'] = $_POST['h'];
        $data['type'] = '';
        $data['status'] = 1;
        $data['record_id'] = $data['user_id'] = 0;
        $id = M('Attach')->add($data);
        $this->ajaxReturn(array('id' => $id, 'w' => $_POST['w'], 'h' => $_POST['h'], 'key' => $_POST['key'], 'fsize' => $_POST['fsize']));
    }

    /**
     * 获取用户信息（视视评论列表用到）
    */
    public function getUserInfo() {
        $uid = $_POST['uid'];
        if (empty($uid)) {
            $this->error('参数错误！');
        }
        $uid_arr = json_decode($uid);
        $where['uid'] = array('in', $uid_arr);
        $where['status'] = 1;
        $userInfo = M('Userinfo')->where($where)->select();
        if (empty($userInfo)) {
            $this->error('用户信息不存在！');
        }
        $data['userInfo'] = $userInfo;
        $this->success('success', $data);
    }






}