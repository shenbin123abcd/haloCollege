<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
class CommonController extends Controller {
	/**
	 * 初始化操作
	 * 权限验证等
	 */
	public function _initialize(){

		// 用户信息
		$this->user = member_info();

		// 检测登录
		$this->_auth($this->user);
		
		// 加载配置
        $this->_loadConfig();
        // 表单数据处理
        // $this->_inputData();
        // 获取模型名称
        //MODULE->CONTROLLER
    	$this->_module_name_ = CONTROLLER_NAME;
		

	}

	/**
	 * RBAC权限控制
	 * @param  array $user 用户信息
	 */
	private function _auth($user){
		// 用户未登录
		!$user && $this->redirect(C('USER_AUTH_GATEWAY'));

		// 权限检查
        $model = D('Rbac');
        $model->userAuthKey = $user['id'];
        $model->userAuthType = C('USER_AUTH_TYPE');
        $model->notAuthModule = C('NOT_AUTH_MODULE');
        $model->adminAuthKey = D('Founder')->getFounder();
        $model->saveAccessName = C('SAVE_ACCESS_NAME');
        if (!$model->accessDecision(MODULE_NAME, CONTROLLER_NAME, ACTION_NAME)) {
        	if(!empty($_GET['dialog'])){
        		$this->display('Public:noauth');
        		exit;
        	}else{
        		$this->error('没有操作权限！',U('Index/main'));
        	}
        }
	}
	
	/**
	 * 默认列表
	 * @see CommonAction::index()
	 */
	public function index(){
		$this->_list($this->model());
        cookie('__forward__', $_SERVER ['REQUEST_URI']);
        $this->display($_GET['display'] ? $_GET['display'] : 'index');
	}
	
	/**
	 * 列表数据
     *
	 * @param object $model 实例化模型
	 * @param array $where 查询条件
	 */
	protected function _list($model,$where=array()) {
         
		$this->_search($model,$where);
		
		$this->filter($where);
		$totalRows = $model->where($where)->count($model->getPk());

		if($totalRows > 0){
			$field = empty($_GET['_field']) ? $model->getPk() : $_GET['_field'];
			$order = empty($_GET['_order']) ? ' desc' : " {$_GET['_order']}";
			import("ORG.Util.Page");
			$listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 50;
			$page       = new \Think\Page($totalRows,$listRows,$_GET);
			//添加了3.1的$page->limit()
			$options=array('where'=>$where,'order'=>"`{$field}` {$order}",'limit'=>$page->limit());
			//$options=array('where'=>$where,'order'=>"`{$field}` {$order}");
			$data = $model->select($options);
			method_exists($this, '_join') && $this->_join($data);
			$this->assign('list', $data);
			$this->assign('page', $page->show());

		}
	}
	
	/**
	 * 查询过滤
	 */
	protected function filter(&$map = array()){

	}
	
	/**
	 * 根据表单生成查询条件和过滤
	 * @param array $map
	 */
	protected function _search($model,&$map = array()) {

		foreach ($model->getDbFields() as $key => $val) {
			if (isset($_REQUEST [$val]) && $_REQUEST [$val] != '' && empty($map [$val])) {
				$map [$val] = $val == 'title' ? array('like','%'. $_REQUEST [$val] .'%') : $_REQUEST [$val];
			}
		}

	}
	
	/**
	 * 默认新增操作
	 * @see CommonAction::add()
	 */
	public function add(){

		$this->display();
	}
	
	/**
	 * 默认插入操作
	 * @see CommonAction::insert()
	 */
	public function insert(){
		$model = $this->model();
		$model->setProperty('_data', $_POST);
		if ($model->create()) {
			$id = $model->add();
            if (!empty ($id)){
                ! empty ($_REQUEST ['attach_id']) && D ( 'Attach' )->where ( array ('id' => array ('in',trim ( $_REQUEST ['attach_id'], ',,' ) ) ) )->save ( array ('record_id' => $id ) );//,'module' => CONTROLLER_NAME
                ! empty ($_REQUEST ['attach_editor_id']) && D ( 'Attach' )->where ( array ('id' => array ('in',trim ( $_REQUEST ['attach_editor_id'], ',' ) ) ) )->save ( array ('record_id' => $id,'module' => CONTROLLER_NAME ) );
            }
            $id ? $this->success ( '新增数据成功！', cookie ( '__forward__' ) ) : $this->error ( '新增数据失败！' );
		} else {
			$this->error ( $model->getError () );
		}
	}
	
