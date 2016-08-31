<?php
namespace Ke\Controller;

use Think\Controller;

class CourseController extends CommonController {
    public function index() {
        
        $this->success([['id'=>1, 'title'=>'标题1'],['id'=>2, 'title'=>'标题2']]);
    }

}