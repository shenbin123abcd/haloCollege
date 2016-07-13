<?php

namespace Admin\Model;
class AttachModel extends CommonModel {

	//自动验证
	protected $_validate = array(
		array('key', 'require', '表单名称不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_INSERT),
		array('module', 'require', '模型名称不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_UPDATE),
		array('record_id', 'require', '记录编号不能为空！', self::MUST_VALIDATE, 'regex', self:: MODEL_UPDATE),
	);

	//自动完成
	protected $_auto = array(
		array('create_time', 'time', self::MODEL_INSERT, 'function'),
		array('update_time', 'time', self::MODEL_BOTH, 'function'),
		array('status', '1', self::MODEL_INSERT, 'string'),
	);

	//附件服务器
	protected function _after_select(&$data,$options){
		if(isset($data[0]['savepath'])&&isset($data[0]['savename'])){
			foreach($data as $key=>$val){
				$data[$key]['url']=attach_server($val['savepath'].$val['savename']);
			}
		}
	}

	/**
	 * 上传附件
	 *@param array $file 文件
	 *@param array $config 配置
	 *@return array
	*/
	public function upload($file,$config=array()) {
		// 导入上传类
		import('ORG.Net.Upload');
		// 实例化对象
		$upload = new Upload();
		foreach($config as $key=>$value){
			$upload->$key=$value;
		}
		// 上传文件返回结果
		return $upload->save($file);
	}

    /**
     * 下载文件
     * 可以指定下载显示的文件名，并自动发送相应的Header信息
     * 如果指定了content参数，则下载该参数的内容
     * @access public
     * @param string $filename 下载文件名
     * @param string $showname 下载显示的文件名
     * @param string $content  下载的内容
     * @param integer $expire  下载内容浏览器缓存时间
     */
	public function download ($filename, $showname='',$content='',$expire=180){
		import('ORG.Net.Http');
		Http::download ($filename,$showname,$content,$expire=180);
	}

	/**
     * 删除附件
     * @param  int 	id
     * @return [type]      地址
     */
    public function del($id){
    	$map = array('id'=>$id);
    	$result = $this->where($map)->field('concat(savepath,savename) AS src')->find();
        // 删除图片文件
        unlink($result['src']);
        $return = $this->where($map)->delete();
       return $return;
    }

}

?>
