<?php
namespace Admin\Controller;

class ModuleFieldController extends CommonController {
	/**
	 * 列表前置操作
	 */
	public function _before_index(){
		$this->moduleName = M('Module')->where(array('id'=>$this->_get('mid')))->getField('title');
	}
	
	/**
	 * 更新前置操作
	 */
	public function _before_edit(){
		$this->field_type = M('ModuleFieldType')->select();
	}
	
	/**
	 * 新增前置操作
	 */
	public function _before_add(){
		$this->_before_edit();
	}
	
	/**
	 * 删除操作
	 * @see CommonAction::delete()
	 */
	public function delete(){
		$model = $this->model ();
		$id = $_REQUEST ['id'];
		$options = array ('where' => array ('id' => array ('in',$id ) ) );
		
		// 删除的数据表
		$data = $model->field('name,mid')->select($options);
		$tablename = M('Module')->where(array('id'=>$data[0]['mid']))->getField('tablename');
		$sql .= 'ALTER TABLE  `'.C('DB_PREFIX').$tablename.'` ';
		foreach ($data AS $value){
			$sql .= 'DROP  `'.$value['name'].'` ,';
		}
		$sql = trim($sql,',');
		
		if($model->delete ( $options )){
			// 删除字段
			M()->query($sql);
			$this->success ( '删除数据成功！' );
		}else {
			$this->error ( '删除数据失败！' );
		}
	}
}