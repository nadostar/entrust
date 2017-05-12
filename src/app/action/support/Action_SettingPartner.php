<?php

/**
* 
*/
class Action_SettingPartner extends _Action_Support {
	protected $menu_allow_no = MenuData::SURVEY_PROJECT_ID;

	protected $error_msg = '';

	private $action = null;
	
	protected function initialize() {
		parent::initialize();

		$this->registValidatorMap('m');
	}

	protected function preAction() {
		parent::preAction();

		$this->action = $this->getQuery('m');

		$this->registValidatorMap('page');
		
		$this->registValidatorMap('id');
		$this->registValidatorMap('pid', 			'Validator_Input', 'Missing pid.');
		$this->registValidatorMap('link_id',		'Validator_Input', 'Link is required.');

		$this->registValidatorMap('name', 			'Validator_Input', 'Partner name is required.');
		$this->registValidatorMap('country',		'Validator_Input', 'Country is required.');
		$this->registValidatorMap('complate', 		'Validator_Input', 'Complate URL is required.');
		$this->registValidatorMap('screenout', 		'Validator_Input', 'Screen out URL is required.');
		$this->registValidatorMap('quotafull', 		'Validator_Input', 'Quotafull URL is required.');
		$this->registValidatorMap('sample_size', 	'Validator_Input', 'Sample size is required.');
		$this->registValidatorMap('hits_limit', 	'Validator_Input', 'Hits limit is required.');

		$this->registValidatorMap('status');

		try {
			$this->validParam();
		} catch (Exception $e) {
			$this->error_msg = $e->getMessage();
			return;
		}
	}

	protected function doAction() {
		LogManager::debug($this->action);

		switch ($this->action) {
			case 'search':
				$this->search();
				break;
			case 'saveChanges':
				$this->saveChanges();
				break;
			case 'viewer':
				$this->viewer();
				break;
			case 'control':
				$this->control();
				break;
			case 'accesskey':
				$this->accesskey();
				break;
			case 'show':
				$this->show();
				break;
			default:
				$target = array(
					'tree' => 'Survey',
					'menu' => $this->menu_allow_no
				);
				
				$this->output->assign('target', $target);
				
				$this->output->assign('pid', $this->getQuery('pid'));
				$this->output->setTmpl('support/setting_partner.php');
				break;
		}
	}

	private function search() {
		$pid = $this->getQuery('pid');

		$page = $this->getQuery('page');
		empty($page) ? $page = 1 : '';

		$pager = new SimplePager($page, Env::PAGE_LIST);

		$data = Logic_Partner::getPartnerDataLimited($this->slave_db, $pid, $pager->limit(), $pager->offset());

		$this->output->assign('data', $data['list']);

		$pager->setPager($data['count'], self::PAGER_ARM_LENGTH);
		
		$params = array(
			'pid' => $pid
		);
		
		LogManager::debug($pager->output($params));

		$this->output->assign('pager', $pager->output($params));

		$this->output->setTmpl('support/_setting_partner_list.php');
	}

	private function saveChanges() {
		$category = null;

		$id = $this->getQuery('id');

		$params = array(
			'name' 			=> $this->getQuery('name'),
			'country' 		=> $this->getQuery('country'),
			'complate_url' 	=> $this->getQuery('complate'),
			'screenout_url' => $this->getQuery('screenout'),
			'quotafull_url' => $this->getQuery('quotafull'),
			'sample_size' 	=> $this->getQuery('sample_size'),
			'hits_limit' 	=> $this->getQuery('hits_limit'),
			'link_id' 		=> $this->getQuery('link_id'),
			'pid' 			=> $this->getQuery('pid'),
		);

		$result_map = array('status' => true, 'message' => 'The data has been save changed!');

		if(strlen($this->error_msg) > 0) {
			$result_map['status'] = false;
			$result_map['message'] = $this->error_msg;
		} else {
			$result = false;

			if(empty($id)) {
				$category = Category::PARTNER_NEW;

				$params['id'] = Logic_Generate::generateId($this->slave_db, 'partner');
				$result = Logic_Partner::insertPartnerData($this->master_db, $params);
			} else {
				$category = Category::PARTNER_CHANGE;

				$params['id'] = $id;
				$result = Logic_Partner::updatePartnerData($this->master_db, $params);
			}

			if($result) {
				Logic_Log::adminlog($this->log_db, $this->login_session->getAdminId(), $category, $params, $this->ip_address);
			} else {
				$result_map['status'] = false;
				$result_map['message'] = 'transaction fail!';
			}
		}

		$this->sendJsonResult($result_map);
	}

