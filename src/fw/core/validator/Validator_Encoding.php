<?php

require_once __DIR__.'/_Validator.php';

class Validator_Encoding extends _Validator {
	/**
	 * @see _Validator::valid()
	 */
	public function valid($value, $param=null, $message='') {
		if (is_null($value) || $value=='') return;
		if (!Util_validator::checkEncoding($value, $param)) throw new Exception_Validation($message);
	}
}