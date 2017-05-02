<?php
/**
 * KVSアクセス基底クラス
 *
 * @package    fw.core.kvs
 * @author     Dai-Yamamoto
 * @copyright  (c) Copyright 2012 O-two, Inc. . All rights reserved.
 */

require_once __DIR__ . '/../exception/Exception_KVS.php';

/**
 * KVSアクセス基底クラス
 *
 * @package    fw.core.kvs
 */
abstract class _KVSAccess
{
    /** デフォルトキャッシュ時間（秒） */
    const DEFAULT_CACHE_TTL = 3600;

    protected $default_cache_ttl = self::DEFAULT_CACHE_TTL;

    /** デフォルトロック時間（秒） */
    const DEFAULT_LOCK_TTL = 3;
    /** デフォルトロック最大試行回数 */
    const DEFAULT_LOCK_TRY = 3;
    /** デフォルトロック試行間隔（秒） */
    const DEFAULT_LOCK_TRY_INTERVAL = 1;

    /** ロックプリフィックス */
    const LOCK_PREFIX = '_L:';

    /** 接続情報 */
    protected $param;

    /**
     * コンストラクタ
     *
     * @param   $param  接続情報
     */
    public function __construct($param = null)
    {
        $this->param = $param;
        $this->connect();
    }

    /**
     * 接続
     */
    abstract public function connect();

    /**
     * キャッシュの取得
     *
     * @param   $key        キー
     * @param   $KVSAccess  KVSアクセスクラス
     * @return 値（存在しなければfalse）
     */
    abstract public function get($key, _KVSAccess $KVSAccess = null);

    /**
     * キャッシュの設定
     *
     * @param   $key    キー
     * @param   $value  値
     * @param   $ttl    保持期間
     */
    abstract public function set($key, $value, $ttl = self::DEFAULT_CACHE_TTL);

    /**
     * キャッシュの削除
     *
     * @param   $key    キー
     */
    abstract public function remove($key);

    /**
     * キャッシュのロック
     *
     * @param   $key    キー
     * @param   $ttl    保持期間
     * @return  true/false=ロック取得/ロック未取得
     * @throws  Exception_KVS
     */
    abstract public function lock(
        $key,
        $ttl = self::DEFAULT_LOCK_TTL,
        $try = self::DEFAULT_LOCK_TRY,
        $lock_interval = self::DEFAULT_LOCK_TRY_INTERVAL
    );

    /**
     * キャッシュのロック解放
     *
     * @param   $key    キー
     */
    abstract public function unlock($key);

    /**
     * キャッシュの設定（ロック有）
     *
     * @param   $key            キー
     * @param   $value          値
     * @param   $ttl            保持期間
     * @param   $lock_ttl       ロック保持期間
     * @param   $try            ロック試行回数
     * @param   $lock_interval  ロック試行間隔
     * @return  true/false=更新成功/更新失敗
     */
    public function setWithLock(
        $key,
        $value,
        $ttl = self::DEFAULT_CACHE_TTL,
        $lock_ttl = self::DEFAULT_LOCK_TTL,
        $try = self::DEFAULT_LOCK_TRY,
        $lock_interval = self::DEFAULT_LOCK_TRY_INTERVAL
    ) {
        $this->lock($key, $lock_ttl, $try, $lock_interval);
        $this->set($key, $value, $ttl);
        $this->unlock($key);
    }

}
