<?php
/**
 * common
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Controller;
class ApiBaseController extends Controller {
	/**
	 * 初始化操作
	 */
	public function _initialize(){
        $allow_origin = array(  
            'http://www.hx.com',
            'http://college.hx.com',
            'http://www.halobear.com',
            'http://halobear.com',
            'http://open.halobear.com',
            'http://college.halobear.com'
        );  
        $origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : '';  
        if(in_array($origin, $allow_origin)){  
            header('Access-Control-Allow-Origin:'.$origin);  
            header('Access-Control-Allow-Methods:POST,GET');  
            header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization');  
            header("Access-Control-Allow-Credentials: true");
        }

        // 兼容jsonp
        $_GET['callback'] && $_POST = $_GET;

	}

    public function _empty(){
        $this->ajaxReturn(array('info'=> ACTION_NAME . ' Not Found', 'iRet'=>0));
	}

    /**
     * 加载配置
     */
    protected function _loadConfig() {
        $data = D('Config')->select();
        $result = array();
        foreach ($data as $value) {
            $result[$value['name']] = json_decode($value['value']) ? json_decode($value['value'],true) : $value['value'];
        }
        C($result);
    }

    protected function _auth(){
        $user = get_user();
        if (!empty($user)) {
            $model = D('SchoolAccount');
            $model->id = $user['id'];
            $model->username = $user['username'];
            $model->phone = $user['phone'];
        }else{
            $type = !empty($_GET['callback']) ? 'jsonp' : 'json';
            $this->ajaxReturn(array('iRet'=>-1, 'info'=>'Access denied'));
        }
    }

    protected function error($info = 'error',$error=''){
        $type = !empty($_GET['callback']) ? 'jsonp' : 'json';
        $this->ajaxReturn(array('iRet'=>0, 'info'=>$info, 'error'=>$error), $type);
    }

    protected function success($info = 'success',$data = array()){
        $type = !empty($_GET['callback']) ? 'jsonp' : 'json';
        $this->ajaxReturn(array('iRet'=>1, 'info'=>$info, 'data'=>$data), $type);
    }
}