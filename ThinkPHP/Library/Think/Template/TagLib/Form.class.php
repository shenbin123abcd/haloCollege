<?php

// +----------------------------------------------------------------------
// | TOPThink
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: TagLibAttr.class.php 550 2011-12-30 03:47:03Z liuchen $
// 表单标签库
namespace Think\Template\TagLib;

use Think\Template\TagLib;
class Form extends TagLib {

    // 标签定义
    protected $tags = array(
        'attr' => array('attr' => 'var,type,default', 'close' => 0),
    );

    /**
     * 标签属性
     * @param type $attr 属性:name,value,type,param
     * @param type $content
     * @return string 
     */
    public function _attr($attr, $content) {
        $tag = $this->parseXmlAttr($attr, 'attr');
        if (!empty($tag['var'])) {
            $var = $tag['var'];
            $parse = '<?php if(empty($' . $var . ')):echo "变量”' . $var . '“不存在！";else:?>' . "\n";
            $parse.= '<?php switch($' . $var . '["type"]): ?>' . "\n";
            $parse.= '<?php case "text":?>' . $this->_text($var) . '<?php break;?>' . "\n";
            $parse.= '<?php case "password":?>' . $this->_password($var) . '<?php break;?>' . "\n";
            $parse.= '<?php case "number":?>' . $this->_number($var) . '<?php break;?>' . "\n";
            $parse.= '<?php case "money":?>' . $this->_text($var) . '<?php break;?>' . "\n";
            $parse.= '<?php case "textarea":?>' . $this->_textarea($var) . '<?php break;?>' . "\n";
            $parse.= '<?php case "editor":?>' . $this->_editor($var) . '<?php break;?>' . "\n";
            $parse.= '<?php case "radio":?>' . $this->_radio($var) . '<?php break;?>' . "\n";
            $parse.= '<?php case "checkbox":?>' . $this->_checkbox($var) . '<?php break;?>' . "\n";
            $parse.= '<?php case "select":?>' . $this->_select($var) . '<?php break;?>' . "\n";
            $parse.= '<?php case "file":?>' . $this->_file($var) . '<?php break;?>' . "\n";
            $parse.= '<?php case "image":?>' . $this->_image($var) . '<?php break;?>' . "\n";
            $parse.= '<?php case "date":?>' . $this->_date($var) . '<?php break;?>' . "\n";
            $parse.= '<?php case "editor":?>' . $this->_editor($var) . '<?php break;?>';
            $parse.= '<?php default: echo "“".$' . $var . '["type"]."”类型表单不存在！"; break;?>' . "\n";
            $parse.= '<?php endswitch;endif;?>' . "\n";
        } else {
            $parse = '缺少创建表单属性！';
        }
        return $parse;
    }

    public function _text($var) {
        $parse = '<input type="text" name="{$' . $var . '.name}" value="{$' . $var . '.value}" class="input length_{$' . $var . '.input_length}"/>';
        return $parse;
    }

    public function _number($var) {
        $parse = '<input type="number" name="{$' . $var . '.name}" value="{$' . $var . '.value}" class="input length_{$' . $var . '.input_length}"/>';
        return $parse;
    }

    public function _password($var) {
        $parse = '<input type="password" name="{$' . $var . '.name}" value="{$' . $var . '.value}" class="input length_{$' . $var . '.input_length}"/>';
        return $parse;
    }

    public function _textarea($var) {
        $parse = '<textarea name="{$' . $var . '.name}" class="length_5">{$' . $var . '.value}</textarea>';
        return $parse;
    }

