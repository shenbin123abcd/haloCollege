<?php
/**
 * 公共模型
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Model;
use Think\Model;

class CommonModel extends Model{
	/**
	 * 获取缓存数据列表
	 * @param string $cache true 使用缓存，false 重新生成缓存
	 * @return array
	 */
	public function getCacheList($cache = true){
		$cache_name = strtolower($this->name);
		
		$data = S($cache_name);
		if(!$cache || empty($data) || APP_DEBUG){
			$data = $this->order('sort ASC,id DESC')->select();
			S($cache_name,$data);
		}
		
		return $data;
	}
	
	/**
	 * 缓存过滤
	 * @param array $map 条件
	 * @return array 结果
	 */
	public function getCacheResult($map = array()){
		$list = $this->getCacheList();
		list($key,$value) = each($map);
		$result = array();
		foreach ($list AS $r_value){
			if($r_value[$key] == $value){
				$result[] = $r_value;
			}
		}
		
		return $result;
	}
	
	/**
	 * 获取父级数据列表
	 * @param number $id 当前分类的ID
	 * @return array
	 */
	public function getParentList($id = 0){
		if(empty($id)){
			return array();
		}
		
		$list = $this->getCacheList();
		
		foreach ($list AS $value){
			if($value['id'] == $id){
				$result[] = $value;
				$id = $value['pid'];
				if ($id == 0) {
					break;
				}else{
					reset($list);
					continue;
				}
			}
		}
		
		return array_reverse($result);
	}
	

	/**
	 * 验证是否重复
	 * @param type $map 验证条件
	 * @param type $pk 主键信息 格式array(id=>1)
	 * @return boolean
	 */
	protected function unique($map, $pk) {
		$pk = each($pk);
		$map = array_merge(array('status' => array('egt', 0)), $map);
		$result = $this->where($map)->order($pk['key'] . ' asc')->find();
		if (empty($pk['value'])) {
			return empty($result) ? true : false;
		} else {
			if (empty($result)) {
				return true;
			} else {
				return $result[$pk['key']] == $pk['value'] ? true : false;
			}
		}
	}
}

?>