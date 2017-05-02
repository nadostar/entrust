<?php

/**
* 
*/
class Action_Admin extends _Action_Support {
	protected $menu_allow_no = MenuData::SYSTEM_ADMIN_ID;

	protected $error_msg = '';

	private $action = null;
	
	const DEFAULT_PASSWORD = "dash@default";
	
	protected function initialize() {
		parent::initialize();
		
		$this->registValidatorMap('m');
	}

	protected function preAction() {
		parent::preAction();

		$this->action = $this->getQuery('m');

		if(!empty($this->action)) {
			$this->registValidatorMap('search');
			$this->registValidatorMap('page', 	'Validator_PositiveNumber');
			
			$this->registValidatorMap('id');
			
			$this->registValidatorMap('email', 	'Validator_Input', 'Emaill is required.');
			$this->registValidatorMap('name', 	'Validator_Input', 'Name is required.');
			$this->registValidatorMap('permission_id');
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
			case 'initPassword':
				$this->initPassword();
				break;
			default:
				$target = array(
					'tree' => 'System',
					'menu' => $this->menu_allow_no
				);
				
				$this->output->assign('target', $target);

				$this->output->setTmpl('support/admin.php');
				break;
		}
	}


	private function search() {
		$search = $this->getQuery('search');

		$page = $this->getQuery('page');
		empty($page) ? $page = 1 : '';

		$pager = new SimplePager($page, Env::PAGE_LIST);

		$data = Logic_Admin::getAdminDataLimited($this->system_db, $search, $pager->limit(), $pager->offset());

		$this->output->assign('data', $data['list']);

		$pager->setPager($data['count'], self::PAGER_ARM_LENGTH);

		$params = array(
			'search' => trim($search)
		);

		LogManager::debug($pager->output($params));
		$this->output->assign('pager', $pager->output($params));

		$this->output->setTmpl('support/_admin_list.php');

	}

	private function saveChanges() {
		$category = null;

		$id = $this->getQuery('id');

		$params = array(
			'id' => $this->getQuery('id'),
			'email' => $this->getQuery('email'),
			'name' => $this->getQuery('name'),
			'password' => $this->cryptPassword(self::DEFAULT_PASSWORD),
			'permission_id' => $this->getQuery('permission_id')
		);

		$result_map = array('status' => true, 'message' => "The data has been save changed!");
		
		if(strlen($this->error_msg) > 0) {
			$result_map['status'] = false;
			$result_map['message'] = $this->error_msg;
		} else {
			$result = false;

			if(empty($id)) {
				$category = Category::ADMIN_NEW;
				
				$params['id'] = Logic_Generate::generateId($this->system_db, 'admin', '', 10000);

				$result = Logic_Admin::insertAdminData($this->system_db, $params);
			} else {
				$category = Category::ADMIN_CHANGE;

				$result = Logic_Admin::updateAdminData($this->system_db, $params);
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

		$admin = array(
			'id' 			=> '',
			'email' 		=> '',
			'name' 			=> '',
			'password' 		=> '',
			'permission_id' => '',
		);

		if(!empty($id)) {
			$admin = Logic_Admin::getAdminDataById($this->system_db, $id);
		}
		$this->output->assign('admin', $admin);
		
		$permissiondata = Logic_Permission::getPermissionDataArray($this->system_db);
		$this->output->assign('permission_data', $permissiondata);

		$this->output->setTmpl('support/_admin_viewer.php');
	}

	private function initPassword() {
		$id = $this->getQuery('id');

		$params = array(
			'id' => $id,
			'password' => $this->cryptPassword(self::DEFAULT_PASSWORD)
		);

		$result_map = array('status' => true, 'message' => "Initialization password success!");

		if(empty($id)) {
			$result_map['status'] = false;
			$result_map['message'] = 'Missing admin id';
		} else {
			if(Logic_Admin::changePassword($this->system_db, $params)) {
				Logic_Log::adminlog($this->log_db, $this->login_session->getAdminId(), Category::ADMIN_INIT_PASSWORD, $params, $this->ip_address);
			} else {
				$result_map['status'] = false;
				$result_map['message'] = 'transaction fail!';
			}
		}
		
		$this->sendJsonResult($result_map);
	}
}