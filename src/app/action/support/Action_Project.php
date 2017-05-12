<?php

/**
* 
*/
class Action_Project extends _Action_Support {
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

		if(!empty($this->action)) {
			$this->registValidatorMap('search');
			$this->registValidatorMap('page');
			
			$this->registValidatorMap('id');

			$this->registValidatorMap('name', 		'Validator_Input', 			'Project Name is required.');
			$this->registValidatorMap('client', 	'Validator_Input', 			'Client is required.');
			$this->registValidatorMap('country',	 'Validator_Input', 		'Country is required.');
			$this->registValidatorMap('sales', 		'Validator_Input', 			'Sales is required.');
			$this->registValidatorMap('type');
			$this->registValidatorMap('ip_access');
			$this->registValidatorMap('ir', 		'Validator_PositiveNumber', 'IR is required.');
			$this->registValidatorMap('cpi', 		'Validator_Input', 			'CPI($) is required.');
			$this->registValidatorMap('sample', 	'Validator_PositiveNumber', 'Sample is required.');
			//$this->registValidatorMap('free', 		'Validator_PositiveNumber', 'Free is required.');
			$this->registValidatorMap('free');
			$this->registValidatorMap('start_at', 	'Validator_Input', 			'Start Date is required.');
			$this->registValidatorMap('end_at', 	'Validator_Input', 			'End Date is required.');
			$this->registValidatorMap('invoice');
			$this->registValidatorMap('payment');
			$this->registValidatorMap('status');

			$this->registValidatorMap('pid');
		}

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
			default:
				$target = array(
					'tree' => 'Survey',
					'menu' => $this->menu_allow_no
				);
				
				$this->output->assign('target', $target);

				$this->output->setTmpl('support/project.php');
				break;
		}
	}

	private function search() {
		$pid 	= $this->getQuery('pid');
		$status = $this->getQuery('status');
		$sales 	= $this->getQuery('sales');

		$page = $this->getQuery('page');
		empty($page) ? $page = 1 : '';

		$pager = new SimplePager($page, Env::PAGE_LIST);

		$data = Logic_Project::getProjectDataLimited($this->slave_db, $pid, $status, $sales, $pager->limit(), $pager->offset());

		if($data['list']) {
			foreach ($data['list'] as $idx => $row) {
				$data['list'][$idx]['join_in_size'] = 0;

				$statdata = Logic_Stat::getStatDataByPid($this->slave_db, $row['id']);
				
				if(!empty($statdata['pid'])) {
					$data['list'][$idx]['c'] = $statdata['c'];
					$data['list'][$idx]['s'] = $statdata['s'];
					$data['list'][$idx]['q'] = $statdata['q'];
					
					$ir_q = 0;
					try {
						$ir_q = intval($statdata['c']) / (intval($statdata['c']) + intval($statdata['s'])) * 100;
					} catch (Exception $e) {
						$ir_q = 0;
					}

					$data['list'][$idx]['ir_q'] = $ir_q;
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
			'pid' => trim($pid),
			'status' => trim($status),
			'sales' => trim($sales),
		);

		LogManager::debug($pager->output($params));
		$this->output->assign('pager', $pager->output($params));

		$this->output->setTmpl('support/_project_list.php');
	}

	private function saveChanges() {
		$category = null;

		$id = $this->getQuery('id');

		$params = array(
			'name' 		=> $this->getQuery('name'),
			'client' 	=> $this->getQuery('client'),
			'country' 	=> $this->getQuery('country'),
			'sales' 	=> $this->getQuery('sales'),
			'type' 		=> $this->getQuery('type'),
			'sample' 	=> $this->getQuery('sample'),
			'free' 		=> empty($this->getQuery('free'))? 0 : $this->getQuery('free'),
			'ir' 		=> $this->getQuery('ir'),
			'cpi' 		=> $this->getQuery('cpi'),
			'ip_access' => $this->getQuery('ip_access'),
			'invoice' 	=> $this->getQuery('invoice'),
			'payment' 	=> $this->getQuery('payment'),
			'start_at' 	=> $this->getQuery('start_at'),
			'end_at' 	=> $this->getQuery('end_at'),
		);

		$result_map = array('status' => true, 'message' => 'The data has been save changed!');

		if(strlen($this->error_msg) > 0) {
			$result_map['status'] = false;
			$result_map['message'] = $this->error_msg;
		} else {
			$result = false;

			if(empty($id)) {
				$category = Category::PROJECT_NEW;

				$params['id'] = Logic_Generate::generateId($this->slave_db, 'project');
				$result = Logic_Project::insertProjectData($this->master_db, $params);
			} else {
				$category = Category::PROJECT_CHANGE;

				$params['id'] = $id;
				$result = Logic_Project::updateProjectData($this->master_db, $params);
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

		$project = array(
			'id' 		=> '',
			'name' 		=> '',
			'client' 	=> '',
			'country' 	=> '',
			'sales' 	=> '',
			'type' 		=> '0',
			'sample' 	=> '',
			'free' 		=> '',
			'ir' 		=> '',
			'cpi' 		=> '',
			'ip_access' => '0',
			'invoice' 	=> '0',
			'payment' 	=> '0',
			'start_at' 	=> '',
			'end_at' 	=> '',
		);

		if(!empty($id)) {
			$project = Logic_Project::getProjectDataById($this->slave_db, $id);
		}

		$this->output->assign('project', $project);

		$this->output->setTmpl('support/_project_viewer.php');
	}

	private function control() {
		$id = $this->getQuery('id');
		
		switch ($this->getQuery('status')) {
			case '1':
				$status = 2;
				break;
			case '2':
				$status = 1;
				break;
		}

		$result_map = array('status' => true, 'message' => 'The data has been save changed!');

		if(!Logic_Project::changeProjectStatus($this->master_db, $id, $status)) {
			$result_map['status'] = false;
			$result_map['message'] = 'transaction fail!';
		}

		$this->sendJsonResult($result_map);
	}
}