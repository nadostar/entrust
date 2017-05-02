<?php
/**
 * JSON 출력 클래스
 *
 * @package    fw.core.output
 */

require_once __DIR__.'/_Output.php';

class Output_JSON extends _Output {
	public function output() {
		echo json_encode($this->data);
	}

}
