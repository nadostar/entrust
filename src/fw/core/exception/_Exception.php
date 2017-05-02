<?php

/**
 * 예외 처리 클래스
 *
 * @package    fw.core.exception
 */
abstract class _Exception extends ErrorException {

	protected $log_level = LogManager::LEVEL_ERROR;//例外の緊急度によってログレベルを変更する

	protected $messageMaker = 'debugMessage_html';

	/**
	 * コンストラクタ
	 *
	 * @param	$message	メッセージ
	 * @param	$code		例外コード
	 * @param	$previous	以前の例外
	 */
	function __construct($message, $log_level = NULL,  $errno = NULL, $errfile = NULL, $errline = NULL) {
		if ( $log_level === NULL ){
			$log_level = $this->log_level;
		}

		if ( $errno !== NULL &&  $errfile !== NULL &&  $errline !== NULL ){
			parent::__construct($message, $errno, $log_level,  $errfile, $errline );
		}
		else {
			parent::__construct($message, 0, $log_level);
		}
	}

	/**
	 *
	 * 緊急度に従ってログを出力する
	 */
	public function outputLog(){

		LogManager::outputLog( $this->getDebugMessage( ) , get_class($this).' LOG', NULL, $this->log_level );


	}

	/**
	 *
	 * デバッグ用メッセージ
	 */
	public function getDebugMessage(){
		$message = '';
		if (isset( $this->xdebug_message) && !empty($this->xdebug_message) ){
			$message = $this->xdebug_message;
		}

		$messageMaker = $this->messageMaker;


		$text = $this->$messageMaker( $this->getTrace(), $message );

		return $text;

	}


	/**
	 *
	 * 例外デバッグメッセージ（フィーチャーフォン用HTMLに）
	 * @param unknown_type $arry
	 */
	static public function debugMessage_html( $trace_arry, $message = ''){
		if ( !is_array($trace_arry)){
			return $arry;
		}

		$message = '<table width="240" border="1" cellpadding="2" cellspacing="0" class="exception_trace">
  <tr>
    <td valign="top">'.$message.'</td>
  </tr>
</table><br>'."\n\n";

		$parse_cl = function( $hash_arry ){

			extract($hash_arry);

			$html = '
<table width="240" border="1" cellpadding="2" cellspacing="0" class="exception_trace">
  <tr>
    <td width="50" valign="top" class="exception_trace_title"><pre>file:</pre></td>
    <td valign="top">'.set($file)."\n".'</td>
  </tr>
  <tr>
    <td valign="top" class="exception_trace_title"><pre>line:</pre></td>
    <td valign="top">'.set($line)."\n".'</td>
  </tr>
  <tr>
    <td valign="top" class="exception_trace_title"><pre>function:</pre></td>
    <td valign="top">'.set($function)."\n".'</td>
  </tr>
  <tr>
    <td valign="top" class="exception_trace_title"><pre>class:</pre></td>
    <td valign="top">'.set($class)."\n".'</td>
  </tr>
  <tr>
    <td valign="top" class="exception_trace_title"><pre>type:</pre></td>
    <td valign="top">'.set($type)."\n".'</td>
  </tr>
  <tr>
    <td valign="top" class="exception_trace_title"><pre>args:</pre></td>
    <td valign="top"><pre>'.var_export( $args, TRUE) .'</pre></td>
  </tr>
</table>';

			return $html;
		};

		$css = '<style type="text/css">
<!--
.exception_trace {
	font-size: xx-small;
}
.exception_trace_title {
	font-weight: bold;
	color: #C30;
	background-color: #FFC;
}
-->
</style>';

		$html = $css.$message;
		foreach( $trace_arry as $each ){
			$html .= $parse_cl( $each );

		}



		return $html;

	}


	/**
	 *
	 * 例外トレースメッセージ（テキストに）
	 * @param unknown_type $arry
	 */
	static public function debugMessage_text( $trace_arry, $message = ''){

		//htmlを拝借…
		$html = self::parseTrace2Html($arry);

		return strip_tags($html);
	}
}



///////////////////
class Exception_Runtime extends _Exception {

}


//　エラーハンドラ定義
function error_handler($errno, $errstr, $errfile, $errline ) {
    throw new Exception_Runtime($errstr, NULL, $errno, $errfile, $errline);
}
set_error_handler('error_handler');

function exception_handler( $Exception ) {
	error_log("Uncaught exception: ".$Exception->getMessage());
}
set_exception_handler('exception_handler');