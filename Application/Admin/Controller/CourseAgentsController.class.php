<?php

namespace Admin\Controller;

class CourseAgentsController extends CommonController {
    public function _join(&$data){
        foreach ($data as $key=>$item) {
            $data[$key]['code'] = 'http://ke.halobear.com?code=' . $item['code'];
        }
    }



}