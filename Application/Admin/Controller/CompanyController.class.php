<?php
namespace Admin\Controller;

class CompanyController extends CommonController {
	protected function _join(&$data){
		foreach ($data as $key => $value) {
			$data[$key]['is_os'] = $value['is_os'] ? '是' : '否';
			$data[$key]['is_flower'] = $value['is_flower'] ? '是' : '否';
			$data[$key]['is_app'] = $value['is_app'] ? '是' : '否';
			$data[$key]['is_bear'] = $value['is_bear'] ? '是' : '否';
			$data[$key]['is_shouhui'] = $value['is_shouhui'] ? '是' : '否';
			$data[$key]['is_rss'] = $value['is_rss'] ? '是' : '否';
		}
	} 
	/**
	 * 查询过滤
	 */
	protected function filter(&$map = array()){
		$_GET['username'] && $map['username'] = array('like', '%' . $_GET['username'] . '%');
		$_GET['contact'] && $map['contact'] = array('like', '%' . $_GET['contact'] . '%');
		$_GET['company_name'] && $map['company_name'] = array('like', '%' . $_GET['company_name'] . '%');
	}

	public function _before_index(){
		$this->region = M('Region')->field('region_id,region_name,parent_id,level')->select();
	}

	public function _before_add(){
		$this->region = M('Region')->field('region_id,region_name,parent_id,level')->select();
	}

	public function _before_edit(){
		$this->_before_add();
	}

	public function _before_insert($type){
		$_POST['province_title'] = M('Region')->where(array('region_id'=>$_POST['province']))->getField('region_name');
		$_POST['city_title'] = M('Region')->where(array('region_id'=>$_POST['city']))->getField('region_name');
		$_POST['region_title'] = M('Region')->where(array('region_id'=>$_POST['region']))->getField('region_name');

		if (empty($_POST['province']) || empty($_POST['city'])) {
			$this->error('请选择地区');
		}elseif (empty($_POST['company_name'])) {
			$this->error('请填写公司名');
		// }elseif (empty($_POST['username'])) {
		// 
		// 	$this->error('请填写联系人');
		}elseif (empty($_POST['contact'])) {
			$this->error('请填写联系方式');
		}

		if (empty($type)) {
			$map = array('company_name'=>$_POST['company_name']);
			if ($_POST['username']) {
				$map['username']= $_POST['username'];
			}else{
				$map['username'] = array('neq', '');
			}

			$count = M('Company')->where($map)->count();
			if ($count) {
				// $this->error('数据已存在');
			}
		}

		$_POST['relation'] = implode(',', $_POST['relation']);
	}

	public function _before_update(){
		$this->_before_insert(1);
	}

	public function excel(){
		$user = session('member_admin');
		
		$file = $_FILES['excel'];
		if (empty($file)) {
			$this->error('请上传excel文件');
		}elseif (substr(strrchr($file['name'], '.'), 1) != 'xls') {
			$this->error('文件格式错误');
		}
		Vendor('excelread.reader');
		$data = new Spreadsheet_Excel_Reader();

		$data->setOutputEncoding('utf-8');
		
		$data->read($file['tmp_name']);

		$data_list = array();
		$fields = array('province_title', 'city_title', 'region_title', 'company_name', 'type', 'username', 'contact', 'job_title', 'relation', 'is_os', 'is_flower', 'is_app', 'is_rss', 'is_bear', 'is_shouhui',  'is_class', 'address', 'remark');
		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
			$key = 0;
			$n = 0;
			for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
				$data_list[$i][$fields[$n]] = (string)$data->sheets[0]['cells'][$i][$j];
				$n++;
			}
			$data_list[$i]['admin_name'] = $user['username'];
			$data_list[$i]['uid'] = $user['id'];
		}
		
		
		$model = $this->model();
		$region_model = M('Region');
		$num = 0;
		foreach ($data_list as $key => $value) {
			$value['province'] = $region_model->where(array('region_name'=>array('like', '%'. $value['province_title'] .'%'),'level'=>1))->getField('region_id');
			$value['city'] = $region_model->where(array('region_name'=>array('like', '%'. $value['city_title'] .'%'),'level'=>2))->getField('region_id');
			$value['region'] = $region_model->where(array('region_name'=>array('like', '%'. $value['region_title'] .'%'),'level'=>3))->getField('region_id');
			
			// 检查
			if ($model->where(array('province'=>$value['province'], 'city'=>$value['city'], 'contact'=>$value['contact'],'company_name'=>$value['company_name'],'username'=>$value['username']))->count()) {
				$error .= $value['province_title'] . ', ' . $value['city_title'] . ', ' . $value['company_name'] . ', ' . $value['username'] . '<br>' ;
			}
			if (!empty($value['company_name'])) {
				$value['create_time'] = time();
				$ret = $model->add($value);
				$ret && $num++;
			}			
		}
		if (empty($error)) {
			$this->success('成功导入 ' . $num . ' 条');
		}else{
			echo '已存在的数据：<br>' . $error;
		}
	}
}