<?php

/**
* 
*/
class Action_Partner extends _Action_Support {
	
	protected $menu_allow_no = MenuData::SURVEY_PARTNER_ID;
	
	protected $error_msg = '';

	private $action = null;

	protected function initialize() {
		parent::initialize();

		$this->registValidatorMap('m');
	}

	protected function preAction() {
		parent::preAction();

		$this->action = $this->getQuery('m');


		if(!empty($this->action)) {
			$this->registValidatorMap('search');
			
			$this->registValidatorMap('page');
			
			$this->registValidatorMap('id');
			$this->registValidatorMap('pid');
			$this->registValidatorMap('pids');
			$this->registValidatorMap('link_id',		'Validator_Input', 'Link is required.');

			$this->registValidatorMap('name', 			'Validator_Input', 'Name is required.');
			$this->registValidatorMap('country',		'Validator_Input', 'Country is required.');
			$this->registValidatorMap('complate', 		'Validator_Input', 'Complate URL is required.');
			$this->registValidatorMap('screenout', 		'Validator_Input', 'Screen out URL is required.');
			$this->registValidatorMap('quotafull', 		'Validator_Input', 'Quotafull URL is required.');
			$this->registValidatorMap('sample_size', 	'Validator_Input', 'Sample size is required.');
			$this->registValidatorMap('hits_limit', 	'Validator_Input', 'Hits limit is required.');
			
			$this->registValidatorMap('status');
		}

		try {
			$this->validParam();
		} catch (Exception $e) {
			$this->error_msg = $e->getMessage();
			return;
		}
	}

