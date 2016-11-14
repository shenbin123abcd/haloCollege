<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/11/11
 * Time: 15:00
 */

namespace Wechat\Controller;


use Think\Controller;

class CommonController extends Controller
{
    /**
     * 错误返回
     * @param string $info
     * @param string $error
     */
    protected function error($info = '网络繁忙请稍候再试', $error = '') {
        $type = !empty($_GET['callback']) ? 'jsonp' : 'json';
        $this->ajaxReturn(array('iRet' => 0, 'info' => $info, 'error' => $error), $type);
    }

    /**
     * 成功返回
     * @param string $info
     * @param array  $data
     */
    protected function success($data = array(), $info = '成功') {
        $type = !empty($_GET['callback']) ? 'jsonp' : 'json';
        $this->ajaxReturn(array('iRet' => 1, 'info' => $info, 'data' => $data), $type);
    }
}