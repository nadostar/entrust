<?php

class Action_SessionOff extends _Action_Support {
	protected $is_login_check = false;
	
	protected function doAction() {
		$this->output->setTmpl('support/session_off.php');
	}
}