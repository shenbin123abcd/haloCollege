<?php
namespace Home\Controller;
use Think\Controller;
class EmptyController extends Controller{
    public function index(){
		$this->_empty();
	}

    public function _empty(){
		send_http_status(404);
		$this->ajaxReturn(array('info'=>'Not Found','error'=>404,'status'=>0));
	}
}