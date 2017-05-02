<?php

require_once __DIR__.'/_LogOutput.php';

/**
 * ログファイル出力
 *
 * @package    fw.core.log
 */
class LogOutput_File extends _LogOutput {
	
	/**
	 * @see _LogOutput::output()
	 */
	public function output($var, $msg, $level) {
		list($micro, $Unixtime) = explode(' ', microtime());
		$micro = substr(str_replace('0.', '.', $micro), 0, 4);
		$txt = '';
		if (isset($this->setting['single_line']) && $this->setting['single_line']) {
			$txt .= date('Y-m-d H:i:s', $Unixtime).$micro.' '.$msg.' > '.preg_replace("/\n/", '', print_r($var, true));
		} else {
			$txt .= date('Y-m-d H:i:s', $Unixtime).$micro.' '.$msg.' > '.print_r($var, true);
		}
		$txt .= "\n";
		if (isset($this->setting['file'])) {
			$file = $this->setting['file'];
			error_log($txt, 3, $file);
		} else {
			error_log($txt, 0);
		}
	}
}