<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/31
 * Time: 11:55
 */

namespace Admin\Controller;


class IosVersionController extends CommonController{
    public function _before_insert(){
        $version = $_POST['version'];
        $count = M('IosVersion')->where(array('version'=>$version))->count();
        if ($count){
            $this->error('改版本号对应的状态记录已经存在，无需添加，前往编辑即可！');
        }
    }

}