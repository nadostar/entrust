<?php

require_once __DIR__.'/_Validator.php';
require_once __DIR__.'/../util/Util_validator.php';

class Validator_Number extends _Validator {
	/**
	 * @see _Validator::valid()
	 */
	public function valid($value, $param=null, $message='') {
		if (is_null($value) || $value=='') return;
		if (!Util_validator::checkNumberWithMinus($value)) throw new Exception_Validation($message);
	}
}