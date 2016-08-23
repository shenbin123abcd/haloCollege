<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/29
 * Time: 17:49
 */

namespace Api\Controller;
use Think\Controller;

class CommunityController extends CommonController{
    /**
     * 微社区banner
     * @param  string $title 标题o
     * @param  string $url   地址
     * @param  string $type  category话题组,topic话题，feed帖子，link网页,video视频,image图片，
     * @return
     */
    public function banner(){
        $data = array(
            array('id'=>'5797219aea77f7a768b0f1cd','title'=>'中国婚礼行业高峰论坛', 'desc'=>'中国婚礼行业高峰论坛','img'=>'http://collegeapi-test.weddingee.com/Public/College/Banner/1.png', 'type'=>'category'),
            array('id'=>'5797218955c400c93316bbb7','title'=>'中国婚礼策划金熊奖', 'desc'=>'中国婚礼策划金熊奖','img'=>'http://collegeapi-test.weddingee.com/Public/College/Banner/2.png','type'=>'category'),
            array('id'=>'579721a8d0146397fce08a38', 'title'=>'中国婚礼公开课', 'desc'=>'中国婚礼公开课','img'=>'http://collegeapi-test.weddingee.com/Public/College/Banner/3.png','type'=>'category'),
        );
        $this->success('success', $data);
    }

}