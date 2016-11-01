<?php
namespace Share\Controller;
use Think\Controller;
class EmptyController extends Controller{
    public function index(){
		$this->_empty();
	}

    public function _empty(){
        if (IS_AJAX){
            $this->ajaxReturn(array('info'=>'Not Found','error'=>404,'status'=>0));
        }else{
            $this->display('Index:index');
        }
	}
}