<?php

require_once __DIR__.'/_Validator.php';

class Validator_Regex extends _Validator {
	/**
	 * @see _Validator::valid()
	 */
	public function valid($value, $param=null, $message='') {
		if (is_null($value) || $value=='') return;
		if (!Util_validator::checkRegex($value, $param)) throw new Exception_Validation($message);
	}
}