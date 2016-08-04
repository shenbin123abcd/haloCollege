<?php
/**
 * Created by PhpStorm.
 * User: zhanghu
 * Date: 2016/4/27
 * Time: 17:57
 */
namespace Admin\Controller;

class SchoolLivesController extends CommonController {
    public function _before_index(){
        $this->_before_add();

    }

    public function _before_insert(){
        empty($_POST['cover_url']) && $this->error('请上传封面图');
        $this->_before_update();

    }

    public function _before_edit(){
        $this->_before_add();

    }


    public function search(){
        $title = I('title');
        $title =trim($title);
        $list = M('SchoolLives')->where(array('title'=>array('like', '%'. $title .'%')))->select();
        $data['data'] = $list;
        $data['message'][0] = empty($list) ? 'FORUM:searchforum.notfound' : 'FORUM:searchforum.success';
        $data['refresh'] = false;
        $data['state']  = empty($list) ? 'fail' : 'success';
        $this->ajaxReturn($data);
    }

    public function _before_add(){
        $this->token = $this->qiniu('crmpub', 'college/cover/');
    }

    public function _before_update(){
        !$this->_checkVideo($_POST['url']) && $this->error('直播不存在，请检查');
        empty($_POST['area']) && $this->error('请填写城市');
        empty($_POST['start_time']) && $this->error('请填写直播开始时间');
        empty($_POST['end_time']) && $this->error('请填写直播结束时间');
        empty($_POST['description']) && $this->error('请填写直播描述');
//        $down = $this->_privateDownloadUrl('http://7o4zdo.com2.z0.glb.qiniucdn.com/' . $_POST['url'] . '?avinfo');
//        $ret = curl_get(str_replace(' ', '%20', $down));
//        $_POST['times'] = format_duration($ret['format']['duration']);

    }

    //获取关联嘉宾
    public function bindGuest(){
        $this->_before_add();
        $model = $this->model();
        $pk = $model->getPk();
        $liveId=$_GET[$pk];
//        $model=D('SchoolLiveBrief');
        $sql="select a.guest_id,b.title,a.status from wtw_school_live_brief a left join wtw_school_guests b on a.guest_id=b.id
               where a.live_id=$liveId";
        $data=M()->query($sql);
        $this->live_id=$liveId;
//        $data=$model->join('wtw_school_guests on wtw_school_live_brief.live_id=wtw_school_guests.id')
//                    ->field('wtw_school_live_brief.guest_id,wtw_school_live_brief.headline,wtw_school_guests.title,wtw_school_live_brief.status')
//                    ->where("live_id=$liveId")->select();

        $this->assign('list',$data);
        $this->display();
    }

    //删除嘉宾
    public function deleteGuest(){
        $guest_id=$_GET['guest_id'];
        $model=D('SchoolLiveBrief');
        $model-> where("guest_id=$guest_id")->delete() ? $this->success ( '删除数据成功！' ) : $this->error ( '删除数据失败！' );

    }

    //禁用嘉宾
    public function forbidGuest(){
        $data = array('status' => '0');
        $guest_id=$_GET['guest_id'];
        $model=D('SchoolLiveBrief');
        $model-> where("guest_id=$guest_id")->save($data) ? $this->success ( '禁用数据成功！' ) : $this->error ( '禁用数据失败！' );

    }

    //恢复禁用嘉宾
    public function resumeGuest(){
        $data = array('status' => '1');
        $guest_id=$_GET['guest_id'];
        $model=D('SchoolLiveBrief');
        $model-> where("guest_id=$guest_id")->save($data) ? $this->success ( '恢复数据成功！' ) : $this->error ( '恢复数据失败！' );

    }
    //直播与嘉宾关联
    public function bindGuestSave(){
        empty($_POST['live_id']) && $this->error('直播id不存在！');
        empty($_POST['guest_id']) && $this->error('请填写要绑定嘉宾的id');
        $data['live_id']=$_POST['live_id'];
        $liveId=$data['live_id'];
        $data['guest_id']=$_POST['guest_id'];
        $guestId=$data['guest_id'];
        $model=D('SchoolLiveBrief');
        $guestIds=$model->query("select guest_id from wtw_school_live_brief where live_id=$liveId");
        $guest=M()->query("select * from wtw_school_guests where id=$guestId");
        foreach($guestIds as $value) {
            if ($value['guest_id'] == $guestId) {
                $this->error("嘉宾 $guestId 不能被重复绑定！");
                exit;
            }
        }
        if(empty($guest)){
            $this->error("嘉宾 $guestId 还未创建,不能绑定！");

        }
            if ($model->create()) {
                $id = $model->add();
                !empty($id) ? $this->success('新增数据成功！',U("/school_lives/bindGuest/id/$liveId"),cookie('__forward__')) : $this->error('新增数据失败！');

            } else {
                $this->error($model->getError());
            }
        }




    private function _privateDownloadUrl($baseUrl, $expires = 3600){
        $deadline = time() + $expires;
        $pos = strpos($baseUrl, '?');
        if ($pos !== false) {
            $baseUrl .= '&e=';
        } else {
            $baseUrl .= '?e=';
        }
        $baseUrl .= $deadline;

        $token = $this->sign($baseUrl);
        return "$baseUrl&token=$token";
    }

    protected function sign($data){
        $hmac = hash_hmac('sha1', $data, C('QINIU_SK'), true);
        return C('QINIU_AK') . ':' . $this->base64_urlSafeEncode($hmac);
    }

    protected function base64_urlSafeEncode($data){
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }

    private function _checkVideo($url){
        // 使用七牛获取资源信息(stat)方法 (http://developer.qiniu.com/docs/v6/api/reference/rs/stat.html)
        $bucket = 'halocollege';

        // EncodedEntryURI
        $entry = $bucket.':'.$url;
        $find = array('+', '/');
        $replace = array('-', '_');
        $encodedEntryURI = str_replace($find, $replace, base64_encode($entry));

        // access token
        $baseurl = '/stat/'.$encodedEntryURI;
        $token = $this->_qiniu_token($baseurl."\n");

        $header = array();
        $header[] = 'Authorization:'.'QBox '.$token;
        $result = curl_get('http://rs.qiniu.com'.$baseurl, $header);

        return $result['fsize'] > 0;
    }

    private function _qiniu_token($signingStr){
        $accessKey = 'm_bQ6vCqK-1n_myddynLMQxg0rxw3YqRptv5D7_i';
        $secretKey = 'EH7AQcudIK47egCwYGzrSFVnutvuCYedfr0Lyl3d';

        $find = array('+', '/');
        $replace = array('-', '_');
        $sign = hash_hmac('sha1', $signingStr, $secretKey, true);
        $encodedSign  = str_replace($find, $replace, base64_encode($sign));

        return $accessKey . ':' . $encodedSign;
    }


}