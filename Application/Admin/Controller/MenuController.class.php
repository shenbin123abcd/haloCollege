<?php
namespace Admin\Controller;

class MenuController extends CommonController {
	/**
	 * 列表页
	 * @see CommonAction::index()
	 */
	public function index(){
		$this->assign('list',$this->_menu());
		$this->display();
	}
	
	/**
	 * 新增操作
	 * @see CommonAction::insert()
	 */
	public function insert(){
		$model = D('Menu');
		
		$model->startTrans();
		// 更新操作
		foreach($_POST['data'] AS $value){
			if($model->create($value)){
				$value['status'] = empty($value['status']) ? 0 : 1;
				$model->save($value);
			}else{
				$model->rollback();
				$this->error($model->getError());
			}
		}
		
		// 新增数据
		foreach ($_POST['newdata'] AS $key=>$value){
			// 父级
			if(strpos($key, 'root_') !== false){
				$temp['temp_'.$key] = $value['id'];
				$value['parent'] = 'root';
			}
			// 子级
			if(strpos($key, 'child_') !== false){
				if(is_numeric($value['parentid'])){
					$value['parent'] = $model->where(array('mid'=>$value['parentid']))->getField('id');
				}else{
					$value['parent'] = $temp[$value['parentid']];
				}
			}
			if($model->create($value)){
				$model->add($value);
			}else{
				$model->rollback();
				$this->error($model->getError());
			}
		}
		$model->commit();
		
		$this->success('操作成功！');
	}
	
	public function _before_edit(){
		$this->menu = D('Menu')->getCacheResult(array('parent'=>'root'));
	}
	
	/**
	 * 导航菜单
	 * @return array
	 */
	private function _menu(){
		$menu_list = M('Menu')->order('sort asc')->getField('id,name,parent,url,sort,status,mid');
		$menu = array();
		foreach ($menu_list AS $value){
			if($value['parent'] == 'root'){
				$menu[$value['id']] = $value;
			}
		}
		foreach ($menu AS &$value){
			foreach ($menu_list AS $l_value){
				if($value['id'] == $l_value['parent']){
					$value['items'][$l_value['id']] = $l_value;
					continue;
				}
			}
		}
		unset($value);
		return $menu;
	}
}