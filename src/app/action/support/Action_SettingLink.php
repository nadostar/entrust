<?php

/**
* 
*/
class Action_SettingLink extends _Action_Support {
	protected $menu_allow_no = MenuData::SURVEY_PROJECT_ID;
	
	protected $error_msg = '';

	private $action = null;
	
	private $export_file = "entrust_link_{date}.csv";

	protected function initialize() {
		parent::initialize();

		$this->registValidatorMap('m');
	}

	protected function preAction() {
		parent::preAction();
		
		$this->action = $this->getQuery('m');

		$this->registValidatorMap('page');
		
		$this->registValidatorMap('id');
		$this->registValidatorMap('pid', 		'Validator_Input', 'Project is required.');
		$this->registValidatorMap('name', 		'Validator_Input', 'Link name is required.');
		$this->registValidatorMap('type',		'Validator_Input', 'Link type is required.');
		$this->registValidatorMap('url');	
		$this->registValidatorMap('attachment');

		$this->registValidatorMap('download_id');
		
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
			case 'accesskey':
				$this->accesskey();
				break;
			case 'download':
				$this->download();
				break;
			default:
				$target = array(
					'tree' => 'Survey',
					'menu' => $this->menu_allow_no
				);
				
				$this->output->assign('target', $target);				

				$this->output->assign('pid', $this->getQuery('pid'));
				$this->output->setTmpl('support/setting_link.php');
				break;
		}
	}


	private function search() {
		$pid = $this->getQuery('pid');

		$page = $this->getQuery('page');		
		empty($page) ? $page = 1 : '';

		$pager = new SimplePager($page, Env::PAGE_LIST);

		$data = Logic_Link::getLinkDataLimited($this->slave_db, $pid, $pager->limit(), $pager->offset());

		$this->output->assign('data', $data['list']);

		$pager->setPager($data['count'], self::PAGER_ARM_LENGTH);
		
		$params = array(
			'pid' => $pid
		);
		
		LogManager::debug($pager->output($params));

		$this->output->assign('pager', $pager->output($params));

		$this->output->setTmpl('support/_setting_link_list.php');
	}

	private function saveChanges() {
		$result_map = array('status' => true, 'message' => 'The data has been save changed!');

		$category = null;

		$id = $this->getQuery('id');
		$pid = $this->getQuery('pid');

		$params = array(
			'name' 	=> $this->getQuery('name'),
			'type' 	=> $this->getQuery('type'),
			'pid' 	=> $pid,
		);

		if($params['type'] == 0) {
			$params['k'] = 0;
			$params['url'] = trim($this->getQuery('url'));

			if(empty($params['url'])) {
				$result_map['status'] = false;
				$result_map['message'] = "URL is required.";
				$this->sendJsonResult($result_map);
				exit();
			}
		} else {
			
			if(empty($_FILES['attachment']['name'])) {
				$result_map['status'] = false;
				$result_map['message'] = "File is required.";
				$this->sendJsonResult($result_map);
				exit();
			}

			$file_name = $_FILES['attachment']['name'];
			$tmp_file_name = $_FILES['attachment']['tmp_name'];
			$info = pathinfo($file_name);
			$ext = strtolower($info['extension']);
			
			$rf = fopen($tmp_file_name, "r");
			$frd = fread($rf, filesize($tmp_file_name));
			$url_array = explode("\n", $frd);
						
			fclose($rf);
			
			$params['urls'] = $url_array;
		}

		if(strlen($this->error_msg) > 0) {
			$result_map['status'] = false;
			$result_map['message'] = $this->error_msg;
		} else {
			$result = false;

			if(empty($id)) {
				$category = Category::LINK_NEW;

				if($params['type'] == 0) {
					$params['id'] = Logic_Generate::generateId($this->slave_db, 'link');
				} else {
					$params['id'] = Logic_Generate::generateId($this->slave_db, 'link', 'M');
				}
				
				$result = Logic_Link::insertLinkData($this->master_db, $params);
			} else {
				$category = Category::LINK_CHANGE;

				$params['id'] = $id;
				$result = Logic_Link::updateLinkData($this->master_db, $params);
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

		$link = array(
			'id' 			=> '',
			'name' 			=> '',
			'type' 			=> '0',
			'url' 			=> '',
			'attachment' 	=> '',
			'pid' 			=> $this->getQuery('pid')
		);

		if(!empty($id)) {
			$link = Logic_Link::getLinkDataById($this->slave_db, $id);
		}

		$project_data = Logic_Project::getProjectDataMap($this->slave_db);
		
		$this->output->assign('project_data', $project_data);
		$this->output->assign('link', $link);

		$this->output->setTmpl('support/_setting_link_viewer.php');
	}

	private function accesskey() {
		$id = $this->getQuery('id');
		$pid = null;
		$link_id = null;

		$data = Logic_Link::getLinkDataArrayById($this->slave_db, $id);

		$params = array();
		foreach ($data as $row) {
			$accesskey = Util_GenerateId::generateId(11);
			$params[] = array(
				'access_key' => $accesskey,
				'pid' => $row['pid'],
				'link_id' => $row['id'],
				'link_key' => $row['k']
			);

			$pid = $row['pid'];
			$link_id = $row['id'];
		}

		$result_map = array('status' => true, 'message' => 'Generate accesskey success!');

		if(Logic_AccessKeys::insertAccessKeysData($this->master_db, $params)) {
			Logic_Project::changeProjectStatus($this->master_db, $pid, 1);

			$statdata = array(
				'pid' => $pid,
				'link_id' => $link_id
			);

			Logic_Stat::insertStatData($this->master_db, $statdata);
			
			Logic_Log::adminlog($this->log_db, $this->login_session->getAdminId(), Category::ACCESSKEY_GENERATE, $params, $this->ip_address);
		} else {
			$result_map['status'] = false;
			$result_map['message'] = 'transaction fail!';		
		}
		
		$this->sendJsonResult($result_map);
	}

	private function download() {
		$this->output = new Output_CSV();
		set_time_limit(0);

		$id = $this->getQuery('download_id');

		$date = date('ymdHis');
		$file = str_replace('{date}', $date, $this->export_file);
		$this->output->setExportFileName($file);
		$this->output->realtimeOutput();

		$data = Logic_AccessKeys::getAccessKeyDataByLinkId($this->slave_db, $id);
		
		$url = Env::SURVEY_URL;

		foreach ($data as $row) {
			$tmp['url'] = str_replace('{accesskey}', $row['access_key'], $url);
			LogManager::debug($tmp['url']);
			$this->output->assignRow($tmp['url']);
		}
	}
}