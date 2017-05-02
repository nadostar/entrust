<?php

require_once __DIR__.'/../_Action_Api.php';

/**
* 
*/
class Action_Receive extends _Action_Api {
	# error
	# Invalid accesskey 	66560
	# Invalid status 		66561
	# Invalid uniqueID 		66562

	# not to join in the survey. 74728

	# Failed to receive (accesskey)	74729
	# Failed to receive (kind)		74730
	# Failed to receive (uniqueID)	74731

	private $receive_progress = array(
		"c" => 1,
		"s" => 2,
		"q" => 3,
	);

	protected function initialize() {
		parent::initialize();
	}

	protected function preAction() {
		parent::preAction();

		try {
			$this->registValidatorMap('r');
			$this->registValidatorMap('k');
			$this->registValidatorMap('uid');

			$this->validParam();
		} catch (Exception $e) {
			LogManager::debug($e->getMessage());
			return;
		}
	}

	protected function doAction() {
		$accesskey 	= $this->getQuery('r');
		$kind 		= $this->getQuery('k');
		$user_id 	= $this->getQuery('uid');

		$params = array(
			"r" => $this->getQuery("r"),
			"k" => $this->getQuery("k"),
			"uid" => $this->getQuery("uid"),
			"ip" => $this->ip_address
		);

		$this->parametersValidate($params);

		$accesskey = $this->accesskeyValidate($params);

		$this->projectValidate($accesskey, $params);

		$this->linkProc($params, $accesskey);

		$this->sendJsonResult($params);
	}

	private function parametersValidate($params) {
		$accesskey 	= trim($params['r']);
		$kind 		= trim($params['k']);
		$uid 		= trim($params['uid']);

		$error = array();

		# Invalid accesskey 	66560
		if(empty($accesskey)) {
			$error["code"] = "66560";
			$error["message"] = "Invalid accesskey.";

			$this->sendJsonResult($error);
			exit();
		}

		# Invalid accesskey 	66560
		if(strlen($accesskey) != 11) {
			$error["code"] = "66560";
			$error["message"] = "Invalid accesskey.";
			$this->sendJsonResult($error);
			exit();
		}

		# Invalid status 		66561
		if(empty($kind)) {
			$error["code"] = "66561";
			$error["message"] = "Invalid kind.";
			$this->sendJsonResult($error);
			exit();
		}

		try {
			$this->receive_progress[$kind];
		} catch (Exception $e) {
			$error["code"] = "66561";
			$error["message"] = "Invalid kind key";
			$this->sendJsonResult($error);
			exit();
		}

		# Invalid uniqueID 		66562
		if(empty($uid)) {
			$error["code"] = "66562";
			$error["message"] = "Invalid uniqueID.";
			$this->sendJsonResult($error);
			exit();
		}

		$history = Logic_LinkHistory::getLinkHistoryById($this->slave_db, $accesskey, $uid);

		# not to join in the survey. 74728
		if($history === false) {
			$error["code"] = "74728";
			$error["message"] = "User (".$uid. ") not to join in the survey.";

			$errordata = array(
				"pid" => "None",
				"kind" => "Receive",
				"code" => "74728",
				"message" => "User (".$uid. ") not to join in the survey.",
				"data" => json_encode($params)
			);

			$this->errorlog($errordata);

			$this->sendJsonResult($error);
			exit();
		}
	}

	private function accesskeyValidate($params) {
		$accesskey = trim($params["r"]);
		$data = Logic_AccessKeys::getAccessKeyDataByKey($this->slave_db, $accesskey);

		$error = array();
		# Failed to access key  73729
		if($data === false) {
			$error["code"] = "73729";
			$error["message"] = "Failed to receive (accesskey).";
	
			$errordata = array(
				"pid" => "None",
				"kind" => "Receive",
				"code" => "74729",
				"message" => "Failed to receive (accesskey).",
				"data" => json_encode($params)
			);

			$this->errorlog($errordata);

			$this->sendJsonResult($error);
			exit();
		}

		return $data;
	}

	private function projectValidate($accesskey, $params) {
		$data = Logic_Project::getProjectDataById($this->slave_db, $accesskey['pid']);

		$error = array();
		if($data === false) {
			$error["code"] = "73730";
			$error["message"] = "Not supported survey";
			$this->sendJsonResult($error);
			exit();
		}

		if($data["disable"] != 1) {
			$error["code"] = "73730";
			$error["message"] = "Not supported survey";
			$this->sendJsonResult($error);
			exit();
		}
	}

	private function linkProc($params, $accesskey) {
		$key = trim($params['r']);
		$kind = trim($params['k']);
		$uid = trim($params['uid']);

		$history = Logic_LinkHistory::getLinkHistoryById($this->slave_db, $key, $uid);

		if($history !== false) {
			if($history['progress'] == 0) {
				if(Logic_LinkHistory::updateLinkHistoryByProgress($this->master_db, $history['id'], $this->receive_progress[$kind])) {
					Logic_Stat::recordStatData($this->master_db, $kind, $accesskey);
				}
			}
		}
	}

	private function errorlog($data) {
		Logic_Log::errorlog($this->log_db, $data);
	}

	private function jumpToPartnerRedirectUrl($url, $message = '') {

	}
}
