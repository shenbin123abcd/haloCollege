<?php

namespace Admin\Controller;

class CourseReserveController extends CommonController {
    public function filter(&$map){
        $map['status'] = 1;
    }

    public function _join(&$data){
        if (!empty($data)){
            foreach ($data as $item) {
                $course_id[] = $item['course_id'];
            }

            $course = M('Course')->where(array('id'=>array('in', $course_id)))->getField('id, title');
            foreach ($data as $key=>$itme){
                $data[$key]['course'] = $course[$item['course_id']];
            }
        }
    }

}