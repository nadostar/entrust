<?php

require_once __DIR__.'/../_Action_Api.php';

/**
* 
*/
class Action_Survey extends _Action_Api {
	
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
		$error = array();

		$accesskey 	= trim($this->getQuery('r'));
		$esid 		= trim($this->getQuery('esid'));

		$params = array(
			'accesskey' => $accesskey,
			'esid' 		=> $esid,
			'ip' 		=> $this->ip_address
		);

		$this->validateParameters($params);

		$snapshot = $this->findAccesskey($params);

		LogManager::debug($snapshot);

		$this->validateProject($snapshot, $params);

		$this->joinInToSurvey($snapshot, $params);
	}

	protected function validateParameters($params) {
		if(empty($params['accesskey'])) {
			LogManager::debug("[ERROR] code=65560, params=".json_encode($params));
			//$this->errorlog("NONE", "Join In", "65560", "Invalid accesskey", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}

		if(strlen($params['accesskey']) != Env::ACCESSKEY_SIZE) {
			LogManager::debug("[ERROR] code=65561, params=".json_encode($params));
			//$this->errorlog("NONE", "Join In", "65561", "Invalid accesskey size", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}

		if(empty($params['esid'])) {
			LogManager::debug("[ERROR] code=65562, params=".json_encode($params));
			//$this->errorlog("NONE", "Join In", "65562", "Invalid esid", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}
	}

	protected function findAccesskey($params) {
		$data = Logic_Snapshot::getSnapshotDataByAccesskey($this->slave_db, $params['accesskey']);

		if($data === false) {
			LogManager::debug("[ERROR] code=73729, params=".json_encode($params));
			//$this->errorlog("NONE", "Join In", "73729", "Accesskey is not found", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/error/');
		}

		return $data;
	}

	protected function validateProject($snapshot, $params) {
		$data = Logic_Project::getProjectDataById($this->slave_db, $snapshot['pid']);

		if($data === false) {
			LogManager::debug("[ERROR] code=73730, params=".json_encode($params));
			//$this->errorlog($snapshot['pid'], 'Join In', "73730", "Project is not found", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		}

		// verify status of project (0: Pending, 1: Active, 2: Closed)
		if($data['status'] != 1) {
			LogManager::debug("[ERROR] code=73731, params=".json_encode($params));
			//$this->errorlog($snapshot['pid'], 'Join In', "73731", "Project unactive ", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		}

		// verify join in to user ip (0: pass, 1: check)
		if($data['ip_access']) {
			$block = Logic_Block::findBlockDataByIp($this->slave_db, $snapshot['pid'], $params['ip']);

			if($block['found'] > 0) {
				$limit = 2;

				if($block['found'] < $limit) {
					LogManager::debug("[ERROR] code=73732, params=".json_encode($params));
					//$this->errorlog($snapshot['pid'], 'Join In', "73732", "The user(".$params['esid'].") IP(". $params['ip'] .") already join in the survey", json_encode($params));
				}

				$this->jumpToPage(Env::APP_URL.'api/not_supported/');
			} else {
				$blockdata = array(
					'pid' => $snapshot['pid'],
					'ip_address' => $params['ip'],
					'data' => json_encode($params)
				);

				Logic_Block::insertBlockData($this->master_db, $blockdata);
			}
		}
	}

	protected function joinInToSurvey($snapshot, $params) {
		// 파트너 데이터 가져오기 (sample, hits, status)
		$partner = Logic_Partner::getPartnerDataById($this->slave_db, $snapshot['partner_id']);

		// 파트너 데이터 없을 경우 에러 처리
		if($partner === false) {
			LogManager::debug("[ERROR] code=73733, params=".json_encode($params));
			$this->errorlog($snapshot['pid'], 'Join In', "73733", "Partner is not found", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		}

		// 파트너 상태 확인하기
		if($partner['status'] == 1) {
			LogManager::debug("[ERROR] code=73734, params=".json_encode($params));
			$this->errorlog($snapshot['pid'], 'Join In', "73734", "Partner unactive", json_encode($params));
			$this->jumpToPage(Env::APP_URL.'api/not_supported/');
		}

		// 설문조사 참여 히스토리 데이터 가져오기
		$history = Logic_LinkHistory::findLinkHistory($this->slave_db, $snapshot['accesskey'], $params['esid']);

		if($history === false) {
			// 파트너 참여수 제한 체크 0: 무제한, 1: 체크
			if($partner['hits_limit'] > 0) {
				// 실제 참여수 데이터
				$htotal = Logic_LinkHistory::getLinkHisoryTotal($this->slave_db, $snapshot['accesskey']);	

				// 참여수 제한 수가 실제 참여수 보다 클 경우 에러 처리
				if($htotal['found'] > $partner['hits_limit']) {
					LogManager::debug("[ERROR] code=73735, params=".json_encode($params));
					$this->errorlog($snapshot['pid'], 'Join In', "73735", "Hits limit over", json_encode($params));
					$this->jumpToPage(Env::APP_URL.'api/not_supported/');
				}

				// 설정한 참여수 제한 수 와 실제 참여수 같을 경우, 파트너 상태 closed 처리
				if($partner['hits_limit'] == $htotal['found']) {
					Logic_Partner::changePartnerStatus($this->master_db, $partner['id'], $closed = 1);
				}
			}
			
			// Gets some useful survey links
			$usefullink = Logic_Link::findUsefulLink($this->slave_db, $snapshot['accesskey'], $snapshot['link_id']);

			// verify link is empty
			if(count($usefullink) == 0) {
				LogManager::debug("[ERROR] code=73733, params=".json_encode($params));
				$this->errorlog($snapshot['pid'], 'Join In', "73736", "Link is not found", json_encode($params));
				$this->jumpToPage(Env::APP_URL.'api/not_supported/');
			}

			$idx = mt_rand(0, count($usefullink) - 1);
			
			$url = $this->generateURL($usefullink[$idx]['url'], array($params['esid']));
			
			$historydata = array('accesskey' => $snapshot['accesskey'], 'uid' => $params['esid'], 'url' => $url);
			if($usefullink[$idx]['link_type'] == 1) {
				if(Logic_Link::alreadyUseLink($this->master_db, $usefullink[$idx]['link_id'], $usefullink[$idx]['link_no'])) {

					Logic_LinkHistory::insertLinkHistoryData($this->master_db, $historydata);
				}
			} else {
				Logic_LinkHistory::insertLinkHistoryData($this->master_db, $historydata);
			}
			
			Logic_Log::accesslog($this->log_db, $snapshot['accesskey'], 0, $params, $this->ip_address);

			$this->jumpToPage($url);
		} else {
			LogManager::debug("history url");
			$this->jumpToPage($history['url']);
		}
	}

	private function errorlog($project_id, $kind, $code, $message, $data) {
		$data = array(
			'pid' => $project_id,
			'kind' => $kind,
			'code' => $code,
			'message' => $message,
			'data' => $data
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