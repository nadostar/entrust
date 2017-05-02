<?php

/**
* 
*/
class Action_FindSurvey extends _Action_Support {
	protected function doAction() {
		$target = array(
			'tree' => 'FindSurvey',
			'menu' => 0
		);
		
		$this->output->assign('target', $target);

		$this->output->setTmpl('support/find_survey.php');
	}
}