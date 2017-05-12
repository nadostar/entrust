<?php

require_once __DIR__.'/../_Action_Api.php';

/**
* 
*/
class Action_Receive extends _Action_Api {

	private $receive_progress_map = array(
		'c' => 1,
		's' => 2,
		'q' => 3
	);

	protected function initialize() {
		parent::initialize();
	}

	protected function preAction() {
		parent::preAction();

		try {
			$this->registValidatorMap('r');
			$this->registValidatorMap('k');
			$this->registValidatorMap('esid');

			$this->validParam();
		} catch (Exception $e) {
			LogManager::debug($e->getMessage());
			return;
		}
	}

	protected function doAction() {
		$accesskey 	= trim($this->getQuery('r'));
		$status 	= trim($this->getQuery('k'));
		$esid 		= trim($this->getQuery('esid'));

		$params = array(
			'accesskey' => $accesskey,
			'status' 	=> $status,
			'esid' 		=> $esid,
			'ip' 		=> $this->ip_address
		);

		$this->validateParameters($params);

		$history = $this->findHistory($params);

		$snapshot = $this->findAccesskey($params);

		$this->validateProject($snapshot, $params);

		$this->jumpToPartnerRedirectLink($snapshot, $history, $params);
	}

	protected function validateParameters($params) {
		if(empty($params['accesskey'])) {
			LogManager::debug("[ERROR] code=55560, params=".json_encode($params));
			//$this->errorlog("NONE", "Receive", "55560", "Invalid accesskey", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}

		if(strlen($params['accesskey']) != Env::ACCESSKEY_SIZE) {
			LogManager::debug("[ERROR] code=55561, params=".json_encode($params));
			//$this->errorlog("NONE", "Receive", "55561", "Invalid accesskey size", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}

		if(empty($params['esid'])) {
			LogManager::debug("[ERROR] code=55562, params=".json_encode($params));
			//$this->errorlog("NONE", "Receive", "55562", "Invalid esid", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}

		if(empty($params['status'])) {
			LogManager::debug("[ERROR] code=55563, params=".json_encode($params));
			//$this->errorlog("NONE", "Receive", "55563", "Invalid status", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}

		try {
			$this->receive_progress_map[$params['status']];
		} catch (Exception $e) {
			//$this->errorlog("NONE", "Receive", "55564", "Invalid status value", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}
	}

	protected function findAccesskey($params) {
		$data = Logic_Snapshot::getSnapshotDataByAccesskey($this->slave_db, $params['accesskey']);

		if($data === false) {
			LogManager::debug("[ERROR] code=83729, params=".json_encode($params));
			//$this->errorlog("NONE", "Receive", "73729", "Accesskey is not found", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}

		return $data;
	}

	protected function findHistory($params) {
		$history = Logic_LinkHistory::findLinkHistory($this->slave_db, $params['accesskey'], $params['esid']);

		if($history === false) {
			LogManager::debug("[ERROR] code=84728, params=".json_encode($params));
			//$this->errorlog("NONE", "Receive", "84728", "Invalid status value", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		}

		return $history;
	}

	protected function validateProject($snapshot, $params) {
		$data = Logic_Project::getProjectDataById($this->slave_db, $snapshot['pid']);

		if($data === false) {
			LogManager::debug("[ERROR] code=83730, params=".json_encode($params));
			//$this->errorlog($snapshot['pid'], 'Receive', "83730", "Project is not found", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		}

		// verify status of project (0: Pending, 1: Active, 2: Closed)
		if($data['status'] != 1) {
			LogManager::debug("[ERROR] code=83731, params=".json_encode($params));
			//$this->errorlog($snapshot['pid'], 'Receive', "83731", "Project unactive ", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		}
	}

	protected function jumpToPartnerRedirectLink($snapshot, $history, $params) {

		LogManager::debug($history);


		$partner = Logic_Partner::getPartnerDataById($this->slave_db, $snapshot['partner_id']);

		if($partner === false) {
			LogManager::debug("[ERROR] code=83733, params=".json_encode($params));
			//$this->errorlog($snapshot['pid'], 'Receive', "83733", "Partner is not found", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		}

		$url = "";
		switch ($params['status']) {
			case 'c':
				$url = $this->generateURL($partner['complate_url'], array($params['esid']));
				break;
			case 's':
				$url = $this->generateURL($partner['screenout_url'], array($params['esid']));
				break;
			case 'q':
				$url = $this->generateURL($partner['quotafull_url'], array($params['esid']));
				break;
		}

		if($history['progress'] == 0) {
			if($partner['status'] == 1) {
				LogManager::debug("[ERROR] code=83734, params=".json_encode($params));
				//$this->errorlog($snapshot['pid'], 'Receive', "83734", "Partner unactive", json_encode($params));
				$this->jumpToPage(Env::APP_URL.'api/not_supported/');
			}

			if(Logic_LinkHistory::changeProgressById($this->master_db, $history['id'], $this->receive_progress_map[$params['status']])) {
				Logic_Stat::recordStatData($this->master_db, $params['status'], $snapshot);
				Logic_Log::accesslog($this->log_db, $snapshot['accesskey'], $this->receive_progress_map[$params['status']], $params, $this->ip_address);
			}
		}

		// go redirect link of partner
		/*
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$response = curl_exec($ch);
		LogManager::debug($response);
		//$ret = json_decode($response, true, 512, JSON_BIGINT_AS_STRING);
			
		$info = curl_getinfo($ch);
		
		curl_close($ch);
		*/

		LogManager::debug("partner redirect url is " . $url);
		$this->jumpToPage($url);
	}

	private function errorlog($project_id, $kind, $code, $message, $data) {
		$data = array(
			'pid' 		=> $project_id,
			'kind' 		=> $kind,
			'code' 		=> $code,
			'message' 	=> $message,
			'data' 		=> $data
		);

		Logic_Log::errorlog($this->log_db, $data);
	}

	private function jumpToPage($url) {
		$this->redirect_url = $url;
		$this->redirect();
		exit();
	}

	private function generateURL($url, $param=array()){
		$index=1;
		foreach ($param as $str) {
			$url = str_replace('{'.($index++).'}', $str, $url);
		}
		return $url;
	}
}