<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/11
 * Time: 17:08
 */

namespace Admin\Model;


class SchoolWeddingModel extends CommonModel{
    //添加分享页地址
    public function _after_insert($data, $options) {
        $model = M('SchoolWedding');
        $where['id'] = $data['id'];
        $url = 'http://college-api.halobear.com/toutiao/detail?wedding_id=';
        $weddinge_id = $data['id'];
        $share_url = $url.$weddinge_id;
        $wedding = $model->where($where)->find();
        $wedding['share_url'] = $share_url;
        $model->save($wedding);
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
    
   //删除头条图片
    public function delete_img($attach_id,$wedding_id){
        $base_url = 'http://7xopel.com2.z0.glb.clouddn.com/';
        $attach = M('Attach')->where(array('id'=>$attach_id))->find();
        //$wedding = M('SchoolWedding')->where(array('id'=>$wedding_id))->find();
        //$savename = $attach['savename'];
        //$url = $base_url.$savename;
        //$pattern = '/<img src="'."$url".'"'.' alt="" />/';
        //$content_decode = htmlspecialchars_decode($wedding['content']);
        //preg_replace($pattern,'aaa',$content_decode);
        if(empty($attach)){
            $this->error('该图片不存在！');
        }
        if($attach['status']==0){
            $this->error('该图片已经被删除！');
        }
        $attach['status']=0;
        $result = M('Attach')->save($attach);   
        if($result!==false){
            return true;
        }
        return false;
    }



}