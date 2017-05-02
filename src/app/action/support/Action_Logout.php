<?php

class Action_Logout extends _Action_Support {

	protected function doAction() {
		Util_Session::remove(self::LOGIN_SESSION_KEY);
		$this->redirect_url = $this->redirect_url = Env::APP_URL.'support/login/';
		$this->output = null;
	}
	
}