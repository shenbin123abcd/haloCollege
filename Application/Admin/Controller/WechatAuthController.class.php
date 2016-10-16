<?php

namespace Admin\Controller;

class WechatAuthController extends CommonController {

    public function filter(&$where = array())
    {
        $_GET['nickname'] && $where['nickname'] = array('like', '%' . $_GET['nickname'] . '%');
    }
}