	protected function doAction() {

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
			case 'toggle':
				$this->toggle();
				break;
			case 'accesskey':
				$this->accesskey();
				break;
			case 'show':
				$this->show();
				break;
			case 'ajaxLink':
				$this->ajaxLink();
				break;
			default:
				$target = array(
					'tree' => 'Survey',
					'menu' => $this->menu_allow_no
				);
				
				$this->output->assign('target', $target);
				
				$project_data = Logic_Project::getProjectDataMap($this->slave_db);
				$this->output->assign('project_data', $project_data);

				$this->output->setTmpl('support/partner.php');
				break;
		}
	}

	private function search() {
		$pid = $this->getQuery('search');

		$page = $this->getQuery('page');
		empty($page) ? $page = 1 : '';

		$pager = new SimplePager($page, Env::PAGE_LIST);

		$data = Logic_Partner::getPartnerDataLimited($this->slave_db, $pid, $pager->limit(), $pager->offset());

		if($data['list']) {
			foreach ($data['list'] as $idx => $row) {
				$snapshot = array(
					'pid' 			=> $row['pid'],
					'link_id'		=> $row['link_id'],
					'partner_id' 	=> $row['id']
				);

				$statdata = Logic_Stat::getStatDataByIds($this->slave_db, $snapshot);

				if(!empty($statdata)) {
					$data['list'][$idx]['c'] = $statdata['complate_count'];
					$data['list'][$idx]['s'] = $statdata['screenout_count'];
					$data['list'][$idx]['q'] = $statdata['quotafull_count'];

					$ir_q = 0;
					try {
						$ir_q = intval($statdata['complate_count']) / (intval($statdata['complate_count']) + intval($statdata['screenout_count'])) * 100;
					} catch (Exception $e) {
						$ir_q = 0;
					}

					$data['list'][$idx]['ir_q'] = sprintf("%.2f", $ir_q); 
				} else {
					$data['list'][$idx]['c'] = 0;
					$data['list'][$idx]['s'] = 0;
					$data['list'][$idx]['q'] = 0;
					$data['list'][$idx]['ir_q'] = 0;
				}
			}
		}

		$this->output->assign('data', $data['list']);

		$pager->setPager($data['count'], self::PAGER_ARM_LENGTH);
		
		$params = array(
			'search' => $pid
		);

		$this->output->assign('pager', $pager->output($params));

		$this->output->setTmpl('support/_partner_list.php');
	}

	private function saveChanges() {
		$category = null;

		$id = $this->getQuery('id');
		$pid = empty($this->getQuery('pids')) ? $this->getQuery('pid') : $this->getQuery('pids');

		if(empty($pid)) {
			$this->error_msg = "Project is required.";
		}

		$params = array(
			'name' 			=> $this->getQuery('name'),
			'country' 		=> $this->getQuery('country'),
			'complate_url' 	=> $this->getQuery('complate'),
			'screenout_url' => $this->getQuery('screenout'),
			'quotafull_url' => $this->getQuery('quotafull'),
			'sample_size' 	=> $this->getQuery('sample_size'),
			'hits_limit' 	=> $this->getQuery('hits_limit'),
			'link_id' 		=> $this->getQuery('link_id'),
			'pid' 			=> $pid,
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

				$snapshot = Logic_Snapshot::getSnapshotDataByPartnerId($this->slave_db, $id);

				if(!empty($snapshot)) {
					$extra = json_decode($snapshot['extra'], true);
					$extra['partner']['sample'] = $params['sample_size'];
					$extra['partner']['hits'] = $params['hits_limit'];
					$extra['partner']['complate_url'] = $params['complate_url'];
					$extra['partner']['screenout_url'] = $params['screenout_url'];
					$extra['partner']['quotafull_url'] = $params['quotafull_url'];

					Logic_Snapshot::updateSnapshotExtra($this->master_db, $snapshot['accesskey'], $extra);
				}
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
			'hits_limit' 	=> '0',
			'link_id' 		=> '',
			'pid' 			=> '',
		);

		$link_data = array();

		if(!empty($id)) {
			$partner = Logic_Partner::getPartnerDataById($this->slave_db, $id);	
			$link_data = Logic_Link::getLinkDataByProjectId($this->slave_db, $partner['pid']);
		}

		$project_data = Logic_Project::getProjectDataMap($this->slave_db);
		
		$this->output->assign('project_data', $project_data);
		$this->output->assign('link_data', $link_data);
		$this->output->assign('partner', $partner);

		$this->output->setTmpl('support/_partner_viewer.php');		
	}

	private function toggle(){
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

		LogManager::debug("status of partner is " . $status);

		if(Logic_Partner::changePartnerStatus($this->master_db, $id, $status)) {
			$snapshot = Logic_Snapshot::getSnapshotDataByPartnerId($this->slave_db, $id);

			if(!empty($snapshot)) {
				$extra = json_decode($snapshot['extra'], true);
				$extra['partner']['status'] = $status;

				Logic_Snapshot::updateSnapshotExtra($this->master_db, $snapshot['accesskey'], $extra);
			}

		} else {
			$result_map['status'] = false;
			$result_map['message'] = 'transaction fail!';
		}

		$this->sendJsonResult($result_map);
	}

	private function accesskey() {
		$id = $this->getQuery('id');

		$result_map = array('status' => true, 'message' => 'Generate accesskey success!');

		$accesskey = Util_GenerateId::generateId(Env::ACCESSKEY_SIZE);

		$partner = Logic_Partner::getPartnerDataById($this->slave_db, $id);
		$project = Logic_Project::getProjectDataById($this->slave_db, $partner['pid']);
			
		$extradata = array(
			/*
			'project' => array(
				'status' 	=> $project['status'], // 1: active, 2: closed
				'sample' 	=> $project['sample'],
				'start_at' 	=> $project['start_at'],
				'end_at' 	=> $project['end_at'],
				'ip_access' => $project['ip_access'],
			),
			*/
			'partner' => array(
				'status' => 0, // 0: active, 1: closed
				'sample' => $partner['sample_size'],
				'hits' => $partner['hits_limit'],
				'complate_url' => $partner['complate_url'],
				'screenout_url' => $partner['screenout_url'],
				'quotafull_url' => $partner['quotafull_url'],
			)
		);

		$data = array(
			'accesskey' 	=> $accesskey,
			'pid' 			=> $partner['pid'],
			'link_id' 		=> $partner['link_id'],
			'partner_id' 	=> $partner['id'],
			'extra'			=> json_encode($extradata)
		);
	
		if(Logic_Snapshot::insertSnapshotData($this->master_db, $data)) {
			Logic_Project::changeProjectStatus($this->master_db, $data['pid'], $active = 1);

			$statdata = array('pid' => $data['pid'], 'link_id' => $data['link_id'], 'partner_id' 	=> $data['partner_id']);
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
		
		$surveylink = Env::SURVEY_URL;
		$joinin_url = str_replace('{accesskey}', $snapshot['accesskey'], $surveylink);

		$receivelink = Env::RECEIVE_URL;
		$complate_url = str_replace('{receive}', 'complete', $receivelink);
		$screenout_url = str_replace('{receive}', 'screenout', $receivelink);
		$quotafull_url = str_replace('{receive}', 'quotafull', $receivelink);

		$data = array(
			'joinin_url' => $joinin_url,
			'complate_url' => $complate_url,
			'screenout_url' => $screenout_url,
			'quotafull_url' => $quotafull_url
		);

		$this->sendJsonResult($data);
	}

	private function ajaxLink() {
		$pid = $this->getQuery('pid');

		$link_data = Logic_Link::getLinkDataByProjectId($this->slave_db, $pid);

		$data = array(
			'list' => $link_data
		);

		$this->sendJsonResult($data);
	}
}