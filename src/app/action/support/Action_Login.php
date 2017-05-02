<?php

class Action_Login extends _Action_Support {
	protected $is_login_check = false;
	
	protected function doAction() {
		$this->output->assign('appname', 'Entrust Survey');
		$this->output->assign('Login', 'Login');
		$this->output->assign('errors', false);
		
		$this->output->assign('email', '');
		$this->output->assign('password', '');
		
		$this->output->setTmpl('support/login.php');
	}
}