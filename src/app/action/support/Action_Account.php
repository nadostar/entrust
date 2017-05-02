<?php

class Action_Account extends _Action_Support {
	protected $menu_allow_no = MenuData::ENTRUST_ACCOUNT_ID;
	
	protected function initialize() {
		parent::initialize();
	}

	protected function doAction() {
		$target = array(
			'tree' => 'Survey',
			'menu' => $this->menu_allow_no
		);
		
		$this->output->assign('target', $target);

		$this->output->setTmpl('support/account.php');
	}
}