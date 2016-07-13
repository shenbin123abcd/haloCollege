<?php

namespace Admin\Controller;
// 首页
class IndexController extends CommonController {
	/**
	 * 后台首页
	 */
    public function index(){

    	$menu = $this->_menu();		
    	$this->assign('menu',json_encode($menu));
		$this->display();
	}
	
	/**
	 * 导航菜单
	 * @return array
	 */
	private function _menu(){
		$menu_list = M('Menu')->where(array('status'=>1))->order('sort asc')->getField('id,name,parent,url');
		$menu = array();
		foreach ($menu_list AS $value){
			if($value['parent'] == 'root'){
				$menu[$value['id']] = $value;
			}
		}
		foreach ($menu AS &$value){
			foreach ($menu_list AS $l_value){
				if($value['id'] == $l_value['parent']){
					$l_value['url'] = U($l_value['url']);
					$value['items'][$l_value['id']] = $l_value;
					continue;
				}
			}
		}
		unset($value);
		return $menu;
	}
	
	/**
	 * 常用菜单设置
	 */
	public function custom_set(){
		// 数据提交
		if(IS_AJAX || IS_POST){
			//$this->success('操作成功');
			$this->error('操作失败');
		}
		
		$this->menu = $this->_menu();
		$this->display();
	}
	
	/**
	 * 获取系统信息
	 */
	public function main() {
		$info = array(
				'操作系统' => PHP_OS,
				'运行环境' => $_SERVER["SERVER_SOFTWARE"],
				'PHP运行方式' => php_sapi_name(),
				'ThinkPHP版本' => THINK_VERSION . ' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
				'上传附件限制' => ini_get('upload_max_filesize'),
				'执行时间限制' => ini_get('max_execution_time') . '秒',
				'服务器时间' => date("Y年n月j日 H:i:s"),
				'北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
				'服务器域名/IP' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
				'剩余空间' => round((disk_free_space(".") / (1024 * 1024)), 2) . 'M',
				'register_globals' => get_cfg_var("register_globals") == "1" ? "ON" : "OFF",
				'magic_quotes_gpc' => (1 === get_magic_quotes_gpc()) ? 'YES' : 'NO',
				'magic_quotes_runtime' => (1 === get_magic_quotes_runtime()) ? 'YES' : 'NO',
		);
		$this->info = $info;
		$this->display();
	}
	
	/**
	 * 数据树
	 */
	public function tree(){
		$list = D('Category')->where(array('status'=>1))->order('id ASC')->field('id,title,pid')->select();

		foreach ($list as $key => $value) {
			$list[$key]['name'] = $value['title'];
			$list[$key]['pId'] = $value['pid'];
			$list[$key]['url'] = __GROUP__.'/article/index/cid/'.$value['id'];
			$list[$key]['dataid'] = 'article'.$value['id'];
			$key == 0  && $list[$key]['open'] = 'true';
			/*if($value['id'] == 1){
				$list[$key]['icon'] = './Public/Admin/Js/dev/util_libs/ztree/img/diy/1_close.png';
			}*/
		}
		$this->ajaxReturn(to_tree($list));
	}
}