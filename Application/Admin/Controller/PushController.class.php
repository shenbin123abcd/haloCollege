<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/26
 * Time: 16:03
 */

namespace Admin\Controller;

use Think\Controller;

class PushController extends Controller {
    private $app_key = 'eb9b8a1cc71a294e9225c211';
    private $master_secret = '4b0f11f62c881a49af4ddd98';

    public function pushMsgAlert($msg=array('content'=>'','extra'=>array(),'object'=>'all')) {
        // 初始化
        $client = new \JPush($this->app_key, $this->master_secret, LOG_PATH . '/Api/jpush' . date('Ymd') . '.log');
        $platForm = array('ios', 'android');
        $msg_content = $msg['content'];
        $msg_title = "msg title1";
        $msg['extra']['content'] = $msg_content;
        $msg['extra']['from_username'] = '';
        $msg['extra']['from_uid'] = 0;
        $msg['extra']['to_uid'] = 0;
        $msg['extra']['detail_id'] = '';
        $msg['extra']['extra'] = '';
        $msg['extra']['is_read'] = 0;
        $msg['extra']['remark_type'] = 1;
        $msg_extra = $msg['extra'];

        $sendno = rand(100001, 999999);
        $time_to_live = 3600;
        $override_msg_id = null;
        $apns_production = false;
        if ($msg['object']=='adr'){
            $result = $client->push()->setPlatform($platForm)->addAllAudience()
                //->addAlias('alias1')
                //->addTag(array('tag1', 'tag2'))
                //->setNotificationAlert('通知')
                ->addAndroidNotification($msg_content,$msg_title,1,$msg_extra)
                //->addIosNotification($msg_content, 'iOS sound', \JPush::DISABLE_BADGE, true, 'iOS category',$msg_extra)
                //->setMessage($msg_content, $msg_title, $msg_type, $msg_extra)
                ->setOptions($sendno,$time_to_live,$override_msg_id,$apns_production)
                ->send();
        }elseif ($msg['object']=='ios'){
            $result = $client->push()->setPlatform($platForm)->addAllAudience()
                //->addAlias('alias1')
                //->addTag(array('tag1', 'tag2'))
                //->setNotificationAlert('通知')
                //->addAndroidNotification($msg_content,$msg_title,1,$msg_extra)
                ->addIosNotification($msg_content, 'iOS sound', \JPush::DISABLE_BADGE, true, 'iOS category',$msg_extra)
                //->setMessage($msg_content, $msg_title, $msg_type, $msg_extra)
                ->setOptions($sendno,$time_to_live,$override_msg_id,$apns_production)
                ->send();
        }else{
            $result = $client->push()->setPlatform($platForm)->addAllAudience()
                //->addAlias('alias1')
                //->addTag(array('tag1', 'tag2'))
                //->setNotificationAlert('通知')
                ->addAndroidNotification($msg_content,$msg_title,1,$msg_extra)
                ->addIosNotification($msg_content, 'iOS sound', \JPush::DISABLE_BADGE, true, 'iOS category',$msg_extra)
                //->setMessage($msg_content, $msg_title, $msg_type, $msg_extra)
                ->setOptions($sendno,$time_to_live,$override_msg_id,$apns_production)
                ->send();
        }


        return $result;
        //    echo 'Result=' . json_encode($result);

    }

    public function pushMsgPersonal($msg = array('uid' =>0, 'content' => '', 'extra' => array(),'type'=>'')) {
        // 初始化
        $client = new \JPush($this->app_key, $this->master_secret);
        $platForm = array('ios', 'android');

        //$msg_content = $msg['content'];
        //$msg_title = "msg title";
        //$msg_type = $msg['type'];
        //数据全部封装到extra
        $msg['extra']['content'] = $msg['content'];
        $msg['extra']['msg_type'] = $msg['type'];
        $msg_extra = $msg['extra'];

        //$alias = "halocollege_".$msg['uid'];
        $alias = "halocollege".$msg['uid'];
        $tags = array();

        $sendno = rand(100001, 999999);
        $time_to_live = 3600;
        $override_msg_id = null;
        $apns_production = false;
        $result = $client->push()->setPlatform($platForm)->addAlias($alias)//->addTag($tags)
            //->setNotificationAlert('张虎')
            //->addAndroidNotification('Hi, android notification', 'notification title', 1, array("key1"=>"value1", "key2"=>"value2"))
            //->addIosNotification("Hi, iOS notification", 'iOS sound', \JPush::DISABLE_BADGE, true, 'iOS category', array("key1"=>"value1", "key2"=>"value2"))
            ->setMessage('', '', '', $msg_extra)->setOptions($sendno, $time_to_live, $override_msg_id, $apns_production)->send();
        return $result;
        //echo 'Result=' . json_encode($result);

    }


}