<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/11
 * Time: 17:08
 */

namespace Admin\Model;


class SchoolWeddingModel extends CommonModel{
    public function _after_insert($data, $options) {
        $record_id = $data['id'];
        $content = $data['content'];
        $imgs_url_arr = $this->get_img_url_by_html($content);
        

        //CONTROLLER_NAME

    }
    /**
     * 提取html中图片地址
     * @param $str
     * @return array
     */
    function get_img_url_by_html($str) {
        $pattern = "/[img|IMG].*?src=['|\"]\/?(.*?(?:[.gif|.jpg|.jpeg|.png|.GIF|.JPG|.JPEG|.PNG]))['|\"].*?[\/]?>/";
        preg_match_all($pattern, $str, $match);
        return isset($match[1]) ? $match[1] : array();
    }


}