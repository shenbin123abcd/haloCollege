<?php
namespace Ke\Controller;

use Think\Controller;

class CommonController extends Controller {

    /**
     * 初始化操作
     */
    public function _initialize() {
        
    }

    /**
     * 空操作
     */
    public function _empty() {
        // send_http_status(404);
        $this->ajaxReturn(array('info' => ACTION_NAME . ' Not Found', 'error' => 404, 'iRet' => 0));
    }

    /**
     * 用户信息
     */
    protected function _auth() {
        $this->user = get_user();
        if (empty($this->user) && ($this->module_auth || in_array(ACTION_NAME, $this->action_auth))) {
            $type = !empty($_GET['callback']) ? 'jsonp' : 'json';
            $this->ajaxReturn(array('iRet' => -1, 'info' => 'Access denied'));
        }
    }

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