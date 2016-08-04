<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/26
 * Time: 16:03
 */

namespace Api\Controller;
use Think\Controller;
require_once("/vendor/jpush/jpush/src/JPush/JPush.php");
class PushController extends CommonController{
    private   $app_key = 'eb9b8a1cc71a294e9225c211';
    private   $master_secret = '4b0f11f62c881a49af4ddd98';
public function pushMsg(){
    // 初始化
    $client = new \JPush($this->app_key, $this->master_secret);
    // 完整的推送示例,包含指定Platform,指定Alias,Tag,指定iOS,Android notification,指定Message等
    $result = $client->push()
        ->setPlatform(array('ios', 'android'))
        ->addAlias('alias1')
        ->addTag(array('tag1', 'tag2'))
        ->setNotificationAlert('Hi, JPush')
        ->addAndroidNotification('Hi, android notification', 'notification title', 1, array("key1"=>"value1", "key2"=>"value2"))
        ->addIosNotification("Hi, iOS notification", 'iOS sound', \JPush::DISABLE_BADGE, true, 'iOS category', array("key1"=>"value1", "key2"=>"value2"))
        ->setMessage("msg content", 'msg title', 'type', array("key1"=>"value1", "key2"=>"value2"))
        ->setOptions(100000, 3600, null, false)
        ->send();

    echo 'Result=' . json_encode($result);

}



}