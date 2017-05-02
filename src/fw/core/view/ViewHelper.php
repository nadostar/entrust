<?php

/**
 * HTMLのエスケープ処理
 *
 * @param  $str			対象文字列
 * @param  $nl2br_flg	改行を<br>タグに変換
 * @param  $is_echo		echo出力するか
 */
function es( $str, $nl2br_flg = FALSE, $is_echo = TRUE ) {
	if ( !isset($str) || $str === NULL || $str === '' ) {
		echo '';
		return '';
	}

	$tmp =  sanitizeStr4html($str, $nl2br_flg );

	if ($is_echo) {
		echo $tmp;
	} else {
		return $tmp;
	}
}

/**
 * HTML出力用のサニタイズ
 *
 * @param  $str
 * @param  $nl2br_flg
 */
function sanitizeStr4html($str, $nl2br_flg = FALSE){
	$str = htmlspecialchars($str, ENT_QUOTES);

	if ($nl2br_flg){
		$str = nl2br($str);
	}
	return $str;
}

/**
 * 日付変換
 *
 * @param  $date	日付文字列
 * @param  $format	日付変換フォーマット
 */
function dateFormat($date, $format = 'Y년m월j일　H시i분s초') {
	$dateformat = '';

	if (empty($date)) {
		return;
	}
	$dateformat = es(date($format, strtotime($date)));

	return $dateformat;
}

/**
 * セレクトボックス生成
 *
 * @param  $selectParam	selectbox内容の連想配列（key:インデックス value:表示名）
 * @param  $name		name属性
 * @param  $default		デフォルトのインデックス
 * @param  $option		属性
 */
function SelectBox($name, $selectParam, $default = '', $option = '') {
	$html = sprintf('<select id="%s" name="%s" %s>%s', $name, $name, $option, "\n");
	if (!is_array($selectParam)) {
		return '';
	}
	foreach ($selectParam as $key => $value) {
		if ($key == $default) {
			$selected = ' selected';
		} else {
			$selected = '';
		}
		$html .= sprintf('<option value="%s"%s>%s</option>%s', $key, $selected, $value, "\n");
	}
	$html .= sprintf('</select>%s', "\n");

	echo $html;
}