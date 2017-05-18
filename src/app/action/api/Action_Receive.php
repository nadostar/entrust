<?php

require_once __DIR__.'/../_Action_Api.php';

/**
* 
*/
class Action_Receive extends _Action_Api {
	protected $error_msg = '';

	protected $receive_status_map = array(
		'complete' 	=> 1,
		'screenout' => 2,
		'quotafull' => 3
	);

	protected function initialize() {
		parent::initialize();
	}

	protected function preAction() {
		parent::preAction();

		try {
			$this->registValidatorMap('rs');
			$this->registValidatorMap('esid');

			$this->validParam();
		} catch (Exception $e) {
			LogManager::debug($e->getMessage());
			return;
		}
	}

	protected function doAction() {
		$status 	= trim($this->getQuery('rs'));
		$accessid 	= trim($this->getQuery('esid'));

		$secret = base64_decode($accessid);

		$this->validateParameters($secret, $status);

		$params = array(
			'accessid' => $accessid,
			'accesskey' => substr($secret, 0, Env::ACCESSKEY_SIZE),
			'status' => $status,
			'esid' => substr($secret, Env::ACCESSKEY_SIZE),
			'ip' => $this->ip_address
		);

		$history = $this->findHistory($params);
		$snapshot = $this->findAccesskey($params);

		$this->logicProc($snapshot, $history, $params);
	}

	protected function validateParameters($secret, $status) {
		if(empty($secret)) {
			LogManager::debug("[ERROR] code=45559");
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}

		$accesskey = substr($secret, 0, Env::ACCESSKEY_SIZE);
		if(empty($accesskey)) {
			LogManager::debug("[ERROR] code=45560");
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}

		if(strlen($accesskey) != Env::ACCESSKEY_SIZE) {
			LogManager::debug("[ERROR] code=45561");
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}

		$esid = substr($secret, Env::ACCESSKEY_SIZE);
		if(empty($esid)) {
			LogManager::debug("[ERROR] code=45562");
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}

		if(empty($status)) {
			LogManager::debug("[ERROR] code=45563");
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}
		
		try {
			$this->receive_status_map[$status];
		} catch(Exception $e) {
			LogManager::debug("[ERROR] code=45564");
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}
	}

	protected function findHistory($params) {
		$history = Logic_Live::findHistoryById($this->slave_db, $params['accessid']);

		if($history === false) {
			LogManager::debug("[ERROR] code=54728, params=".json_encode($params));
			//$this->errorlog("NONE", "Receive", "84728", "Invalid status value", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		}

		return $history;
	}

	protected function findAccesskey($params) {
		$data = Logic_Live::findSnapshotByAccesskey($this->slave_db, $params['accesskey']);

		if($data === false) {
			LogManager::debug("[ERROR] code=53729, params=".json_encode($params));
		} else {
			$data['extra'] = json_decode($data['extra'], true);
		}

		return $data;
	}

	protected function logicProc($snapshot, $history, $params) {
		
		$project = Logic_Live::findProjectById($this->slave_db, $snapshot['pid']);
		$partner = $snapshot['extra']['partner'];

		if($project === false) {
			LogManager::debug("[ERROR] code=53730, params=".json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		}

		// 프로젝트 유효성 체크
		if($project['status'] != 1) {
			LogManager::debug("[ERROR] code=53731, params=".json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		}
		
		// 파트너 상태 유효성 체크
		if($partner['status'] == 1) {
			LogManager::debug("[ERROR] code=53732, params=".json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		}

		$stat = Logic_Live::findStatisticsById($this->slave_db, $snapshot);
		if($partner['sample'] > 0) { 
			if($stat['complate_count'] >= $partner['sample']) {
				LogManager::debug("[ERROR] code=53733, params=".json_encode($params));
				$this->jumpToPage(Env::APP_URL.'api/not_supported/');
			}
		}

		if($stat['complate_total'] >= $project['sample']) {
			LogManager::debug("[ERROR] code=53734, params=".json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		}

		$url = "";
		switch ($params['status']) {
			case 'complete':
				$url = $this->makeURL($partner['complate_url'], array($params['esid']));
				break;
			case 'screenout':
				$url = $this->makeURL($partner['screenout_url'], array($params['esid']));
				break;
			case 'quotafull':
				$url = $this->makeURL($partner['quotafull_url'], array($params['esid']));
				break;
		}

		if($history['progress'] == 0) {
			
			if(Logic_Live::changeProgressOfLink($this->master_db, $params['accessid'], $this->receive_status_map[$params['status']])) {
				Logic_Live::statisticsCounting($this->master_db, $params['status'], $snapshot);

				if($params['status'] == 'complete') {
					if($partner['sample'] > 0) {
						$diff = intval($partner['sample']) - intval($stat['complate_count'] + 1);
						if($diff <= 0) {
							$extra = $snapshot['extra'];
							$extra['partner']['status'] = 1;	// 파트너 상태 종료 처리
							
							Logic_Live::changeSnapshotExtra($this->master_db, $snapshot['accesskey'], $extra);
							Logic_Live::closeStatusOfPartner($this->master_db, $snapshot['partner_id']);
						}
					}

					$diff = intval($project['sample']) - intval($stat['complate_total'] + 1);
					if($diff <= 0) {
						Logic_Live::closeStatusOfProject($this->master_db, $snapshot['pid']);
					}
				}

				Logic_Log::accesslog($this->log_db, $snapshot['accesskey'], $this->receive_status_map[$params['status']], $params, $this->ip_address);
				
			}
		}

		$this->jumpToPage($url);
	}

	private function jumpToPage($url) {
		$this->redirect_url = $url;
		$this->redirect();
		exit();
	}

	private function makeURL($url, $param=array()){
		$index=1;
		foreach ($param as $str) {
			$url = str_replace('{'.($index++).'}', $str, $url);
		}
		return $url;
	}
}