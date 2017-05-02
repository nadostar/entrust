<?php

require_once __DIR__.'/_Validator.php';

class Validator_Input extends _Validator {
	/**
	 * @see _Validator::valid()
	 */
	public function valid($value, $param=null, $message='') {
		if (!Util_validator::checkInput($value)) throw new Exception_Validation($message);
	}
}