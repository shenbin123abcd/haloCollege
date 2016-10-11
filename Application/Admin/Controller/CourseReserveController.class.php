<?php

namespace Admin\Controller;

class CourseReserveController extends CommonController {
    //public function filter(&$map){
    //    $map['status'] = 1;
    //}

    public function _join(&$data){
        if (!empty($data)){
            foreach ($data as $item) {
                $course_id[] = $item['course_id'];
            }

            $title = $_REQUEST['course_title'];

            $course = M('Course')->where(array('id'=>array('in', $course_id),'title'=>array('like','%'.$title.'%')))->getField('id, title');
            foreach ($data as $key=>$item){
                $data[$key]['course'] = $course[$item['course_id']];
                if (empty($data[$key]['course'])){
                    unset($data[$key]);
                }                
            }
        }
    }
    
    
     

}