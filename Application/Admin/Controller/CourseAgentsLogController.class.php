<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/10/16
 * Time: 18:34
 */

namespace Admin\Controller;


class CourseAgentsLogController extends CommonController
{
    protected function _join(&$data){
        $type = ['返佣', '提现', '退款'];
        foreach ($data as $key=>$value) {
            $data[$key]['type'] = $type[$value['type']];
        }
    }
}