<?php

class Action_Event extends _Action_Support {
	protected $menu_allow_no = MenuData::ENTRUST_EVENT_ID;
	
	protected function initialize() {
		parent::initialize();
	}

	protected function doAction() {
		$target = array(
			'tree' => 'Survey',
			'menu' => $this->menu_allow_no
		);
		
		$this->output->assign('target', $target);

		$this->output->setTmpl('support/event.php');
	}
}