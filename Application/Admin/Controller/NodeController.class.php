<?php
namespace Admin\Controller;

class NodeController extends CommonController {
	/**
	 * 列表页
	 * @see CommonAction::index()
	 */
	public function index(){
		$list = $this->model()->order('id asc')->select();
		foreach ($list AS $value){
			if($value['pid'] == 0){
				$new_list[] = $value;
			}
		}
		foreach ($new_list AS &$value){
			foreach ($list AS $l_value){
				if ($l_value['pid'] == $value['id']) {
					$value['items'][] = $l_value;
					continue;
				}
			}
		}
		
		$this->assign('list',$new_list);
		$this->display();
	}
	
	/**
	 * 新增操作
	 * @see CommonAction::insert()
	 */
	public function insert(){
		$model = $this->model();

		$model->startTrans();
		// 更新操作
		foreach($_POST['data'] AS $value){
			$this->model()->setProperty('_data', $value);
			if($model->create($value)){
				$value['status'] = empty($value['status']) ? 0 : 1;
				$value['name'] = $value['pid'] == 0 ? ucfirst($value['name']) : strtolower($value['name']);
				$model->save($value);
			}else{
				$model->rollback();
				$this->error($model->getError());
			}
		}
		
		// 新增数据
		$temp = array();
		foreach ($_POST['newdata'] AS $key=>$value){
			// 父级
			if(strpos($key, 'root_') !== false){
				$value['pid'] = 0;
				$value['name'] = ucfirst($value['name']);
			}
			
			// 子级
			if(strpos($key, 'child_') !== false){
				if(!is_numeric($value['pid'])){
					$value['pid'] = $temp[$value['pid']];
				}
				$value['name'] = strtolower($value['name']);
			}
			
			$this->model()->setProperty('_data', $value);
			if($model->create($value)){
				$temp['temp_'.$key] = $model->add($value);
			}else{
				$model->rollback();
				$this->error($model->getError());
			}
		}
		$model->commit();
		
		$this->success('操作成功！');
	}
	
}