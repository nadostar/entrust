<?php

require_once __DIR__.'/_LogOutput.php';

/**
 * HTMLログ出力
 *
 * @package    fw.core.log
 */
class LogOutput_HTML extends _LogOutput {

	/**
	 * @see _LogOutput::output()
	 */
	public function output($var, $msg, $level) {
		list($micro, $Unixtime) = explode(' ', microtime());
		$micro = substr(str_replace('0.', '.', $micro), 0, 4);
		echo date('Y-m-d H:i:s', $Unixtime).$micro.' > '.$msg;
		echo '<br/>';
		echo '<pre>';
		print_r($var);
		echo '</pre>';
	}
}