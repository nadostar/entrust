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
		$this->registValidatorMap('request_limit', 	'Validator_Input', 'Request limit is required.');

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
			'request_limit' => $this->getQuery('request_limit'),
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
			'request_limit' => '0',
			'link_id' 		=> '',
			'pid' 			=> $pid,
		);

		if(!empty($id)) {
			$partner = Logic_Partner::getPartnerDataById($this->slave_db, $id);	
		}

		$project_data = Logic_Project::getProjectDataMap($this->slave_db);
		$link_data = Logic_Link::getLinkDataByPid($this->slave_db, $pid);

		$this->output->assign('project_data', $project_data);
		$this->output->assign('link_data', $link_data);
		$this->output->assign('partner', $partner);

		$this->output->setTmpl('support/_setting_partner_viewer.php');
	}
}