	/**
	 * 默认编辑操作
	 * @see CommonAction::edit()
	 */
	public function edit(){		
		$model = $this->model();
		$pk = $model->getPk();
		$data = $model->where(array($pk=>$_GET[$pk]))->find();
		empty($data) && $this->error('查询数据失败！');
		$this->assign('data',$data);
		$this->display();
	}



	/**
	 * 默认更新操作
	 * @see CommonAction::update()
	 */
	public function update(){
		$model = $this->model();
		$model->setProperty('_data', $_POST);
		if ($model->create()) {
			$data = $model->data();
			$record = $model->save($data);
			if (!empty($_POST['attach_id'])) {
				$data = array('record_id' => $data['id']);//, 'module' => CONTROLLER_NAME
				D('Attach')->where(array('id' => array('in', trim($_POST['attach_id'], ',,'))))->save($data);
			}

			if (!empty($_POST['attach_editor_id'])) {
				$data = array('record_id' => $data['id'], 'module' => CONTROLLER_NAME);
				D('Attach')->where(array('id' => array('in', trim($_POST['attach_editor_id'], ','))))->save($data);
			}
			$record === false ? $this->error('更新数据失败！') : $this->success('更新数据成功！', cookie('__forward__'));
		} else {
			$this->error($model->getError());
		}
	}
	
	/**
	 * 默认删除操作
	 * @see CommonAction::delete()
	 */
	public function delete(){
		$model = $this->model ();
		$options = array ('where' => array ($model->getPk () => array ('in',$_REQUEST [$model->getPk ()] ) ) );
		$model->delete ( $options ) ? $this->success ( '删除数据成功！' ) : $this->error ( '删除数据失败！' );
	}
	
	/**
	 * 默认禁用
	 */
	public function forbid() {
		$data = array('status' => '0');
		$id = $_REQUEST[$this->model()->getPk ()];
		empty($id) && $this->error('请选择操作对象!');
		$options = array('where' => array($this->model()->getPk () => array('in', $id), 'status' => 1));
		$this->model()->save($data, $options) === false ? $this->error('状态禁用失败！') : $this->success('状态禁用成功！');
	}
	
	/**
	 * 默认恢复
	 */
	public function resume() {
		$data = array('status' => '1');
		$id = $_REQUEST[$this->model()->getPk ()];
		empty($id) && $this->error('请选择操作对象!');
		$options = array('where' => array($this->model()->getPk () => array('in', $id), 'status' => 0));
		$this->model()->save($data, $options) === false ? $this->error('状态恢复失败！') : $this->success('状态恢复成功！');
	}
	
	/**
	 * 默认排序
	 */
	public function sort(){
		$sort = $this->_post('sort');
		$model = $this->model();
		foreach ($sort AS $key=>$value){
			$model->where(array($model->getPk()=>$key))->setField('sort',$value);
		}
		$this->success('排序成功！');
	}
	
