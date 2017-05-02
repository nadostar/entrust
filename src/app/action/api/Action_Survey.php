<?php

require_once __DIR__.'/../_Action_Api.php';

/**
* 
*/
class Action_Survey extends _Action_Api {

	# error
	# Invalid access key (r) 65560
	# Invalid unique ID (uid) 65561
	# Failed to access key  73729
	# Not supported survey  73730
	# Not supported survey  73731
	# Failed to already IP {} address 73732
	# Not supported link 73733

	protected function initialize() {
		parent::initialize();
	}

	protected function preAction() {
		parent::preAction();

		try {
			$this->registValidatorMap('r');
			$this->registValidatorMap('uid');
			
			$this->validParam();
		} catch (Exception $e) {
			LogManager::debug($e->getMessage());
			return;
		}
	}

	protected function doAction() {
		$error = array();

		$r = $this->getQuery('r');
		$uid = $this->getQuery('uid');

		$params = array(
			"r" => $r,
			"uid" => $uid,
			"ip" => $this->ip_address
		);
		// 설문 조사 파라미터 체킹...
		$this->parameterValidate($params);
		
		// accesskey 유효성 체크 및 데이터 리턴 
		$accesskey = $this->accesskeyValidate($params);

		// project 유효성 체크
		$this->projectValidate($params, $accesskey);
		
		// jump to 설문조사 링크로 ...
		$this->jumpToSurveyLinkProc($params, $accesskey);
	}

	private function generateURL($url, $param=array()){
		$index=1;
		foreach ($param as $str) {
			LogManager::debug($str);
			LogManager::debug($url);
			$url = str_replace('{'.($index++).'}', $str, $url);
		}
		return $url;
	}

	private function parameterValidate($params) {
		$accesskey 	= trim($params['r']);
		$uid 		= trim($params['uid']);

		if(empty($accesskey)) {
			$errordata = array(
				"pid" => "NONE",
				"kind" => "Survey",
				"code" => "65560",
				"message" => "Invalid accesskey",
				"data" => json_encode($params)
			);

			$this->errorlog($errordata);
			exit();
		}

		if(strlen($accesskey) != 11) {
			$errordata = array(
				"pid" => "NONE",
				"kind" => "Survey",
				"code" => "65560",
				"message" => "Invalid accesskey",
				"data" => json_encode($params)
			);

			$this->errorlog($errordata);
			exit();
		}

		if(empty($uid)) {
			$errordata = array(
				"pid" => "NONE",
				"kind" => "Survey",
				"code" => "65561",
				"message" => "Invalid unique id",
				"data" => json_encode($params)
			);

			$this->errorlog($errordata);
			exit();
		}
	}

	private function accesskeyValidate($params) {
		$accesskey = trim($params['r']);

		$data = Logic_AccessKeys::getAccessKeyDataByKey($this->slave_db, $accesskey);

		if($data === false) {
			$errordata = array(
				"pid" => "NONE",
				"kind" => "Survey",
				"code" => "73729",
				"message" => "Failed to accesskey",
				"data" => json_encode($params)
			);

			$this->errorlog($errordata);

			$this->jumpToPage('');
		}

		return $data;
	}

	private function projectValidate($params, $accesskey) {
		// Get project data by accesskey
		$data = Logic_Project::getProjectDataById($this->slave_db, $accesskey['pid']);

		// verify the project
		if($data === false) {

			$errordata = array(
				"pid" => $accesskey['pid'],
				"kind" => "Survey",
				"code" => "73730",
				"message" => "Not supported survey (no project)",
				"data" => json_encode($params)
			);

			$this->errorlog($errordata);

			$this->jumpToPage('');
		}

		// verify project status (0: Pending, 1: Active, 2: Closed)
		if($data['disable'] != 1) {
			$errordata = array(
				"pid" => $accesskey['pid'],
				"kind" => "Survey",
				"code" => "73731",
				"message" => "Not supported survey (not active)",
				"data" => json_encode($params)
			);

			$this->errorlog($errordata);

			$this->jumpToPage('');
		}

		// verify survey ip
		if($data['ip_access']) {
			$block = Logic_Block::findBlockDataByIp($this->slave_db, $accesskey['pid'], $params['ip']);

			if($block['found'] > 0) {

				// setting record error log limit
				$limit = 2;
				if($block['found'] < $limit) {

					$errordata = array(
						"pid" => $accesskey['pid'],
						"kind" => "Survey",
						"code" => "73732",
						"message" => "Failed to already IP (".$params['ip'].") address.",
						"data" => json_encode($params)
					);

					$this->errorlog($errordata);
				}

				$this->jumpToPage('');
			} else {
				$blockdata = array(
					"pid" => $accesskey["pid"],
					"ip_address" => $params['ip'],
					"data" => json_encode($params)
				);

				Logic_Block::insertBlockData($this->master_db, $blockdata);
			}
		}
	}

	private function jumpToSurveyLinkProc($params, $accesskey) {
		$data = Logic_Link::getLinkDataByAccessKey($this->slave_db, $accesskey);

		if($data === false) {
			$errordata = array(
				"pid" => $accesskey['pid'],
				"kind" => "Survey",
				"code" => "73733",
				"message" => "Not supported survey link",
				"data" => json_encode($params)
			);

			$this->errorlog($errordata);
			
			$this->jumpToPage('');
		}

		// the partner status closed
		if($data['status']) {
			$this->jumpToPage('');
		}

		$limit = Logic_LinkHistory::getLinkHistoryByAccessKey($this->slave_db, $params['r']);

		$joinin = intval($limit['joinin']);
		$limit = intval($data['request_limit']);

		if($joinin >= $limit) {
			// $errordata = array(
			// 	"pid" => $accesskey['pid'],
			// 	"kind" => "Survey",
			// 	"code" => "73733",
			// 	"message" => "Not supported survey link",
			// 	"data" => json_encode($params)
			// );

			// $this->errorlog($errordata);
			$this->jumpToPage('');
		}
		
		$history = Logic_LinkHistory::getLinkHistoryById($this->slave_db, $params['r'], $params['uid']);

		// record link history
		if($history === false) {
			$historydata = array(
				"accesskey" => $params['r'],
				"uid" => $params['uid']
			);

			Logic_LinkHistory::insertLinkHistoryData($this->master_db, $historydata);

			Logic_Log::accesslog($this->log_db, $params['r'], 0, $params, $this->ip_address);
		}

		$data['kind'] = "Survey";
		$data['redirect'] = $this->generateURL($data['url'], array($params['uid']));

		$this->jumpToPage($data['redirect']);
	}

	private function errorlog($data) {
		Logic_Log::errorlog($this->log_db, $data);
	}

	private function jumpToPage($url, $message = '') {
		$this->redirect_url = $url;
		$this->redirect();
		exit();
	}
}