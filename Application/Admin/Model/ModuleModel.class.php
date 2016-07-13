<?php
/**
 * 模型管理
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Model;

class ModuleModel extends CommonModel{
	/**
	 * 自动验证
	 * @var $_validate
	 */
	protected $_validate = array(
			array('title', 'require', '模型名称不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
			array('tablename', 'require', '表名不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_INSERT),
			array('tablename', '/^\w+$/', '表名格式错误！', self::MUST_VALIDATE, 'regex', self:: MODEL_INSERT),
			array('tablename', 'unique', '表名不能重复！', self::MUST_VALIDATE, 'unique', self:: MODEL_INSERT),
	);
	
	/**
	 * 自动完成
	 * @var $_auto
	 */
	protected $_auto = array(
			array('create_time', 'time', self:: MODEL_INSERT, 'function'),
			array('update_time', 'time', self:: MODEL_BOTH, 'function'),
			array('status', '1', self::MODEL_INSERT, 'string'),
	);
	
	/**
	 * 创建实体表
	 * @param $data 表数据
	 * @see Model::_after_insert()
	 */
	public function _after_insert($data){
		// 基础表
		$sql = "
			CREATE TABLE `". C('DB_PREFIX') . $data['tablename'] ."` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
			  `title` varchar(100) NOT NULL COMMENT '标题',
			  `keyword` varchar(255) NOT NULL COMMENT '关键词',
			  `description` varchar(255) NOT NULL COMMENT '描述',
			  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
			  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
			  `status` tinyint(1) NOT NULL COMMENT '状态',
			  `uid` int(10) NOT NULL COMMENT '用户ID',
			  `sort` int(10) NOT NULL COMMENT '排序',
			  `cid` int(10) NOT NULL COMMENT '分类id',
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8";

		// 创建关联内容表
		$content = array();
		$this->query($sql);
		if(!empty($data['is_content'])){
			$sql = "
				CREATE TABLE `". C('DB_PREFIX') . $data['tablename'] ."_data` (
				  `id` int(10) unsigned NOT NULL  COMMENT '编号',
				  `content` text NOT NULL COMMENT '内容',
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8";
			$this->query($sql);
			
			$content[] = array('mid'=>$data['id'],'title'=>'内容','name'=>'content','type'=>'editor','tips'=>'','length'=>0,'status'=>1,'is_show'=>1);
		}
		
		// 记录默认字段
		$data_all = array(
			array('mid'=>$data['id'],'title'=>'标题','name'=>'title','type'=>'text','tips'=>'','length'=>100,'status'=>1,'is_show'=>1),
			array('mid'=>$data['id'],'title'=>'关键词','name'=>'keyword','type'=>'textarea','tips'=>'该网页的关键词','length'=>255,'status'=>1,'is_show'=>1),
			array('mid'=>$data['id'],'title'=>'描述','name'=>'description','type'=>'textarea','tips'=>'对该网页内容的描述','length'=>255,'status'=>1,'is_show'=>1),
			array('mid'=>$data['id'],'title'=>'用户ID','name'=>'uid','type'=>'number','tips'=>'','length'=>10,'status'=>1,'is_show'=>0)
		);
		$data_all = array_merge($data_all,$content);
		D('ModuleField')->addAll($data_all);
		write_log('module_field',M()->_sql());
	}
}

?>