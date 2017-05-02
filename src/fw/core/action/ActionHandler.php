<?php

require_once __DIR__.'/../exception/Exception_Action.php';

class ActionHandler {

	public static function getActionInstance($mode, $action) {
		if ($action == '') {
			throw new Exception_Action('action is empty');
		}
		if (strpos($action, '_') !== false) {
			$tmp_arry = explode('_', $action);
			$classname = 'Action_';
			foreach($tmp_arry as $each){
				$classname .= ucfirst($each);
			}
		} else {
			$classname = 'Action_'.ucfirst($action);
		}

		if ($mode) {
			$filename = BASE_DIR.'src/app/action/'.$mode.'/'.$classname.'.php';
		} else {
			$filename = BASE_DIR.'src/app/action/'.$classname.'.php';
		}
		
		if (!is_file($filename)) {
			throw new Exception_Action('invalid Action:'.$filename);
		}

		require $filename;
		return new $classname();
	}
}