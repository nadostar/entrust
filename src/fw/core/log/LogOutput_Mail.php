<?php

require_once __DIR__.'/_LogOutput.php';
require_once __DIR__.'/../exception/Exception_Log.php';

class LogOutput_Mail extends _LogOutput {

	/**
	 * @see _LogOutput::output()
	 */
	public function output($var, $msg, $level) {
		list($micro, $Unixtime) = explode(' ', microtime());
		$micro = substr(str_replace('0.', '.', $micro), 0, 4);
		$txt = '';
		$txt .= date('Y-m-d H:i:s', $Unixtime).$micro.' > '.$msg."\n";
		$txt .= print_r($var, true)."\n";

		$subject = '';
		if (isset($this->setting['subject'])) {
			$subject = $this->setting['subject'];
		}

		if (isset($this->setting['to'])) {
			$to = $this->setting['to'];
		} else {
			throw new Exception_Log('invalid mail to');
		}

		mb_send_mail($to, $subject, $txt);
	}
}