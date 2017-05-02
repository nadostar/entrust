<?php

/**
* 
*/
class Action_FindUsers extends _Action_Support {
	protected function doAction() {
		$target = array(
			'tree' => 'FindUsers',
			'menu' => 0
		);
		
		$this->output->assign('target', $target);

		$this->output->setTmpl('support/find_users.php');
	}
}