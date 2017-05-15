<?php

/**
* 
*/
class Action_SettingLink extends _Action_Support {
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
		$this->registValidatorMap('pid', 		'Validator_Input', 'Project is required.');
		$this->registValidatorMap('name', 		'Validator_Input', 'Link name is required.');
		$this->registValidatorMap('type',		'Validator_Input', 'Link type is required.');
		$this->registValidatorMap('url');	
		$this->registValidatorMap('attachment');

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
			case 'usefulLinks':
				$this->usefulLinks();
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
			$params['id'] = Logic_Generate::generateId($this->slave_db, 'link');
		} else {
			$params['id'] = Logic_Generate::generateId($this->slave_db, 'link', 'M');
		}

		if(strlen($this->error_msg) > 0) {
			$result_map['status'] = false;
			$result_map['message'] = $this->error_msg;
		} else {
			$result = false;

			if(empty($id)) {
				$result = Logic_Link::insertLinkData($this->master_db, $params);
			} else {
				unset($params['id']); // remove id
				$result = Logic_Link::updateLinkData($this->master_db, $id, $params);
				$params['id'] = $id;
			}

			$useful_link_data = array(
				'link_id' => $params['id'],
				'link_no' => 0, // this is single type
			);

			if($params['type'] == 0) {
				$useful_link_data['url'] = array(trim($this->getQuery('url')));
			} else {
				$file_name = $_FILES['attachment']['name'];
				$tmp_file_name = $_FILES['attachment']['tmp_name'];
				$info = pathinfo($file_name);
				$ext = strtolower($info['extension']);
				
				$rf = fopen($tmp_file_name, "r");
				$frd = fread($rf, filesize($tmp_file_name));
				$url_array = explode("\n", $frd);
							
				fclose($rf);
				
				$useful_link_data['url'] = $url_array;
			}

			$max_link_no = Logic_Link::getUsefulLinkMaxNo($this->slave_db, $params['id']);
						
			if(!empty($max_link_no['no'])) {
				$useful_link_data['link_no'] = $max_link_no['no'];
			}
			
			// single 의 경우 데이터 수정 처리;
			// multi 의 경우 신규 데이터 처리;
			if($params['type'] == 0) {
				if(!empty($id)) {
					if(!Logic_Link::updateSingleUsefulLinkData($this->master_db, $useful_link_data['link_id'], 1, $useful_link_data['url'][0])) {
						$result_map['status'] = false;
						$result_map['message'] = 'transaction fail!';
					}
				} else {
					if(!Logic_Link::insertUsefulLinkData($this->master_db, $useful_link_data)) {
						$result_map['status'] = false;
						$result_map['message'] = 'transaction fail!';
					}
				}
			} else {
				if(!Logic_Link::insertUsefulLinkData($this->master_db, $useful_link_data)) {
					$result_map['status'] = false;
					$result_map['message'] = 'transaction fail!';
				}
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


			if($link['type'] == 0) {
				$usefuldata = Logic_Link::getSingleLinkData($this->slave_db, $id);
				$link['url'] = $usefuldata['url'];
			}
		}

		$project_data = Logic_Project::getProjectDataMap($this->slave_db);
		
		$this->output->assign('project_data', $project_data);
		$this->output->assign('link', $link);

		$this->output->setTmpl('support/_setting_link_viewer.php');
	}

	private function usefulLinks() {
		$id = $this->getQuery('id');

		$page = $this->getQuery('page');
		empty($page) ? $page = 1 : '';

		$pager = new SimplePager($page, Env::PAGE_LIST);

		$data = Logic_Link::getMultiLinkDataLimited($this->slave_db, $id, $pager->limit(), $pager->offset());

		$this->output->assign('data', $data['list']);
		$pager->setPager($data['count'], self::PAGER_ARM_LENGTH);

		$params = array(
			'id' => $id
		);

		$this->output->assign('pager', $pager->output($params));

		$this->output->setTmpl('support/_setting_link_usefullink_viewer.php');
	}
}