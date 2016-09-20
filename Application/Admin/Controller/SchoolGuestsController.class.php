<?php
/**
 * SchoolGuests
 * @author wtwei
 * @version $Id$
 */
namespace Admin\Controller;
class SchoolGuestsController extends CommonController {
	public function _join(&$data){
		
	}

	public function _before_edit(){
		$this->_before_add();
	}
	
	public function _before_add(){
		$this->token = $this->qiniu('crmpub', 'college/avatar');
	}

}