<?php
/**
 * 模型管理
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Model;

class ModuleFieldModel extends CommonModel{
	/**
	 * 自动验证
	 * @var $_validate
	 */
	protected $_validate = array(
			array('title', 'require', '名称不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_BOTH),
			array('name', '/^\w+$/', '字段名格式错误！', self::MUST_VALIDATE, 'regex', self:: MODEL_INSERT),
			array('name', 'unique', '字段名不能重复！', self::MUST_VALIDATE, 'callback', self:: MODEL_INSERT),
			array('length', 'number', '表名不能重复！', self::VALUE_VALIDATE, 'regex', self:: MODEL_BOTH),
	);
	
	/**
	 * 自动完成
	 * @var $_auto
	 */
	protected $_auto = array(
			array('status', '1', self::MODEL_INSERT, 'string'),
	);
	
	/**
	 * 验证重复
	 * @see CommonModel::unique()
	 */
	protected function unique() {
		$map = array('name' => $_POST['name'], 'mid' => $_POST['mid']);
		$pk = array($this->getPk() => $_POST[$this->getPk()]);
		return parent::unique($map, $pk);
	}
	
	/**
	 * 创建字段
	 * @param $data 字段数据
	 * @see Model::_after_insert()
	 */
	public function _after_insert($data){
		// 增加表字段
		$data['tablename'] = M('Module')->where(array('id'=>$data['mid']))->getField('tablename');
		$sql = $this->_sqlString($data);
		$this->query($sql);
	}
	
	/**
	 * sql语句
	 * @param string $tablename
	 * @param number $length
	 * @param string $comment
	 * @return string
	 */
	private function _sqlString($data){
		$sql = 'ALTER TABLE  `'.C('DB_PREFIX') . $data['tablename'].'` ADD  `'. $data['name'].'` ';
		
		switch ($data ['type']) {
			case 'text' :
				$length = ($data ['length'] <= 0 || $data ['length'] > 255) ? 255 : $data ['length'];
				$sql .= 'VARCHAR( ' . $length . ' ) CHARACTER SET utf8 COLLATE utf8_general_ci';
				break;
			case 'textarea' :
				$sql .= 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci';
				break;
			case 'editor' :
				$sql .= 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci';
				break;
			case 'radio' :
				$length = ($data ['length'] <= 0 || $data ['length'] > 3) ? 3 : $data ['length'];
				$sql .= 'TINYINT( ' . $length . ' ) UNSIGNED';
				break;
			case 'checkbox' :
				$length = ($data ['length'] <= 0 || $data ['length'] > 3) ? 3 : $data ['length'];
				$sql .= 'TINYINT( ' . $length . ' ) UNSIGNED';
				break;
			case 'date' :
				$sql .= 'INT( 10 ) UNSIGNED';
				break;
			case 'image' :
				$length = ($data ['length'] <= 0 || $data ['length'] > 255) ? 255 : $data ['length'];
				$sql .= 'VARCHAR( ' . $length . ' ) CHARACTER SET utf8 COLLATE utf8_general_ci';
				break;
			case 'file' :
				$length = ($data ['length'] <= 0 || $data ['length'] > 255) ? 255 : $data ['length'];
				$sql .= 'VARCHAR( ' . $length . ' ) CHARACTER SET utf8 COLLATE utf8_general_ci';
				break;
			case 'money' :
				$length = ($data ['length'] <= 0 || $data ['length'] > 10) ? 10 : $data ['length'];
				$sql .= 'DECIMAL(' . $length . ',2)';
				break;
			case 'number' :
				$length = ($data ['length'] <= 0 || $data ['length'] > 10) ? 10 : $data ['length'];
				$sql .= 'INT( ' . $length . ' ) UNSIGNED';
				break;
			case 'select' :
				$length = ($data ['length'] <= 0 || $data ['length'] > 3) ? 3 : $data ['length'];
				$sql .= 'TINYINT( ' . $length . ' ) UNSIGNED';
				break;
			default :
				$sql = 'VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci';
		}
		
		$sql .= ' NOT NULL ' . empty($data['tips']) ? '' : 'COMMENT  \''.$data['tips'].'\'';
		return $sql;
	}
}

?>