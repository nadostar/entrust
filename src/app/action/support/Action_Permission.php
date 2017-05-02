<?php

/**
* 
*/
class Action_Permission extends _Action_Support {
	protected $menu_allow_no = MenuData::SYSTEM_PERMISSION_ID;
	
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
			$this->registValidatorMap('page');

			$this->registValidatorMap('id');

			$this->registValidatorMap('name', 'Validator_Input', 'Name is required.');
			$this->registValidatorMap('description');
			$this->registValidatorMap('allow_no_v');
			$this->registValidatorMap('allow_no_m');
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
			case 'viewer':
				$this->viewer();
				break;
			case 'saveChanges':
				$this->saveChanges();
				break;
			default:
				$target = array(
					'tree' => 'System',
					'menu' => $this->menu_allow_no
				);
				
				$this->output->assign('target', $target);

				$this->output->setTmpl('support/permission.php');
				break;
		}
	}

	private function search() {
		$search = $this->getQuery('search');

		$page = $this->getQuery('page');
		empty($page) ? $page = 1 : '';
		
		$pager = new SimplePager($page, Env::PAGE_LIST);
		
		$data = Logic_Permission::getPermissionDataLimited($this->system_db, $search, $pager->limit(), $pager->offset());

		$this->output->assign('data', $data['list']);

		$pager->setPager($data['count'], self::PAGER_ARM_LENGTH);

		$params = array(
			'search' => $search
		);

		$this->output->assign('pager', $pager->output($params));

		$this->output->setTmpl('support/_permission_list.php');
	}

	private function viewer() {
		$id = $this->getQuery('id');

		$permission = array(
			'id' 			=> '',
			'name' 			=> '',
			'description' 	=> '',
			'manage' 		=> array(),
			'view' 			=> array()
		);

		if(!empty($id)) {
			$permission = Logic_Permission::getPermissionDataById($this->system_db, $id);
			$allow_no_array = Logic_Permission::getAlreadyAllowMap($this->system_db, $id);
			
			$view_map = array();
			$manage_map = array();
			foreach ($allow_no_array as $idx => $row) {
				if($row['roles'] == 'all') {
					$view_map[$row['allow_no']] = 'checked';
					$manage_map[$row['allow_no']] = 'checked';
				}

				if($row['roles'] == 'view') {
					$view_map[$row['allow_no']] = 'checked';
				}

				if($row['roles'] == 'manage') {
					$manage_map[$row['allow_no']] = 'checked';
				}
			}

			$permission['view'] = $view_map;
			$permission['manage'] = $manage_map;
		}

		$this->output->assign('permission', $permission);

		$this->output->setTmpl('support/_permission_viewer.php');
	}

	private function saveChanges() {
		$result_map = array('status' => true, 'message' => "The data has been save changed!");

		$category = null;

		$id = $this->getQuery('id');

		$view_array = $this->generateAllowNo($this->getQuery('allow_no_v'));
		$manage_array = $this->generateAllowNo($this->getQuery('allow_no_m'));

		if(count($view_array) == 0 && count($manage_array) == 0) {
			$result_map['status'] = false;
			$result_map['message'] = "Roles is required.";
			$this->sendJsonResult($result_map);
			exit();
		} 

		$allow_no = array_unique(array_merge($view_array, $manage_array));
		$roles = $this->generateRoles($view_array, $manage_array);

		$params = array(
			'name' => $this->getQuery('name'),
			'description' => $this->getQuery('description'),
			'allow_no' => $allow_no,
			'roles' => $roles
		);

		if(strlen($this->error_msg) > 0) {
			$result_map['status'] = false;
			$result_map['message'] = $this->error_msg;
		} else {
			$result = false;

			if(empty($id)) {
				$category = Category::PERMISSION_NEW;

				$result = Logic_Permission::insertPermissionData($this->system_db, $params);
			} else {
				$category = Category::PERMISSION_CHANGE;

				$params['id'] = $id;
				$result = Logic_Permission::updatePermissionData($this->system_db, $params);
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

	private function generateRoles($view_array, $manage_array) {
		$roles = array();

		foreach ($view_array as $idx => $key) {
			$roles[$key] = 'view';
		}

		foreach ($manage_array as $idx => $key) {
			$roles[$key] = 'manage';
			
			if(array_key_exists($key, $roles)) {
				$roles[$key] = 'all';
			}
		}

		return $roles;
	}

	private function generateAllowNo($allow_no) {
		$allow_no_array = array();

		if(is_array($allow_no)) {
			$allow_no_array = $allow_no;
		} else {
			if(!empty($allow_no)) {
				$allow_no_array = array($allow_no);
			}
		}

		return $allow_no_array;
	}
}