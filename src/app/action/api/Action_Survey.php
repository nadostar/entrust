<?php

require_once __DIR__.'/../_Action_Api.php';

/**
* 
*/
class Action_Survey extends _Action_Api {

	protected $error_msg = '';

	protected function initialize() {
		parent::initialize();
	}

	protected function preAction() {
		parent::preAction();

		try {
			$this->registValidatorMap('r');
			$this->registValidatorMap('esid');
			
			$this->validParam();
		} catch (Exception $e) {
			LogManager::debug($e->getMessage());
			return;
		}
	}

	protected function doAction() {
		$accesskey 	= trim($this->getQuery('r'));
		$esid 		= trim($this->getQuery('esid'));

		$this->validateParameters($accesskey, $esid);

		$params = array(
			'accessid'  => base64_encode($accesskey.$esid),
			'accesskey' => $accesskey,
			'esid' 		=> $esid,
			'ip' 		=> $this->ip_address
		);

		$snapshot = $this->findAccesskey($params);

		$this->logicProc($snapshot, $params);
	}

	protected function validateParameters($accesskey, $esid) {
		if(empty($accesskey)) {
			LogManager::debug("[ERROR] code=65560, params=".json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}

		if(empty($accesskey != Env::ACCESSKEY_SIZE)) {
			LogManager::debug("[ERROR] code=65561, params=".json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}

		if(empty($esid)) {
			LogManager::debug("[ERROR] code=65562, params=".json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}
	}

	protected function findAccesskey($params) {
		$data = Logic_Live::findSnapshotByAccesskey($this->slave_db, $params['accesskey']);

		if($data === false) {
			LogManager::debug("[ERROR] code=73729, params=".json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		} else {
			$data['extra'] = json_decode($data['extra'], true);
		}

		return $data;
	}

	protected function logicProc($snapshot, $params) {
		$project = $snapshot['extra']['project'];
		$partner = $snapshot['extra']['partner'];

		// 프로젝트 유효성 체크
		if($project['status'] != 1) {
			LogManager::debug("[ERROR] code=73731, params=".json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		}
		
		// 파트너 상태 유효성 체크
		if($partner['status'] == 1) {
			LogManager::debug("[ERROR] code=73732, params=".json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		}

		// 설문조사 IP 억세스 설정 체크
		if($project['ip_access'] == 1) {
			$block = Logic_Live::findBlockDataByIP($this->slave_db, $snapshot['pid'], $params['ip']);

			if($block['found'] > 0) {
				$limit = 2;

				if($block['found'] < $limit) {
					LogManager::debug("[ERROR] code=73733, params=".json_encode($params));
					$this->jumpToPage(Env::APP_URL.'api/not_supported/');
				}

				// jump to page
			} else {
				$blockdata = array('pid' => $snapshot['pid'], 'ip_address' => $params['ip'], 'data' => json_encode($params));
				Logic_Live::writeBlockOfRemoteAddr($this->master_db, $blockdata);
			}
		}

		// 설문조사 참여 데이터 조회
		$history = Logic_Live::findHistoryById($this->slave_db, $params['accessid']);

		if($history === false) {
			// 파트너 설문조사 참여 제한 수 체크 (0: 무제한, 1: 제한)
			if($partner['hits'] > 0) {
				$history_cnt = Logic_Live::getHistoryCountByAccesskey($this->slave_db, $snapshot['accesskey']);

				if($history_cnt['found'] > $partner['hits']) {
					LogManager::debug("[ERROR] code=73734, params=".json_encode($params));
					$this->jumpToPage(Env::APP_URL.'api/not_supported/');
				}

				if(($history_cnt['found'] + 1) >= $partner['hits']) {
					$extra = $snapshot['extra'];
					$extra['partner']['status'] = 1;	// 파트너 상태 종료 처리
					LogManager::debug($extra);

					Logic_Live::changeSnapshotExtra($this->master_db, $snapshot['accesskey'], $extra);
					Logic_Live::closeStatusOfPartner($this->master_db, $snapshot['partner_id']);
				}
			}

			// Gets some useful survey links
			$usefullink = Logic_Live::findUsefulLinkById($this->slave_db, $snapshot['link_id']);
			if(count($usefullink) == 0) {
				LogManager::debug("[ERROR] code=73735, params=".json_encode($params));
				$this->jumpToPage(Env::APP_URL.'api/not_supported/');
			}
			
			$idx = mt_rand(0, (count($usefullink) - 1));
			
			$url = $this->makeURL($usefullink[$idx]['url'], array($params['accessid']));

			// multi 링크 타입의 경우 하나의 링크 한번만 사용 가능
			if($usefullink[$idx]['type'] == 1) {
				Logic_Live::alreadyUseLink($this->master_db, $usefullink[$idx]['link_id'], $usefullink[$idx]['link_no']);
			}

			$historydata = array(
				'accessid' 	=> $params['accessid'],
				'accesskey' => $params['accesskey'],
				'uid' 		=> $params['esid'],
				'url' 		=> $url
			);
			Logic_Live::writeHistory($this->master_db, $historydata);
			Logic_Log::accesslog($this->log_db, $snapshot['accesskey'], 0, $params, $this->ip_address);

			$this->jumpToPage($url);
		} else {
			// jump to page
			//LogManager::debug("history url is ".$history['url']);
			$this->jumpToPage($history['url']);
		}
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