<?php
namespace Ke\Controller;
use Think\Controller;
class EmptyController extends Controller{
    public function index(){
		$this->_empty();
	}

    public function _empty(){
        if(IS_AJAX){
            $this->ajaxReturn(array('info' => ACTION_NAME . ' Not Found', 'error' => 404, 'iRet' => 0));
        }else{
            $this->display('Index:index');
        }
	}
}