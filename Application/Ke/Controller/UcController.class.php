<?php
namespace Ke\Controller;

use Think\Controller;

class UcController extends CommonController {
    public function book() {
        $this->assign('is_address', get_address($this->user['id']) ? 1 : 0);
        $this->display();
    }

    // 案例支付成功记录
    public function caseRecord(){
        $list = M('wfc2016_order_case')->where(array('record_id'=>$this->user['id'], 'status'=>1))->field('order_no,goods_name,goods_id,goods_subtitle,goods_cover,goods_url,type,price,module,spec,num')->order('id DESC')->select();
        foreach ($list as $key => $value) {
            // 样片
            if ($value['module'] == 'case') {
                $list[$key]['type'] = $value['type'] == '98' ? '基础款' : '高级款';
                $list[$key]['send_date'] = '2016-08-30';
            }elseif ($value['module'] == 'book') {
                $list[$key]['type'] = '';
                $list[$key]['send_date'] = '2016-08-30';
            }elseif ($value['module'] == 'daoju') {
                $list[$key]['type'] = $value['spec'];
                $list[$key]['send_date'] = '';
            }
        }

        $list = $list ? $list : array();
        $this->assign('list', json_encode($list));

        $this->display();
    }
}