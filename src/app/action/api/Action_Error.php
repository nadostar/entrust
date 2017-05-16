<?php

require_once __DIR__.'/../_Action_Api.php';

/**
* 
*/
class Action_Error extends _Action_Api {
	
	protected function initialize() {
		parent::initialize();
	}

	protected function preAction() {
		parent::preAction();
	}

	protected function doAction() {
		$data = array(
			"code" => "403",
			"title" => "Opps!",
			"message" => "Sorry, but the page you are looking for has note been found. Try checking the URL for error, then hit the refresh button on your browser or try found something else in our app."
		);

		$this->output->assign('data', $data);
		$this->output->setTmpl('api/error.php');
	}
}