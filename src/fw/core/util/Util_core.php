<?php

/**
 * 未定義を考慮した値の取得
 *
 * @param	値
 * @return	値
 */
function set(&$var) {
	return ($var == 0 || !empty($var) ? $var : null);
}

/**
 * 配列と連想配列の判断
 *
 * @param	$array	判定対象
 * @return	true/false=連想配列/配列
 */
function is_hash(&$array) {
	$i = 0;
	foreach($array as $k => $dummy) {
		if ( $k !== $i++ ) return true;
	}
	return false;
}

/**
 *
 * 配列の正当性チェック　配列且つ内容があるかの判断
 * @param unknown_type $arry
 */
function is_valid_array($arry){
	if ( isset($arry) && is_array($arry) && sizeof($arry) > 0 ){
		return TRUE;
    } else {
		return FALSE;
	}
}


/**
 * オブジェクトの内容表示
 *
 * @param	$var	オブジェクト
 * @param	$msg	メッセージ
 */
function prt($var, $msg='', $is_print_r = FALSE ) {
	$back_trace_arry = debug_backtrace();
	$back_trace = '';
	if ( isset($back_trace_arry[0]['file']) && isset($back_trace_arry[0]['line'])){

		$back_trace = basename($back_trace_arry[0]['file']).':'.$back_trace_arry[0]['line'];
	}

	list($micro, $Unixtime) = explode(' ', microtime());
	$micro = substr(str_replace('0.', '.', $micro), 0, 4);
	echo date('Y-m-d H:i:s', $Unixtime).$micro.' > '.$msg;
	echo '<br>';
	if ( !empty($back_trace)) echo "<b>${back_trace}</b><br>";
	echo '<pre>';

	if ( $is_print_r ){
		print_r($var);
	}
	else {
		var_dump($var);
	}


	echo '</pre>';
	echo '<hr noshade>';
}



/**
 *
 * 自分自身を返す
 * @param unknown_type $val
 */
function me( $val ){
	return $val;
}


/*
 * str_split マルチバイト
 *
 * http://php.net/manual/ja/ref.mbstring.php 
 * このページのサンプルをそのまま使うと無限ループになったので注意 
 *「$i + $split_length 」=> 「$i+=$split_length」
 * A multibyte one-to-one alternative for the str_split function (http://php.net/manual/en/function.str-split.php):
 *
 * @param unknown_type $string          入力文字列
 * @param unknown_type $split_length    分割した部分の最大長 (全角も1文字としてカウントします)
*/
function mb_str_split($string, $split_length = 1){
    $split_length = ($split_length <= 0) ? 1 : $split_length;
    $mb_strlen = mb_strlen($string, 'UTF-8');
    $ret = array();
    for($i = 0; $i < $mb_strlen; $i+=$split_length){
        $ret[] = mb_substr($string, $i, $split_length, 'UTF-8'); 
    }
    return $ret;
}


/*
 * 64bitPHPかどうか
 *
 * @return  true/false=64bit/64bit以外
 */
function is64bit() {
    return PHP_INT_SIZE==8; // PHP_INT_SIZE = n byte
}



