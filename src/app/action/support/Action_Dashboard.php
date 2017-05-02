<?php

class Action_Dashboard extends _Action_Support {
	
	
	protected function doAction() {
		$target = array(
			'tree' => 'Dashboard',
			'menu' => 0
		);
		
		$this->output->assign('target', $target);

		$this->output->setTmpl('support/dashboard.php');
	}
}