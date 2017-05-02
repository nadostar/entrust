<?php

/**
* 
*/
class Action_Main extends _Action_Support {
	protected $is_login_check = false;
	
	protected function doAction() {
		$this->output->setTmpl('support/main.php');
	}
}