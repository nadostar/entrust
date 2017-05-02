<?php

class Action_LoginExec extends _Action_Support {
	/** 共通ログインチェックを動作させるか */
	protected $is_login_check = false;
	
	/** エラーメッセージ */
	protected $error_msg = '';
	
	protected function preAction() {
		parent::preAction();
		//　画面のエラーインフョメーション部分に表示するために、preAction内で手動validateキック
		$this->registValidatorMap('email', 'Validator_Input', "invalid email or password!");
		$this->registValidatorMap('password', 'Validator_Input', "invalid email or password!");
		
		try {
			$this->validParam();
		} catch (Exception $e) {
			$this->error_msg = $e->getMessage();
		}
	}
	
	protected function doAction() {
		$email = $this->getQuery('email');
		$password = $this->getQuery('password');

		$is_error = false;

		if(strlen($this->error_msg) > 0) {
			$this->output->assign('error', $this->error_msg);
			$is_error = true;
		} else {
			if(!$this->loginProc($email, $password)) {
				$this->output->assign('error', "Sorry, invalid email or password!");
				$is_error = true;
			}
		}

		if($is_error) {
			$this->output->assign('email', $email);
			$this->output->assign('password', $password);
			$this->output->setTmpl('support/login.php');
		} else {
			$this->redirect_url = Env::APP_URL.'support/dashboard/';
			$this->output = null;
		}
	}

	private function loginProc($email, $password) {
		$crypt_password = $this->cryptPassword($password);

		$adminData = Logic_Admin::login($this->system_db, $email, $crypt_password);
		if($adminData === false) {
			return false;
		}

		$allowData = Logic_Permission::getAlreadyAllowMap($this->system_db, $adminData['permission_id']);
		if($allowData === false) {
			return false;
		}

		$sessionData = new AdminData($adminData, $allowData);
		Util_Session::set(self::LOGIN_SESSION_KEY, $sessionData);

		Logic_Log::adminlog($this->log_db, $sessionData->getAdminId(), Category::LOGIN, '', $this->ip_address);

		return true;
	}
}