    public function _radio($var) {
        $parse = '<?php $value=$' . $var . '["value"];?>';
        $parse .= '<?php $param=$' . $var . '["param"];?>';
        $parse.= '<?php if(json_decode($param)): ?>';
        $parse.= '<?php $arr = json_decode($param); ?>';
        $parse.= '<?php elseif(function_exists($param)): ?>';
        $parse.= '<?php $arr = $param(); endif;?>';
        $parse.= '<ul class="switch_list cc">';
        $parse.= '<?php foreach($arr as $key=>$val):?>';
        $parse.= '<li><label><input type="radio" name="{$' . $var . '.name}" value="{$key}" <eq name="value" value="$key">checked="checked"</eq>/><span>{$val}</span></label></li>';
        $parse.= '<?php endforeach;?>';
        $parse.= '</ul>';
        return $parse;
    }

    public function _checkbox($var) {
        $parse.= '<?php $value=explode(",",$' . $var . '["value"]);?>';
        $parse.= '<?php $param=$' . $var . '["param"];?>';
        $parse.= '<?php if(json_decode($param)): ?>';
        $parse.= '<?php $arr = json_decode($param); ?>';
        $parse.= '<?php elseif(function_exists($param)): ?>';
        $parse.= '<?php $arr = $param(); endif;?>';
        $parse.= '<input type="hidden" name="{$' . $var . '.name}[]" value=""/>';
        $parse.= '<ul class="three_list cc">';
        $parse.= '<?php foreach($arr as $key=>$val):?>';
        $parse.= '<li><label><input type="checkbox" name="{$' . $var . '.name}[]" value="{$key}" id="{$' . $var . '.name}_{$key}" <in name="key" value="$value">checked="checked"</in>/><span>{$val}</span></label></li>';
        $parse.= '<?php endforeach;?>';
        $parse.= '</ul>';
        return $parse;
    }

    public function _select($var) {
        $parse = '<select name="{$' . $var . '.name}" class="select_{$' . $var . '.input_length}">';
        $parse.= '<?php $value=$' . $var . '["value"];?>';
        $parse .= '<?php $param=$' . $var . '["param"];?>';
        $parse.= '<?php if(json_decode($param)): ?>';
        $parse.= '<?php $arr = json_decode($param); ?>';
        $parse.= '<?php elseif(function_exists($param)): ?>';
        $parse.= '<?php $arr = $param(); endif;?>';
        $parse.= '<?php foreach($arr as $key=>$val):?>';
        $parse.= '<option value="{$key}" <eq name="value" value="$key">selected="selected"</eq>>{$val}</option>';
        $parse.= '<?php endforeach;?>';
        $parse.='</select>';
        return $parse;
    }

    public function _file($var) {
        $parse = '<input type="file" name="{$' . $var . '.name}" value="{$' . $var . '.value}" class="file {$' . $var . '.input_length}"/>';
        return $parse;
    }

    public function _image($var) {
        $parse = '<input type="file" name="{$' . $var . '.name}" value="{$' . $var . '.value}" class="file {$' . $var . '.input_length}"/>';
        return $parse;
    }

    public function _images($var) {
        $parse = '<input type="file" name="{$' . $var . '.name}" max="{$' . $var . '.length}" value="{$' . $var . '.value}" class="file {$' . $var . '.input_length}"/>';
        return $parse;
    }

    /*public function _image($var) {
    	$parse = '<div class="single_image_up">';
    	$parse .= '<a href="">上传图片</a>';
        $parse .= '<input type="file" name="{$' . $var . '.name}" class="J_upload_preview" data-preview="#j_preview_img"/>';
        $parse .= '</div>';
        $parse .= '<img id="j_preview_img" style="display: none; max-width: 300px;">';
        return $parse;
    }*/

    public function _date($var) {
        $parse = '<input type="text" name="{$' . $var . '.name}" value="{$' . $var . '.value|date=\'Y-m-d\',###}" class="input J_date date length_{$' . $var . '.length}"/>';
        return $parse;
    }

    public function _editor($var) {
        $parse = '<textarea name="{$' . $var . '.name}" class="editor" type="editor">{$' . $var . '.value}</textarea>';
        return $parse;
    }

}

?>