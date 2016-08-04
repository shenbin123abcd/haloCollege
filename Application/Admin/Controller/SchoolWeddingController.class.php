<?php

/**
 * Created by PhpStorm.
 * User: zhanghu
 * Date: 2016/6/28
 * Time: 16:55
 */
namespace Admin\Controller;

class SchoolWeddingController extends CommonController {

    public function index() {
        $this->_list($this->model());
        $list = $this->list;
        $categorys = M('SchoolWeddingCategory')->where("status=1")->select();
        foreach ($list as $key => $value) {
            foreach ($categorys as $key_cat => $value_cat) {
                if ($value['category_id'] == $value_cat['id']) {
                    $list[$key]['category_name'] = $value_cat['name'];

                }
            }
        }
        $this->list = $list;
        cookie('__forward__', $_SERVER ['REQUEST_URI']);
        $this->display($_GET['display'] ? $_GET['display'] : 'index');
    }


    public function _before_add() {
        $category = M('SchoolWeddingCategory')->where("status=1")->field('id,name')->select();
        $this->assign('category', $category);
        $this->token = $this->qiniu('crmpub', 'SchoolWeddingCover');


    }

    public function _before_update() {
        $pattern = "/(<img.*?)(src=.*?\/?>)/";
        $string = preg_replace($pattern, "$1style='width:100%;display:block;'$2", $_POST['content']);
        $_POST['content'] = $string;
    }

    public function _before_insert() {
        empty($_POST['headline']) && $this->error('请填写婚礼标题！');
        empty($_POST['brief']) && $this->error('请填写婚礼简介！');
        empty($_POST['category_id']) && $this->error('请选择头条分类！');
        empty($_POST['content']) && $this->error('请编辑头条内容！');
        $pattern = "/(<img.*?)(src=.*?\/?>)/";
        $string = preg_replace($pattern, "$1style='width:100%;display:block;'$2", $_POST['content']);
        $_POST['content'] = $string;
        $_POST['create_time'] = time();
        $_POST['update_time'] = time();
        $_POST['status'] = 1;
        $_POST['uid'] = $this->user['id'];

    }


    public function _before_edit() {
        $this->_before_add();
        $attach = M('Attach')->where(array('record_id' => I('id'), 'module' => 'SchoolWeddingCover', 'status' => 1))->field('id,savename')->select();
        foreach ($attach as $key => $value) {
            $attach[$key]['src'] = 'http://7xopel.com2.z0.glb.clouddn.com/' . $value['savename'];
        }
        $this->attach = $attach;
    }

    /**
     * 默认编辑操作
     * @see CommonAction::edit()
     */
    public function edit() {
        $base_url = 'http://7xopel.com2.z0.glb.clouddn.com/';
        $model = $this->model();
        $pk = $model->getPk();
        $data = $model->where(array($pk => $_GET[$pk]))->find();
        empty($data) && $this->error('查询数据失败！');
        $categoryName = M('SchoolWeddingCategory')->where(array('id' => $data['category_id']))->field('name')->find();
        $where['status'] = 1;
        $imgs = M('Attach')->where(array('status' => 1, 'module' => 'SchoolWedding', 'record_id' => $_GET[$pk]))->field('id,name,savename as url')->select();
        if (!empty($imgs)) {
            foreach ($imgs as $key => $value) {
                $imgs[$key]['url'] = $base_url . $value['url'];
            }
        }
        $cate = $this->category;
        foreach ($cate as $key => $value) {
            if ($value['id'] == $data['category_id']) {
                $cat_arr = $cate[$key];
                unset($cate[$key]);
            }
        }
        array_unshift($cate, $cat_arr);
        $this->category = $cate;
        $this->assign('imgs', $imgs);
        $this->assign('categoryName', $categoryName);
        $this->assign('data', $data);
        $this->display();
    }


    //删除图片--封面
    public function attach_delete() {
        $model = M('Attach');
        $arrach_id = I('id');
        $where['id'] = $arrach_id;
        $where['module'] = 'SchoolWeddingCover';
        $attach = $model->where($where)->find();
        if (empty($attach)) {
            $this->error('该图片不存在了！');
        }
        if ($attach['status'] == 0) {
            $this->error('该图片已经被移除了！');
        }
        $attach['status'] = 0;
        $result = $model->save($attach);
        if ($result !== false) {
            $this->success('图片移除成功！');
        }
    }


    //删除图片--编辑器
    public function deleteImg() {
        $attach_id = $_GET['attach_id'];
        $wedding_id = $_GET['wedding_id'];
        if (empty($attach_id) || empty($wedding_id)) {
            $this->error('参数错误！');
        }
        $result = D('SchoolWedding')->delete_img($attach_id, $wedding_id);
        if ($result) {
            $this->success('列表图片删除成功，请手动删除编辑器中对应的图片！');
        } else {
            $this->error('删除图片失败！');
        }

    }

    /**
     * 头条访问统计
     */

    public function visits() {
        $model = M('WeddingVisitcount');
        $where['wtw_wedding_visitcount.status'] = 1;
        $count = $model->join('left join wtw_school_wedding on wtw_school_wedding.id=wtw_wedding_visitcount.wedding_id')
            ->field('wtw_wedding_visitcount.*,wtw_school_wedding.headline')->where($where)->select();
        $this->assign('list', $count);
        $this->display('visits');
    }


}