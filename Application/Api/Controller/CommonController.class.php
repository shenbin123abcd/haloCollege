<?php
namespace Api\Controller;

use Think\Controller;

class CommonController extends Controller {
    protected $user = array();
    protected $module_auth = 0;
    protected $action_auth = array();

    /**
     * 初始化操作
     */
    public function _initialize() {
        $allow_origin = array('http://www.hx.com', 'http://college.hx.com', 'http://www.halobear.com', 'http://halobear.com', 'http://open.halobear.com', 'http://college.halobear.com');
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        if (in_array($origin, $allow_origin)) {
            header('Access-Control-Allow-Origin:' . $origin);
            header('Access-Control-Allow-Methods:POST,GET');
            header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization');
            header("Access-Control-Allow-Credentials: true");
        }
        // 兼容jsonp
        $_GET['callback'] && $_POST = $_GET;
        $this->_auth();
    }

    /**
     * 空操作
     */
    public function _empty() {
        if(IS_AJAX){
            $this->ajaxReturn(array('info' => ACTION_NAME . ' Not Found', 'error' => 404, 'iRet' => 0));
        }else{
            $this->display('Index:index');
        }
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
    protected function error($info = 'error', $error = '') {
        $type = !empty($_GET['callback']) ? 'jsonp' : 'json';
        $this->ajaxReturn(array('iRet' => 0, 'info' => $info, 'error' => $error), $type);
    }

    /**
     * 成功返回
     * @param string $info
     * @param array  $data
     */
    protected function success($info = 'success', $data = array()) {
        $type = !empty($_GET['callback']) ? 'jsonp' : 'json';
        $this->ajaxReturn(array('iRet' => 1, 'info' => $info, 'data' => $data), $type);
    }
}