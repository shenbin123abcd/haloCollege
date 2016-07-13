<?php

namespace Think\Template\TagLib;

use Think\Template\TagLib;
class Lists extends TagLib {

	//定义标签
	protected $tags = array(
		'list' => array('attr' => 'type,name,id,value,checkbox', 'close' => 0),
	);

	//标签解析 - 3.2.3属性系统自动解析，不用手动解析
	public function _list($attr) {
		//$tag = $this->parseXmlAttr($attr, 'list');
		return $this->parseShowStr($attr);
	}

	//字符串方式解析显示列
	public function parseShowStr($tag) {
		$box = $tag['box'];
		$checkbox = $tag['checkbox'];
		$id = empty($tag['id']) ? 'vo' : $tag['id'];
		$name = $tag['name'];
		$show = $tag['show'];
		$show = preg_replace(array('/^{/', '/[^\[\]\{\}\:\,]+/', '/\}\s*\{/', '/}$/', '/\{\{/', '/\}\}/'), array('[{', '"\0"', '},{', '}]', '[{', '}]'), $show);
		$show = json_decode($show, true);
		if (empty($show))
			return;
		$parse.= '<table width="100%">';
		$checkbox_html = empty($box) ? '' : '
					<td width="25">
						<label><input type="checkbox" class="J_check_all" data-direction="x" data-checklist="J_check_x">全选</label>
					</td>';
		$parse.=empty($box) ? '' : 
			'<thead>
				<tr>'.$checkbox_html;
		foreach ($show as $value) {
			$parse.='<td '.(empty($value['width'])?'':'width="'.$value['width'].'"').'><b>';
			if(!empty($value['order'])){
				$parse.='<a href="';
				$parse.='<php>$info=parse_url($_SERVER["REQUEST_URI"]);</php>';
				$parse.='<php>parse_str($info["query"],$param);</php>';
				$parse.='<php>$param["_field"]="'.$value['field'].'";</php>';
				$parse.='<php>$param["_order"]="'.$value['order'].'";</php>';
				$parse.='<php>$_REQUEST["_field"]=="'.$value['field'].'"&&($param["_order"]=$_REQUEST["_order"]=="asc"?"desc":"asc");</php>';
				$parse.='<php>echo $info["path"]."?".http_build_query($param);</php>';
				$parse.='"';
				$parse.='<php>echo $_REQUEST["_field"]=="'.$value['field'].'"?\'class="\'.$_REQUEST[\'_order\'].\'"\':\'\'</php>';
				$parse.='>'.($value['title']).'</a>';
			}else{
				$parse.=$value['title'];
			}
			$parse.=is_array($value[0])?$value[0]['title']:'';
			$parse.='</b></td>';
		}
		$parse.='</tr></thead><volist name="' . $name . '" id="' . $id . '"><tr>';
		$parse.=empty($box) ? '' : '<td><input name="id[]" class="J_check" data-yid="J_check_y" data-xid="J_check_x" type="checkbox" value="{$' . $id . '.' . $box . '}"/></td>';
		// dump($show);exit;
		foreach ($show as $value) {
			$value = is_array($value[0]) ? $value : array($value);
			$parse.='<td '.(empty($value[0]['class'])?'':'class="'.$value[0]['class'].'"').'>';
			foreach ($value as $column) {
				$column['field'].=empty($column['func']) ? '' : ( '|' . preg_replace(array('/\/+/', '/#(\w+)/'), array(',', ':$1'), $column['func']));
				if (empty($column['url'])) {
					// 表单类型
					if($column['input'] == 'text'){
						$parse.= '<input type="text" class="input length_1" name="'.$column['field'].'[{$'.$id.'[id]}]" value="{$'.$id.'['.$column['field'].']}" />';
					}else{
						$parse.= empty($column['field']) ? $column['text'] : '{$' . $id . '.' . $column['field'] . '}';
					}
				} else {
					$column['url'] = preg_replace('/\$([\w]+)/', '{$' . $id . '.$1}', $column['url']);
					// 表单类型
					if($column['input'] == 'text'){
						$parse.= '<input type="text" class="input length_1" name="'.$column['field'].'['.'{$' . $id . '.' . $column['field'] . '}'.']" value="$'.$id.'['.$column['field'].']" />';
						continue;
					}
					// 状态显示
					if (isset($column['status'])){
						$parse .= '<eq name="'. $id .'.status" value="'. $column['status'] .'">';
						$parse.='<a href="' . $column['url'] . '" title="'.$column['text'].'"';
						foreach ($column as $k => $v) {
							in_array($k, array('title', 'field', 'func', 'text', 'url', 'width')) || $parse.=' ' . $k . '=' . '"' . $v . '"';
						}
						$parse.= $column['field'] == "" ? '>[' . $column['text'] : '>[{$' . $id . '.' . $column['field'] . '}';
						$parse.=']</a>';
						$parse .='</eq>';
					}else {
						$parse.='<a href="' . $column['url'] . '" title="'.$column['text'].'"';
						foreach ($column as $k => $v) {
							in_array($k, array('title', 'field', 'func', 'text', 'url', 'width')) || $parse.=' ' . $k . '=' . '"' . $v . '"';
						}
						$parse.= $column['field'] == "" ? '>[' . $column['text'] : '>[{$' . $id . '.' . $column['field'] . '}';
						$parse.=']</a>';
					}
				}
			}
			$parse.= '</td>';
		}
		$parse.='</tr></volist></table>';
		$parse.='<empty name="'.$name.'"><div class="not_content_mini"><i></i>啊哦，没有内容哦！</div></empty>';
		return $parse;
	}

}

?>