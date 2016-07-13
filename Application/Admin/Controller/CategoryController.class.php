<?php

namespace Admin\Controller;

class CategoryController extends CommonController {
	/**
	 * 列表
	 */
	public function index(){
		$cate = $this->model()->select();

		$tree = to_tree($cate);

		foreach ($tree as $key => $value) {
			if($value['pid'] == 0){
				$list[] = $value;
			}
		}

		foreach ($list as $key => $value) {
			foreach ($tree as $t_key => $t_value) {
				if ($t_value['top_id'] == $value['id']) {
					$list[$key]['items'][] = $t_value;
				}
			}
		}
		cookie('__forward__', $_SERVER ['REQUEST_URI']);
		$this->assign('list',$list);
		$this->display();
	}

	/**
	 * 搜索
	 */
	public function search(){
		$keyword = $this->_post('keyword');
		$where = array('title' => array('like','%'. $keyword .'%') );
		$list = $this->model()->where($where)->field('id,title')->select();
		$data['data'] = $list;
		$data['message'][0] = empty($list) ? 'FORUM:searchforum.notfound' : 'FORUM:searchforum.success';
		$data['refresh'] = false;
		$data['state']  = empty($list) ? 'fail' : 'success';
		$this->ajaxReturn($data);
	}

	/**
	 * 新增
	 */
	public function insert(){
		$model = D('Category');
		
		$model->startTrans();

		// 更新操作
		foreach ($_POST['name'] as $key => $value) {
			$update_data = array('id'=>$key,'name'=>$value,'sort'=>$_POST['sort'][$key]);
			if($model->create($update_data)){
				$model->save($update_data);
			}else{
				$model->rollback();
				$this->error($model->getError());
			}
		}
		
		// 新增数据
		foreach ($_POST['new_catetitle'] AS $key=>$value){
			foreach ($value as $sub_key => $sub_value) {
				if ($key == 0) {
					$data['pid'] = $data['top_id'] = $data['level'] = 0;
				}else{
					$data['pid'] = $key;
					$data['top_id'] = $this->_getTopId($data['pid']);;
					$data['level'] = $_POST['templevel'][$key][$sub_key]-1;
				}
				$data['order'] = $_POST['new_cateorder'][$key][$sub_key];
				$data['title'] = $sub_value;
				$data['name'] = $_POST['new_catename'][$key][$sub_key];
				
				if($model->create($data)){
					$pid[$_POST['tempid'][$key][$sub_key]] = $model->add($data);
				}else{
					$model->rollback();
					$this->error($model->getError());
				}
			}
			
		}
		
		foreach ($_POST['temp_catetitle'] as $key => $value) {
			foreach ($value as $sub_key => $sub_value) {
				$data['order'] = $_POST['temp_cateorder'][$key][$sub_key];
				$data['title'] = $sub_value;
				$data['name'] = $_POST['temp_catename'][$key][$sub_key];
				$data['pid'] = $pid[$key];
				$data['top_id'] = $this->_getTopId($data['pid']);
				$data['level'] = $_POST['templevel'][$key][$sub_key] - 1;

				if($model->create($data)){
					$pid[$_POST['tempid'][$key][$sub_key]] = $model->add($data);
				}else{
					$model->rollback();
					$this->error($model->getError());
				}
				//$top_id = $this->_getTopId($data['pid']);
				//$model->where(array('id'=>$temp_id))->save(array('top_id'=>$top_id));
			}
			
		}
		$model->commit();
		
		$this->success('操作成功！');
	}

	/**
	 * 前置操作
	 */
	public function _before_edit(){
		$this->model = M('Module')->where(array('status'=>1))->field('id,title')->select();
	}

	/**
	 * 获取祖先节点ID
	 * @param  int $pid 父级ID
	 * @return int      ID
	 */
	private function _getTopId($pid){
		static $cid = 0;
		$cate = $this->model()->where(array('id'=>$pid))->field('id,pid')->find();
		if ($cate[pid] != 0) {
			$this->_getTopId($cate['pid']);
		}else{
			$cid = $cate['id'];
		}
		return $cid;
	}

}