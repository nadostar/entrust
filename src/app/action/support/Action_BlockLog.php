<?php

/**
* 
*/
class Action_BlockLog extends _Action_Support {
	protected $menu_allow_no = MenuData::LOG_IP_ADDRESS_ID;

	protected $error_msg = '';

	private $action = null;

	protected function initialize() {
		parent::initialize();
		
		// module
		$this->registValidatorMap('m');
	}

	protected function preAction() {
		parent::preAction();

		$this->action = $this->getQuery('m');

		$this->registValidatorMap('page');
		$this->registValidatorMap('limit');

		$this->registValidatorMap('id');

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
			default:
				$target = array(
					'tree' => 'Logs',
					'menu' => $this->menu_allow_no
				);

				$this->output->assign('target', $target);

				$this->output->setTmpl('support/blocklog.php');	
				break;
		}
	}

	private function search() {

	}

	private function viewer() {
		$id = $this->getQuery('id');


	}
}