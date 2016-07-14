<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/11
 * Time: 17:08
 */

namespace Admin\Model;


class SchoolWeddingModel extends CommonModel{
    //头条id与上传附件绑定
    public function _after_insert($data, $options) {
        $record_id = $data['id'];
        $content = $data['content'];
        $module = CONTROLLER_NAME;
        $content = htmlspecialchars_decode($content);
        $imgs_url_arr = $this->get_img_url_by_html($content);
        foreach ($imgs_url_arr as $key=>$value){
            $url_arr=explode('/',$value);
            $saveName_arr[] = array_pop($url_arr);
            unset($url_arr);
        }
        if(!empty($saveName_arr)){
            $where['savename'] = array('in',$saveName_arr);
            $where['module'] = $module;
            M('Attach')->where($where)->setField('record_id',$record_id);
        }

    }

    /**
     * 提取html中图片地址
     * @param $str
     * @return array
     */
    public function get_img_url_by_html($str) {
        $pattern = "/[img|IMG].*?src=['|\"]\/?(.*?(?:[.gif|.jpg|.jpeg|.png|.GIF|.JPG|.JPEG|.PNG]))['|\"].*?[\/]?>/";
        preg_match_all($pattern, $str, $match);
        return isset($match[1]) ? $match[1] : array();
    }


}