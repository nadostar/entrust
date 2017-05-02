<?php
/**
 *
 * @package    fw.core.validator
 * @author     xing
 */

require_once __DIR__.'/../exception/Exception_Validation.php';

abstract class _Validator {
	abstract public function valid($value, $param=null, $message='');
}