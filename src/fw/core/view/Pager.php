<?php
/**
 * 페이징 처리
 *
 * @package    fw.core.view
 * @author     xing
 */

class Pager {
  /** デフォルト表示件数 */
  const NUM_PER_PAGE = 10;
  /** ページャの腕の長さ */
  const PAGER_ARM_LENGTH = 1;

  /** カレントページ */
  private $cur_page;
  /** 前のページ */
  private $pre_page;
  /** 次のページ */
  private $next_page;
  /** 1ページあたりの件数 */
  private $num_per_page;

  /** 最初のページ */
  private $min_page;
  /** 最後のページ */
  private $max_page;

  /** 最小取得値 */
  private $offset;
  /** 取得件数 */
  private $limit;
  /** 最大件数 */
  private $total;

  /** ページ番号格納用 */
  private $no;
  /** 全ページ番号格納用 */
  private $all_no;

  /** URL */
  private $url;

  /** 例外フラグ */
  private $exception;

  /**
   * コンストラクタ
   *
   * @param	$page			ページ番号
   * @param	$url			URL
   * @param	$num_per_page	1ページあたりの件数
   * @param	$exception		例外クラス名（nullで未処理）
   */
  public function __construct($page, $url, $num_per_page = self::NUM_PER_PAGE, $exception=null) {
      //セガクラウド用対応　ドメイン直下のURLに変更する
      $tmp_url = preg_replace('|(https*://[^/]+)|', '', $url);

    $this->cur_page 	= ($page > 0) ? $page : 1;
    $this->url 			= $tmp_url;
    $this->num_per_page = $num_per_page;
    $this->exception	= $exception;

    // kanai ページ範囲チェック
    if( isset($this->exception) && $page<1){
      throw new $this->exception("page not much choice (range over minus)");
    }

    // データ取得用パラメータセット
    $this->limit 		= ($num_per_page > 0) ? $num_per_page : 1;
    $this->offset 		= $this->limit * ($this->cur_page-1);
    $this->no = $this->cur_page;
  }

  /**
   * ページャセット
   *
   * @param	$total			データ全件数
   * @param	$pager_arm_len	ページャーの腕の長さ
   */
  public function setPager($total, $pager_arm_len = self::PAGER_ARM_LENGTH) {
    // 各パラメータセット
    $this->total		= $total;
    $this->max_page		= ceil($this->total / $this->num_per_page);
    $this->min_page		= 1;
    $this->pre_page		= ($this->cur_page > $this->min_page) ? $this->cur_page - 1 : 0;
    $this->next_page	= ($this->cur_page < $this->max_page) ? $this->cur_page + 1 : 0;

    // kanai ページ範囲チェック(トータル数が０の場合は検索結果が無いだけと判断しエラーにしない。)
    if( isset($this->exception) && $this->cur_page>$this->max_page && $this->total>0){
      throw new $this->exception("page not much choice (range over plus)");
    }

    // 中心・左右のページ番号
    $center = $this->cur_page;
    $left = $this->cur_page - $pager_arm_len;
    $right = $this->cur_page + $pager_arm_len;

    // 最初・最後のページとカレントページの差
    $left_margin = $this->cur_page - 1;
    $right_margin = $this->max_page - $this->cur_page;

    // カレントページ（中心）が左右に偏った場合の修正
    if ($left_margin < $pager_arm_len) {
      $right += $pager_arm_len - $left_margin;
      $center += $pager_arm_len - $left_margin;
    }
    if ($right_margin < $pager_arm_len) {
      $left -= $pager_arm_len - $right_margin;
      $center -= $pager_arm_len - $right_margin;
    }

    // 左右の端はみだし修正
    if ($left < 1) {
      $left = 1;
    }
    if ($right > $this->max_page) {
      $right = $this->max_page;
    }

    // 全ページ番号格納
    for ($i = $left; $i <= $right; $i++) {
      $all_no[$i] = $i;
    }
    // kanai トータル０対応（フィルター検索で結果件数が０の場合があるため）
    if(!empty($all_no)){
      $this->all_no = $all_no;
    }
  }

  /**
   * 全ページ作成
   */
  public function all() {
    $buf = array();
    foreach ($this->all_no as $no) {
      $buf[$no] = new Pager($no, $this->url);
      $buf[$no]->setPager($this->total);
    }
    return $buf;
  }

  /**
   * 出力：カレントページ
   */
  public function cur() {
    $this->no = $this->cur_page;
    return $this;
  }

  /**
   * 出力：カレントページの最小インデックス
   */
  public function firstIndex() {
    $idx = $this->num_per_page * ($this->no - 1) + 1;
    if ($idx > $this->total) { $idx = $this->total; }
    return $idx;
  }

  /**
   * 出力：カレントページの最小インデックス
   */
  public function lastIndex() {
    $idx = $this->num_per_page * $this->no;
      if ($idx > $this->total) { $idx = $this->total; }
    return $idx;
  }

  /**
   * 出力：最大件数
   */
  public function total() {
    return $this->total;
  }

  /**
   * 出力：ページ番号
   */
  public function no() {
    return $this->cur_page;
  }

  /**
   * 出力：最初のページ
   */
  public function first() {
    $this->no = $this->min_page;
    return $this;
  }

  /**
   * 出力：最後のページ
   */
  public function last() {
    $this->no = $this->max_page;
    return $this;
  }

  /**
   * 出力：前のページ
   */
  public function prev() {
    $this->no = $this->pre_page;
    return $this;
  }

  /**
   * 出力：次のページ
   */
  public function next() {
    $this->no = $this->next_page;
    return $this;
  }

  /**
   * 出力：offset
   */
  public function offset() {
    return $this->offset;
  }

  /**
   * 出力：limit
   */
  public function limit() {
    return $this->limit;
  }

  /**
   * ページャURL構築・出力
   */
  public function url() {
    if (!$this->no) return '';

    // URLを分割
    $tmp = explode('?', $this->url);
    $sep = '?';
    $url = '';

    // クエリパラメータを再構築、URL生成
    if (count($tmp) > 1) {
      $tmp_2 = explode('&', $tmp[1]);
      foreach ($tmp_2 as $key => $each) {
        if (substr($each, 0, 5) == 'page=') {
          unset($tmp_2[$key]);
        }
      }
      if (count($tmp_2) > 0) {
        $url = $tmp[0].'?'.join('&', $tmp_2);
        $sep = '&';
      } else {
        $url = $tmp[0];
      }
    }

    // ページ番号を連結
    $url = sprintf('%s%s%s=%s', $url, $sep, 'page', $this->no);

    return $url;
  }

  /**
   * オブジェクト出力定義
   */
  function __toString() {
    return (string) $this->no;
  }
}