	/**
	 * 当前模型
	 * @param string $name 模型名
	 * @return Ambigous <Model, unknown>
	 */
	protected function model($name = '') {
		static $models = array();
		//MODULE->CONTROLLER
		$name = empty($name) ? CONTROLLER_NAME : $name;
		$model = empty($models[$name]) ? D($name) : $models[$name];
		$models[$name] = $model;

		return $model;
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

    /**
     * 表单数据处理
     */
    private function _inputData(){
    	// 例外
    	if(in_array(CONTROLLER_NAME,array('Menu','Category','Node','Role'))){
    		return ;
    	}
    	if(in_array(ACTION_NAME,array('sort'))){
    		return ;
    	}


    	foreach ($_REQUEST as $key => $value) {
    		is_array($value) && $_REQUEST[$key] = trim(implode(',', $value),',');
    	}
    	foreach ($_POST as $key => $value) {
    		is_array($value) && $_POST[$key] = trim(implode(',', $value),',');
    	}
    }
	
	/**
	 * 空操作
	 */
	public function _empty(){
		$this->error('页面不存在！');
	}
	
	//默认上传文件
    public function upload() {
        import('ORG.Net.Upload');
        $config=array(
            'savePath' => C('UPLOAD_PATH') . CONTROLLER_NAME . '/',
            'saveRule' => 'filename',
        );
        list($key,$file) = each($_FILES);
        if(is_array($this->uploadConfig[$key])){
            $config = array_merge($config,$this->uploadConfig[$key]);
        }
        $model = D('Attach');
        $result = $model->upload($file,$config);
        if($result['status']){
            $result['data']['key'] = $key;
            $result['data']['user_id'] = $this->user['id'];
            if($model->create($result['data'])){
                if($id = $model->add()){
                    $result['data']['id'] = $id;
                }else{
                    $result = array('status'=>0,'info'=>'保存附件记录失败！');
                }
            }else{
                $result = array('status'=>0,'info'=>$model->getError());
            }
        }
        
        $this->ajaxReturn($result);
    }

    //生成七牛token --非编辑器
    protected function qiniu($bucket,$dir = 'image',$callback = 'http://college-koala.halobear.com/public/qiniuUploadCallback'){
        $accessKey = C('QINIU_AK');
        $secretKey = C('QINIU_SK');

        $deadline = time()+1728000;
        $saveKey = $dir . '/$(etag)$(suffix)';
        $callbackBody = 'key=$(key)&w=$(imageInfo.width)&h=$(imageInfo.height)&fname=$(fname)&fsize=$(fsize)&filetype=${x:filetype}&video=${x:video}&module=' . $dir;

        $data =  array(
            'scope'=>$bucket,
            'deadline'=>$deadline,	
            'saveKey'=>$saveKey
        );

        if ($callback) {
        	$data['callbackUrl'] = $callback;
            $data['callbackBody'] = $callbackBody;
        }
        $data = json_encode($data);
        $find = array('+', '/');
        $replace = array('-', '_');
        $data = str_replace($find, $replace, base64_encode($data));
        $sign = hash_hmac('sha1', $data, $secretKey, true);
        $token = $accessKey . ':' . str_replace($find, $replace, base64_encode($sign)).':'.$data ;
        return $token;
    }

	//生成七牛token --非编辑器
	protected function qiniuBigCover($bucket,$dir = 'image',$callback = 'http://college-koala.halobear.com/public/qiniuUploadCallback'){
		$accessKey = C('QINIU_AK');
		$secretKey = C('QINIU_SK');

		$deadline = time()+1728000;
		$saveKey = $dir . '/$(etag)$(suffix)';
		$callbackBody = 'key=$(key)&w=$(imageInfo.width)&h=$(imageInfo.height)&fname=$(fname)&fsize=$(fsize)&filetype=${x:filetype}&video=${x:video}&module=' . $dir;

		$data =  array(
			'scope'=>$bucket,
			'deadline'=>$deadline,
			'saveKey'=>$saveKey
		);

		if ($callback) {
			$data['callbackUrl'] = $callback;
			$data['callbackBody'] = $callbackBody;
		}
		$data = json_encode($data);
		$find = array('+', '/');
		$replace = array('-', '_');
		$data = str_replace($find, $replace, base64_encode($data));
		$sign = hash_hmac('sha1', $data, $secretKey, true);
		$token = $accessKey . ':' . str_replace($find, $replace, base64_encode($sign)).':'.$data ;
		return $token;
	}

	//生成七牛token --banner图片上传
	protected function qiniuBanner($bucket,$dir = 'image',$callback = 'http://college-koala.halobear.com/public/qiniuUploadBanner'){
		$accessKey = C('QINIU_AK');
		$secretKey = C('QINIU_SK');

		$deadline = time()+1728000;
		$saveKey = $dir . '/$(etag)$(suffix)';
		$callbackBody = 'key=$(key)&w=$(imageInfo.width)&h=$(imageInfo.height)&fname=$(fname)&fsize=$(fsize)&filetype=${x:filetype}&video=${x:video}&module=' . $dir;

		$data =  array(
			'scope'=>$bucket,
			'deadline'=>$deadline,
			'saveKey'=>$saveKey
		);

		if ($callback) {
			$data['callbackUrl'] = $callback;
			$data['callbackBody'] = $callbackBody;
		}
		$data = json_encode($data);
		$find = array('+', '/');
		$replace = array('-', '_');
		$data = str_replace($find, $replace, base64_encode($data));
		$sign = hash_hmac('sha1', $data, $secretKey, true);
		$token = $accessKey . ':' . str_replace($find, $replace, base64_encode($sign)).':'.$data ;
		return $token;
	}

	//获取上传TOKEN
	public function getToken(){
		$token = make_qiniu_token('crmpub',CONTROLLER_NAME,'http://college-koala.halobear.com/public/qiniuUpload');
		$this->ajaxReturn($token,'JSON');
	}

}