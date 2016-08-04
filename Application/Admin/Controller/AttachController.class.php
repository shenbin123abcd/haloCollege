<?php
namespace Admin\Controller;

class AttachController extends CommonController {

    //编辑器使用的上传
    public function editor() {
        $model = D('Attach');
        $config = array(
            'allowExts' => 'jpg,gif,png,jpeg',
            'savePath' => C('UPLOAD_PATH') . MODULE_NAME . '/',
        );
        $result = $model->upload($_FILES['imgFile'],$config);
        $data = array(
            'error' => $result['status']==1 ? 0 : 1,
            'url' => ltrim($result['data']['savepath'] . $result['data']['savename'],'.'),
            'message' => $result['info'],
        );
        echo json_encode($data);
    }


    //附件查询
    public function select() {

        $where = array('record_id' => $_REQUEST['id'],'module'=>$_REQUEST['module'],'status'=>1);
        $data = D('Attach')->field('id,name,key,concat(savepath,savename) as src')->where($where)->order('id desc')->select();
        foreach ($data as $key => $value) {
            $data[$key]['src'] = C('APPS_PATH') . trim($value['src'],'.');
        }
        if(!empty($data)){
            $result=array('status' => 1, 'info' => '查询附件成功！', 'data' =>$data);
        }else{
            $result=array('status' => 0, 'info' => '查询附件失败！');
        }
        echo json_encode($result);
    }

    //附件预览
    public function preview(){
        if($_GET['id']){
            $data = D('Attach')->field('concat(savepath,savename) AS path')->where(array('id'=>$_GET['id']))->find();
            if(file_exists($data['path'])){
                redirect(ltrim($data['path'],'.'));
            }else{
                $this->error('文件不存在！');
            }
            
        }
    }

    /**
     * 删除图片
     * @return [type] [description]
     */
    public function delete(){
        $status = D('Attach')->del($_GET['id']);
        $status ? $this->success('删除成功！') : $this->error('删除失败！');
    }
}

?>