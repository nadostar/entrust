<?php

require_once __DIR__.'/_Validator.php';
require_once __DIR__.'/../util/Util_validator.php';

class Validator_NumberRange extends _Validator {
	/**
	 * @see _Validator::valid()
	 */
	public function valid($value, $param=null, $message='') {
		if (is_null($value) || $value=='') return;
		if (isset($param['min'])) {
			$min = $param['min'];
		} else {
			throw new Exception_Validation($message);
		}
		if (isset($param['max'])) {
			$max = $param['max'];
		} else {
			throw new Exception_Validation($message);
		}

		if (!Util_validator::checkNumberRange($value, $min, $max)) throw new Exception_Validation($message);
	}
}
