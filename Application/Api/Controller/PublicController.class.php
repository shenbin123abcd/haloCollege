<?php
/**
 * 公开的操作
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Controller;

use Think\Controller;

class PublicController extends Controller {

    /**
     * 七牛上传回调 --非编辑器
     */
    public function qiniuUploadCallback(){
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

        $this->ajaxReturn(array('id'=>$id,'w'=>$_POST['w'],'h'=>$_POST['h'],'key'=>$_POST['key'],'fsize'=>$_POST['fsize']));
    }


}