	private function viewer() {
		$id = $this->getQuery('id');
		$pid = $this->getQuery('pid');

		$partner = array(
			'id'			=> '',
			'name' 			=> '',
			'country' 		=> '',
			'complate_url' 	=> '',
			'screenout_url' => '',
			'quotafull_url' => '',
			'sample_size' 	=> '0',
			'hits_limit' => '0',
			'link_id' 		=> '',
			'pid' 			=> $pid,
		);

		if(!empty($id)) {
			$partner = Logic_Partner::getPartnerDataById($this->slave_db, $id);	
		}

		$project_data = Logic_Project::getProjectDataMap($this->slave_db);
		$link_data = Logic_Link::getLinkDataByProjectId($this->slave_db, $pid);

		$this->output->assign('project_data', $project_data);
		$this->output->assign('link_data', $link_data);
		$this->output->assign('partner', $partner);

		$this->output->setTmpl('support/_setting_partner_viewer.php');
	}

	private function control(){
		$id = $this->getQuery('id');
		$status = $this->getQuery('status');

		// status 0:active, 1:closed
		// status toggle
		switch ($this->getQuery('status')) {
			case '0':
				$status = 1;
				break;
			case '1':
				$status = 0;
				break;
		}

		$result_map = array('status' => true, 'message' => 'The data has been save changed!');
		if(!Logic_Partner::changePartnerStatus($this->master_db, $id, $status)) {
			$result_map['status'] = false;
			$result_map['message'] = 'transaction fail!';
		}

		$this->sendJsonResult($result_map);
	}

	private function accesskey() {
		$id = $this->getQuery('id');

		$result_map = array('status' => true, 'message' => 'Generate accesskey success!');

		$accesskey = Util_GenerateId::generateId(16);

		$partnerdata = Logic_Partner::getPartnerDataById($this->slave_db, $id);
	
		$data = array(
			'accesskey' => $accesskey,
			'pid' => $partnerdata['pid'],
			'link_id' => $partnerdata['link_id'],
			'partner_id' => $partnerdata['id']
		);
	
		if(Logic_Snapshot::insertSnapshotData($this->master_db, $data)) {
			Logic_Project::changeProjectStatus($this->master_db, $data['pid'], $active = 1);

			$statdata = array(
				'pid' => $data['pid'],
				'link_id' => $data['link_id']
			);

			Logic_Stat::insertStatData($this->master_db, $statdata);
		} else {
			$result_map['status'] = false;
			$result_map['message'] = 'transaction fail!';	
		}

		$this->sendJsonResult($result_map);
	}

	private function show() {
		$id = $this->getQuery('id');

		$snapshot = Logic_Snapshot::getSnapshotDataByPartnerId($this->slave_db, $id);
		
		LogManager::debug($snapshot);

		$surveylink = Env::SURVEY_URL;
		$joinin_url = str_replace('{accesskey}', $snapshot['accesskey'], $surveylink);

		$receivelink = Env::RECEIVE_URL;
		$complate_url = strtr($receivelink, array('{accesskey}' => $snapshot['accesskey'], '{status}' => 'c'));
		$screenout_url = strtr($receivelink, array('{accesskey}' => $snapshot['accesskey'], '{status}' => 's'));
		$quotafull_url = strtr($receivelink, array('{accesskey}' => $snapshot['accesskey'], '{status}' => 'q'));

		$data = array(
			'joinin_url' => $joinin_url,
			'complate_url' => $complate_url,
			'screenout_url' => $screenout_url,
			'quotafull_url' => $quotafull_url
		);

		LogManager::debug($data);

		$this->sendJsonResult($data);
	}
}