<?php
/**
 * 邀请码管理
 *
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Controller;

class SchoolCodeController extends CommonController {
	public function _join(&$data){
		foreach ($data as $key => $value) {
			$id[] = $value['id'];
		}

		/*$list = M('DownList')->where(array('cid'=>array('in',$id)))->getField('cid,ip,create_time');

		foreach ($data as $key => $value) {
			$data[$key]['ip'] = $list[$value['id']]['ip'];
			$data[$key]['create_time'] = empty($list[$value['id']]['create_time']) ? '' : date('m-d H:i',$list[$value['id']]['create_time']);
		}*/
	}

	public function _before_index(){
		$this->use_total = $this->model()->sum('use_num');
	}

	/**
	 * 禁用
	 */
	public function forbid() {
		$data = array('status' => '0');
		$id = $_REQUEST[$this->model()->getPk ()];
		empty($id) && $this->error('请选择操作对象!');
		$options = array('where' => array($this->model()->getPk () => array('in', $id), 'status' => 1));
		if($this->model()->save($data, $options) === false){
			$this->error('状态禁用失败！');
		}else{
			$this->success('状态禁用成功！');
		}
	}
	
	/**
	 * 恢复
	 */
	public function resume() {
		$data = array('status' => '1');
		$id = $_REQUEST[$this->model()->getPk ()];
		empty($id) && $this->error('请选择操作对象!');
		$options = array('where' => array($this->model()->getPk () => array('in', $id), 'status' => 0));
		if($this->model()->save($data, $options) === false){
			$this->error('状态恢复失败！');
		}else{
			$this->success('状态恢复成功！');
		}
	}

	// 生成随机字符串
	private function _rand($len) {
		// 去掉了容易混淆的字符oOLl和数字01
		$chars = 'abcdefghjkmnpqrstuvwxyz23456789';
		if ($len > 10) { // 位数过长重复字符串一定次数
			$chars = str_repeat ( $chars, 5 );
		}
	
		$chars = str_shuffle ( $chars );
		$str = substr ( $chars, rand ( 0, 5 ), $len );
		return $str;
	}

	// 生成账号
	private function _account() {
		$account = $this->_rand ( 6 );
		if (M('SchoolCode')->where ( array ('code' => $account ) )->count ( 'id' )) {
			$this->_account ();
		}
		return $account;
	}

	public function insert(){
		$model = D('SchoolCode');
		$number = intval($_POST['number']);
		($number <= 0 || $number > 100) && $this->error('数量填写错误！');
		if($model->create()){
			$data['total_num'] = $_POST['total_num'] ? $_POST['total_num'] : 1;
			for ($i = 0; $i < $number; $i++){
				$data['code'] = $this->_account();
				$data['status'] = 1;
				$data['tag'] = $_POST['tag'];
				$model->add($data);
			}
			$this->success('邀请码生成完成！',cookie ( '__forward__' ));
		}else{
			$this->error($model->getError());
		}
	}


}
