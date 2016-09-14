<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/9/13
 * Time: 15:45
 */

namespace Api\Controller;

class PaymentController extends CommonController {
    protected $action_auth = ['createOrder'];

    public function createMemberOrder(){
        $cate_id = intval(I('cate_id'));

        // 检查开通分类
        $cate = M('SchoolMemberCate')->where(array('id'=>$cate_id, 'status'=>1))->find();
        empty($cate) && $this->error('分类不存在');


    }
}