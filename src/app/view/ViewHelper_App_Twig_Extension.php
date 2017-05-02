<?php
/**
 * アプリ用ビューヘルパー
 *
 * @package    app.view
 * @author     Dai-Yamamoto
 * @copyright  (c) Copyright 2012 O-two, Inc. . All rights reserved.
 */

class ViewHelper_App_Twig_Extension extends Twig_Extension {
  public function getName() {
    return 'ViewHelper_App_Twig_Extension';
  }

  public function getFilters() {
    return array('url' => new Twig_Filter_Method($this, 'url'),
        'iurl' => new Twig_Filter_Method($this, 'iurl'),
        'cardmini' => new Twig_Filter_Method($this, 'cardmini'),
        'prt' => new Twig_Filter_Method($this, 'prt'),
        'ver_url' => new Twig_Filter_Method($this, 'ver_url'),
        'datetime2lasttime' => new Twig_Filter_Method($this, 'datetime2lasttime'),
        'ymdhis' => new Twig_Filter_Method($this, 'ymdhis'),
        'deck_type_str' => new Twig_Filter_Method($this, 'deck_type_str'),
        'ceil' => new Twig_Filter_Method($this, 'ceil'),
        'floor' => new Twig_Filter_Method($this, 'floor'),
        'json_encode' => new Twig_Filter_Method($this, 'json_encode'),
    );
  }

  public function getFunctions() {
      return array(
              'time' => new Twig_Function_Function('time'),
              'date' => new Twig_Function_Function('date'),
              'substr' => new Twig_Function_Function('substr'),
              'prt' => new Twig_Function_Function('prt'),
              '_' => new Twig_Function_Function('_'),//ローカライズ関数 gettext
      );
  }


  /////////////////

  public function url($url) {
        $url = Env::APP_URL.$url;

        $person_hash = set( $_GET['uid'] );
        if ( empty($person_hash)) $person_hash = '';

        // キャッシュ防止
        if (strpos($url, '?') !== false) {

            $url .= '&uid='.$person_hash.'&';
        } else {
            $url .= '?uid='.$person_hash.'&';
        }



        return $url;

  }

  public function iurl($value) {
    return iurl($value);

  }

  public function ver_url($value) {
    return ver_url($value, FALSE);

  }

  /**
   *
   * datetimeから現在の秒数との差を得る
   * @param unknown_type $value
   */
  public function datetime2lasttime($value) {
    $tmp = datetime2unixtime($value);

    if (NOW_TIME > $tmp) {
      return '00:00:00';
    } else {
      $tmp = $tmp - NOW_TIME;
      $hour = floor($tmp / 3600);
      $min = floor(($tmp - ($hour * 3600)) / 60);
      $sec = $tmp - ($hour * 3600) - ($min * 60);

      return sprintf("%02d:%02d:%02d", $hour, $min, $sec);
    }

  }

  public function ymdhis($value) {

    if (date('Y', NOW_TIME) != date('Y', $value)) {
      return date('Y/m/d H:i', $value);
    } else {
      return date('m/d H:i', $value);
    }

    return date();
  }

  public function prt($value) {
    return prt($value);
  }



  public function ceil($value) {
    return ceil($value);
  }

  public function floor($value) {
    return floor($value);
  }

  public function json_encode($arry) {
    return json_encode($arry);
  }

  public function cardmini($value) {
    return str_replace('card', 'card_mini', $value);
  }

}
