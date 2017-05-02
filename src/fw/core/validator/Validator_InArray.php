<?php

require_once __DIR__.'/_Validator.php';

class Validator_InArray extends _Validator {
	/**
	 * @see _Validator::valid()
	 */
	public function valid($value, $param=null, $message='') {
		if (is_null($value) || $value=='') return;
		if (!is_array($param)) throw new Exception_Validation($message);
		if (!Util_validator::checkArrayVal($value, $param)) throw new Exception_Validation($message);
	}
}