<?php
namespace Ke\Controller;

use Think\Controller;

class IndexController extends CommonController {
    public function index() {
        $code = I('code');
        $agents = cookie('agents');
        if (!empty($code)){ //  && empty($agents)
            // 检查code的有效性
            $check = M('CourseAgents')->where(['code'=>$code, 'status'=>1])->count();

            if ($check){
                cookie('agents', $code, 86400 * 30);
            }
        }

        $this->display();
    }

}