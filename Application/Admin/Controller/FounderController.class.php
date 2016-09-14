<?php
/**
 * 创始人管理
 * @author wtwei
 * @version $Id$
 */
 namespace Admin\Controller;
class FounderController extends CommonController {
	/**
	 * 数据处理
	 * @param arrar $data
	 */
	public function _join(&$data){
		foreach ($data AS $value){
			$uid[] = $value['uid'];
		}
		$data = M('Member')->where(array('id'=>array('in',$uid)))->field('id,username')->select();
	}
	
	/**
	 * 删除操作
	 * @see CommonAction::delete()
	 */
	public function delete(){
		$model = $this->model ();
		$options = array ('where' => array ($model->getPk () => array ('in',$_REQUEST [$model->getPk ()] ) ) );
		$model->delete ( $options ) ? $this->success ( '删除数据成功！' ) : $this->error ( '删除数据失败！' );
	}
	
	/**
	 * 新增前置操作
	 */
	public function _before_insert(){
		if(!M('Member')->where(array('username'=>$_POST['username']))->count('id')){
			$this->ajaxReturn(array('url'=>U('Member/add',array('username'=>$_POST['username'],'act'=>'founder')),'status'=>2));
		}
	}
}