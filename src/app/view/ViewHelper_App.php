<?php
/**
 * View 관련 클래스
 *
 * @package    app.view
 * @author     xing
 */

/**
 * url 회득
 *
 * @param	$url						url
 * @param	$is_echo					true/false=문자열 출력 여부
 * @param	$is_til						true/false
 * @param	$is_opensocial_owner_id		true/false
 * @return	url
 */
function url($url, $is_echo=true, $is_til=true) {
	$url = Env::APP_URL.$url;

	$param = array();

	// 캐시 방지
	if ($is_til) {
		$param[] = 'til='.til();
	}

	if (count($param) > 0) {
		if (strpos($url, '?') !== false) {
			$url .= '&';
		} else {
			$url .= '?';
		}
	}
	$url .= implode('&', $param);

	if ($is_echo) {
		echo $url;
	}
	return $url;
}

/**
 * <form> tld
 *
 * @param	$action		アクション送信先
 * @param	$method		リクエストメソッド
 * @param	$name		formの名前
 * @return				form開始タグ
 */
function start_form_tag($action, $method='get', $name=null) {
	if (is_null($name)) {
		echo '<form action="'.url($action, false, false, false).'" method="'.$method.'">'."\n";
	} else {
		echo '<form action="'.url($action, false, false, false).'" method="'.$method.'" name="'.$name.'">'."\n";
	}
	echo '<input type="hidden" name="til" value="'.til().'" />'."\n";
}

/**
 * </form> tld
 *
 * @return	form終了タグ
 */
function end_form_tag() {
	echo '</form>'."\n";
}

/**
 * til取得
 *
 * @return 				ハッシュ
 */
function til() {
	return md5(time().'til');
}

function json_format($json, $html = true) {
	$tabcount = 0;
	$result = '';
	$inquote = false;
	$ignorenext = false;
	if ($html) {
		$tab = "   ";
		$newline = "<br/>";
	} else {
		$tab = "\t";
		$newline = "\n";
	}
	for($i = 0; $i < strlen($json); $i++) {
		$char = $json[$i];
		if ($ignorenext) {
			$result .= $char;
			$ignorenext = false;
		} else {
			switch($char) {
				case '{':
					$tabcount++;
					$result .= $char . $newline . str_repeat($tab, $tabcount);
				break;
				case '}':
					$tabcount--;
					$result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
				break;
				case ',':
					$result .= $char . $newline . str_repeat($tab, $tabcount);
				break;
				case '"':
					$inquote = !$inquote;
					$result .= $char;
				break;
				case '\\':
					if ($inquote) $ignorenext = true;
					$result .= $char;
				break;
				default:
					$result .= $char;
			}
		}
	}
	echo $result;
}

