<?php
namespace Api\Controller;

use Think\Controller;

class IndexController extends CommonController {
    protected $module_auth = 0;
    protected $action_auth = array();

    public function index() {
        $this->_auth();
        dump($this->user);
    }

}