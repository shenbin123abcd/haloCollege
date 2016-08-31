<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/26
 * Time: 16:03
 */

namespace Api\Controller;

use Think\Controller;

class PushController extends CommonController {
    private $app_key = 'eb9b8a1cc71a294e9225c211';
    private $master_secret = '4b0f11f62c881a49af4ddd98';

    public function pushMsgAlert() {
        // 初始化
        $client = new \JPush($this->app_key, $this->master_secret, LOG_PATH . '/Api/jpush' . date('Ymd') . '.log');
        $platForm = array('ios', 'android');
        $msg_content = "通知";
        $msg_title = "msg title";
        $msg_type = "type";
        $msg_extra = array("key1" => "value1", "key2" => "value2");

        $sendno = 100000;
        $time_to_live = 3600;
        $override_msg_id = null;
        $apns_production = false;
        $result = $client->push()->setPlatform($platForm)->addAllAudience()//->addAlias('alias1')
            //->addTag(array('tag1', 'tag2'))
            ->setNotificationAlert('通知')//->addAndroidNotification('Hi, android notification', 'notification title', 1, array("key1"=>"value1", "key2"=>"value2"))
            //->addIosNotification("Hi, iOS notification", 'iOS sound', \JPush::DISABLE_BADGE, true, 'iOS category', array("key1"=>"value1", "key2"=>"value2"))
            //->setMessage($msg_content, $msg_title, $msg_type, $msg_extra)
            //->setOptions($sendno,$time_to_live,$override_msg_id,$apns_production)
            ->send();

        return 'Result=' . json_encode($result);

    }

    public function pushMsgPersonal($msg = array('uid' => 0, 'content' => '', 'extra' => array())) {
        // 初始化
        $client = new \JPush($this->app_key, $this->master_secret);
        $platForm = array('ios', 'android');

        $msg_content = $msg['content'];
        $msg_title = "msg title";
        $msg_type = "type";
        $msg_extra = $msg['extra'];

        $alias = "halocollege_" . $msg['uid'];
        $tags = array();

        $sendno = 100000;
        $time_to_live = 3600;
        $override_msg_id = null;
        $apns_production = false;
        $result = $client->push()->setPlatform($platForm)->addAlias($alias)//->addTag($tags)
            //->setNotificationAlert('张虎')
            //->addAndroidNotification('Hi, android notification', 'notification title', 1, array("key1"=>"value1", "key2"=>"value2"))
            //->addIosNotification("Hi, iOS notification", 'iOS sound', \JPush::DISABLE_BADGE, true, 'iOS category', array("key1"=>"value1", "key2"=>"value2"))
            ->setMessage($msg_content, $msg_title, $msg_type, $msg_extra)->setOptions($sendno, $time_to_live, $override_msg_id, $apns_production)->send();
        return $result;
        //echo 'Result=' . json_encode($result);

    }


}