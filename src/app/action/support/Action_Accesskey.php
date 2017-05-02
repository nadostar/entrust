<?php

/**
* 
*/
class Action_AccessKey extends _Action_Support {
	protected $menu_allow_no = MenuData::SURVEY_ACCESSKEY_ID;

	protected $error_msg = '';

	private $action = null;

	protected function initialize() {
		parent::initialize();

		$this->registValidatorMap('m');
	}

	protected function preAction() {
		parent::preAction();

		$this->action = $this->getQuery('m');

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
				break;
			default:
				$target = array(
					'tree' => 'Survey',
					'menu' => $this->menu_allow_no
				);

				$this->output->assign('target', $target);

				$this->output->setTmpl('support/accesskey.php');
				break;
		}
		
	}

}