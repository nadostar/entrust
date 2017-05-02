<?php

class Action_AuthenticationError extends _Action_Support {
	protected $is_login_check = false;
	
	/**
	 * @see _Action::doAction()
	 */
	protected function doAction() {
		$this->output->setTmpl('support/authentication_error.php');
	}
}