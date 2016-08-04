<?php
// 商户模型
namespace Api\Model;
use Think\Model;

class WorkStoreModel extends Model {
    /**
     * 检查手机号是否开通，未开通自动开通
     * @param  string $phone 手机号
     * @return [type]        [description]
     */
    public function checkAccount($username, $phone) {
        $model = M('SchoolAccount');
        $count = $model->where(array('phone' => $phone))->count();
        $ret['iRet'] = 1;
        if (!$count) {
            $ret = $this->_register($username, $phone);
        }

        return $ret;
    }

    /**
     * 账号中心注册
     * @return [type] [description]
     */
    private function _register($username, $phone) {
        $api = C('AUTH_API_URL') . 'user';
        $data = array('username' => $username, 'phone' => I('phone'), 'password' => rand(100000, 999999), 'regip' => get_client_ip());
        $result = curl_post($api, $data);

        if ($result['iRet'] == 1) {
            // 同步注册
            $model = M('SchoolAccount');
            $id = $model->add($data);

            // 短信通知
            send_msg($phone, array($phone, $data['password']), 89033, 'aaf98f89493217c801493229479e0007');
            write_log('store_open', var_export($data, 1));
        }

        return $result;
    }

    /**
     * 开通
     * @param  array $data 商户数据
     * @return [type]       [description]
     */
    public function open($data) {
        $api = C('AUTH_API_URL') . 'store/open';
        $result = curl_post($api, $data);

        return $result;
    }

    public function company($data) {

        $api = C('AUTH_API_URL') . 'company?' . http_build_query($data);
        $result = curl_get($api, $data);

        return $result;
    }

    /**
     * 商城
     * @return [type] [description]
     */
    public function shopSyn($data) {
        $data['time'] = time();
        write_log('store_open_shop', var_export($data, 1));
        ksort($data);
        $data = http_build_query($data);
        $config = C('SHOP_PARAMS');
        $temp = $data . '&token=' . sha1($data . $config['key']);

        $result = curl_get($config['private_url'] . 'addCustomer?' . $temp);
        if (!empty($result) && $result['iRet'] == 1) {
            return array('iRet' => 1, 'info' => '余额充值成功');
        } else {
            return array('iRet' => 0, 'info' => '余额充值失败，请联系客服', 'err' => $result);
        }
    }
}

?>
