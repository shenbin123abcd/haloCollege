<?php
namespace Admin\Controller;

class ConfigController extends CommonController {
	public function _join(&$data){
        $group = C('CONFIG_GROUP');
        foreach($data as $key=>$value){
            $data[$key]['group']=$group[$value['group']];
        }
    }

    // 配置
    public function config(){
    	$this->group = C('CONFIG_GROUP');
    	$this->list = M('Config')->where(array('status'=>1))->order('id ASC,sort ASC')->select();
    	
    	$this->display();
    }

    //保存配置
    public function saveConfig() {
        foreach ($_POST as $key => $value) {
            //M('Config')->where(array('name' => $key))->getField('type') == 'date' && $value = strtotime($value);
            M('Config')->where(array('name' => $key))->save(array('value' => $value));
        }
        $this->success('更新配置成功！');
    }
}