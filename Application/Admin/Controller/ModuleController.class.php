<?php
namespace Admin\Controller;

class ModuleController extends CommonController {
	/**
	 * 模型删除
	 * @see CommonAction::delete()
	 */
	public function delete(){
		$model = $this->model ();
		$id = $_REQUEST ['id'];
		$options = array ('where' => array ('id' => array ('in',$id ) ) );
		
		// 删除的数据表
		$data = $model->field('tablename,is_content')->select($options);
		foreach ($data AS $value){
			$table .= '`'.C('DB_PREFIX').$value['tablename'].'`,';
			$value['is_content'] == 1 && $table .= '`'.C('DB_PREFIX').$value['tablename'].'_data`,';
		}
		
		if($model->delete ( $options )){
			// 删除表
			$sql = 'DROP TABLE '.trim($table,',');
			$model->query($sql);
			
			// 删除字段
			M('ModuleField')->where(array('mid'=>array('in',$id)))->delete();
			
			$this->success ( '删除模型成功！' );
		}else{
			$this->error ( '删除模型失败！' );
		}
	}
	
}