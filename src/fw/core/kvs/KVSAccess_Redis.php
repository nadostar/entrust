<?php
/**
 * Redisアクセスクラス
 *
 * @package    fw.core.kvs
 * @author     Masashi-Kitagawa
 * @copyright  (c) Copyright 2012 O-two, Inc. . All rights reserved.
 */

require_once __DIR__ . '/_KVSAccess.php';

/**
 * Redisアクセスクラス
 *
 * @package    fw.core.kvs
 */
class KVSAccess_Redis extends _KVSAccess
{
    /** Redisオブジェクト */
    private $redis = null;

    /** デフォルトポート */
    const DEFAULT_PORT = 6379;

    /**
     * @see _KVSAccess::connect()
     */
    public function connect()
    {
        //この時点では何もしない

    }

    private function connectionFactory()
    {
        if ($this->redis === null) {
            if (!isset($this->param['host'])) {
                throw new Exception_KVS('invalid host');
            } else {
                $host = $this->param['host'];
            }
            if (!isset($this->param['port'])) {
                $port = self::DEFAULT_PORT;
            } else {
                $port = $this->param['port'];
            }

            $this->redis= new Redis();
            $this->redis->connect($host, $port);
            if (isset($this->param['password'])) {
                $ret = $this->redis->auth($this->param['password']);
                if (!$ret) {
                    throw new Exception_KVS('unauthenticated');
                }
            }
            $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
            if (isset($this->param['prefix'])) {
                $this->redis->setOption(Redis::OPT_PREFIX, $this->param['prefix']);
            }

            //キャッシュ生存時間
            if (isset($this->param['cache_ttl'])) {
                $this->default_cache_ttl = $this->param['cache_ttl'];
            }

        }
    }

    /**
     * @see _KVSAccess::get()
     */
    public function get($key, _KVSAccess $KVSAccess = null)
    {
        $this->connectionFactory();

        $ret = false;
        if (!is_null($KVSAccess)) {
            $ret = $KVSAccess->get($key);
        }
        if ($ret === false) {
            $ret = $this->redis->get($key);
        }

        return $ret;
    }

    /**
     * @see _KVSAccess::set()
     */
    public function set($key, $value, $ttl = null)
    {
        if ($ttl === null) {
            $ttl = $this->default_cache_ttl;
        }

        $this->connectionFactory();

        if ($ttl > 0) {
            return $this->redis->setex($key, $ttl, $value);
        } else {
            return $this->redis->set($key, $value);
        }
    }

    /**
     * @see _KVSAccess::remove()
     */
    public function remove($key)
    {
        $this->connectionFactory();

        return $this->redis->delete($key);
    }
    /**
     * キーをパターン指定で取得して一括削除
     *
     * @param $key_pattern hoge*のようにパターンをワイルドカード指定
     */
    public function removeKeys($key_pattern)
    {
        $this->connectionFactory();

        //対象パターンに合致するキーを取得
        $target_keys = $this->redis->keys($key_pattern);

        //トランザクション開始
        $redis_multied = $this->redis->multi();

        //Redis::OPT_PREFIXでprefixが付いていると正しく削除できない
        //prefixがlo:だとするとlo:lo:hogeを削除しにいく
        if (strlen($this->param['prefix']) > 0) {
            $redis_multied->setOption(Redis::OPT_PREFIX, null);
        }

        //キーを削除
        foreach($target_keys as $key) {
            $redis_multied->delete($key);
        }

        //prefix指定を元に戻す
        if (strlen($this->param['prefix']) > 0) {
            $redis_multied->setOption(Redis::OPT_PREFIX, $this->param['prefix']);
        }
        return $redis_multied->exec();
    }
    /**
     * @see _KVSAccess::lock()
     */
    public function lock(
        $key,
        $ttl = self::DEFAULT_LOCK_TTL,
        $try = self::DEFAULT_LOCK_TRY,
        $lock_interval = self::DEFAULT_LOCK_TRY_INTERVAL
    ) {
        $this->connectionFactory();

        $lock_key = self::LOCK_PREFIX . $key;
        $cnt = 0;
        while ($cnt++ < $try) {
            $ret = $this->redis->incr($lock_key, 1);
            if ($ret === false) {
                $this->set($lock_key, 0, $ttl);
                $ret = $this->redis->incr($lock_key, 1);
            }
            if ($ret == 1) {
                // 成功
                return;
            } else {
                // 失敗
                $this->unlock($key);
                sleep($lock_interval);
            }
        }

        throw new Exception_KVS("kvs lock failed : key=$key");
    }

    /**
     * @see _KVSAccess::unlock()
     */
    public function unlock($key)
    {
        $this->connectionFactory();

        $lock_key = self::LOCK_PREFIX . $key;

        return $this->redis->decr($lock_key, 1);
    }

    /**
     * 全削除
     */
    public function flush()
    {
        $this->connectionFactory();

        return $this->redis->flushDB();
    }

    ///////////////////////////
    /**
     *
     * データが存在するか確認する
     * @param  $key
     */
    public function isCached($key)
    {
        $this->connectionFactory();

        return $this->redis->exists($key);
    }

    /**
     *
     * TTLを取得する
     * @param  $key
     */
    public function ttl($key)
    {
        $this->connectionFactory();

        return $this->redis->ttl($key);
    }

    /**
     *
     * タイムアウト値を設定する
     * @param  $key
     * @param  $ttl
     */
    public function setTimeout($key , $ttl)
    {
        $this->connectionFactory();

        return $this->redis->setTimeout($key, $ttl);
    }

    ///////////////////////////
    // list
    /**
     *
     * リスト型への追加
     * @param  $key
     * @param  $val
     * @param  $limit
     */
    public function add2List($key, $val, $limit = 10)
    {
        $this->connectionFactory();

        $sizeof = $this->redis->lSize($key);
        if ($sizeof < $limit) {
            $this->redis->lPush($key , $val);
        } else {
            //トランザクション
            $this->redis->multi()
            ->rPop($key)
            ->lPush($key , $val)
            ->exec();
        }

    }

    /**
     *
     * リスト型の取得
     * @param  $key
     */
    public function getList($key)
    {
        $this->connectionFactory();

        return $this->redis->lRange($key, 0, -1);
    }

    /**
     *
     * リスト型の取得(総件数付き)
     * @param  $key
     * @param  $start
     * @param  $end
     */
    public function getListWithSize($key, $start = 0, $end = -1)
    {
        $this->connectionFactory();

        return $this->redis->multi()->lRange($key, $start, $end)->lSize($key)->exec();
    }

    /**
     *
     * リスト型の再生成
     * @param  $key
     * @param  $values
     */
    public function regenerateList($key, $values)
    {
        $this->connectionFactory();

        $redis_multied = $this->redis->multi();

        //キーを削除
        $redis_multied->delete($key);
        foreach ($values as $val) {
            $redis_multied->rPush($key, $val);
        }

        return $redis_multied->exec();
    }

    ///////////////////////////
    // hash
    /**
     *
     * ハッシュ型に追加する
     * @param  $key
     * @param  $hashKey
     * @param  $val
     */
    public function add2Hash($key, $hashKey, $val)
    {
        $this->connectionFactory();

        return $this->redis->hSet($key, $hashKey, $val);
    }

    /**
     *
     * ハッシュ型の値をインクリメントする
     * @param  $key
     * @param  $hashKey
     * @param  $incVal
     */
    public function incHashVal($key, $hashKey, $incVal = 1)
    {
        $this->connectionFactory();

        return $this->redis->hIncrBy($key, $hashKey, $incVal);
    }

    public function hIncrBy($key, $hashKey, $incVal = 1)
    {
        $this->connectionFactory();

        return $this->redis->hIncrBy($key, $hashKey, $incVal);
    }

    /**
     *
     * ハッシュ型の値を取得する
     * @param  $key
     * @param  $is_sort
     */
    public function getAllHash($key, $is_sort = true)
    {
        $this->connectionFactory();

        $tmp_arry = $this->redis->hGetAll($key);

        if (!$tmp_arry) {
            return false;
        }

        if ($is_sort) {
            arsort($tmp_arry);
        }

        return $tmp_arry;
    }

    /**
     *
     * ハッシュ型の値をハッシュキーを指定して取得する
     * @param  $key
     * @param  $hash_key
     * @param  $is_sort
     */
    public function getHash($key, $hash_key, $is_sort = true)
    {
        $this->connectionFactory();

        $tmp_arry = $this->redis->hGet($key, $hash_key);

        if (!$tmp_arry) {
            return false;
        }
        return $tmp_arry;
    }

    ///////////////////////////
    // zRank
    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $score
     * @param  $value
     */
    public function zAdd($key, $score, $value)
    {
        $this->connectionFactory();

        return  $this->redis->zAdd($key, $score, $value);

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $score
     * @param  $value
     */
    public function zIncrBy($key, $score, $value)
    {
        $this->connectionFactory();

        return  $this->redis->zIncrBy($key, $score, $value);

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $value
     * @return number
     */
    public function zRank($key, $value)
    {
        $this->connectionFactory();

        return  $this->redis->zRank($key, $value)+ 1;//0オリジンなので

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $value
     * @return number
     */
    public function zRevRank($key, $value)
    {
        $this->connectionFactory();

        return  $this->redis->zRevRank($key, $value) + 1;//0オリジンなので

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $value
     */
    public function zScore($key, $value)
    {
        $this->connectionFactory();

        return  $this->redis->zScore($key, $value);

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $start
     * @param  $end
     * @param  $withscores
     */
    public function zRange($key, $start, $end, $withscores = true)
    {
        $this->connectionFactory();
        if ($withscores) {
            return  $this->redis->zRange($key,  $start, $end, $withscores);
        } else {
            return  $this->redis->zRange($key,  $start, $end);
        }
    }

    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $start
     * @param  $end
     * @param  $limit
     */
    public function zRangeByScore($key, $start, $end, $limit = array())
    {
        $this->connectionFactory();
        if (!empty($limit)) {
            return  $this->redis->zRangeByScore($key,  $start, $end, $limit);
        } else {
            return  $this->redis->zRangeByScore($key,  $start, $end);
        }
    }

    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $start
     * @param  $end
     * @param  $withscores
     */
    public function zRevRange($key, $start, $end, $withscores = true)
    {
        $this->connectionFactory();
        if ($withscores) {
            return  $this->redis->zRevRange($key,  $start, $end, $withscores);
        } else {
            return  $this->redis->zRevRange($key,  $start, $end);
        }
    }

    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $value
     */
    public function zDelete($key, $value)
    {
        $this->connectionFactory();

        return  $this->redis->zDelete($key, $value);

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $start
     * @param  $end
     */
    public function zCount($key, $start, $end)
    {
        $this->connectionFactory();

        return  $this->redis->zCount($key, $start, $end);

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     */
    public function zSize($key)
    {
        $this->connectionFactory();

        return  $this->redis->zSize($key);

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $value
     */
    public function lPush($key, $value)
    {
        $this->connectionFactory();

        return  $this->redis->lPush($key, $value);

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     */
    public function lSize($key)
    {
        $this->connectionFactory();

        return  $this->redis->lSize($key);

    }

    /**
     * キー、インデックス指定の値取得
     *
     * @param $key キー
     * @param $index インデックス
     */
    public function lGet($key, $index)
    {
        $this->connectionFactory();

        return  $this->redis->lGet($key, $index);
    }

    /**
     * リストから値を削除
     *
     * @param $key
     * @param $value
     * @param $count
     */
    public function lRem($key, $value, $count = 0)
    {
        $this->connectionFactory();

        return $this->redis->lRem($key, $value, $count);
    }
    /**
     * 一意な値を先頭に追加
     *
     * @param  $key
     * @param  $value
     */
    public function lPush_Unique($key, $value)
    {
        $this->connectionFactory();

        return $this->redis->multi()
            ->lRem($key, $value, 0)
            ->lPush($key , $value)
            ->exec();
    }
    /**
     * リストから値(複数)を削除
     *
     * @param $key
     * @param $values
     */
    public function lRem_Values($key, $values)
    {
        $this->connectionFactory();

        $redis_multied = $this->redis->multi();

        foreach ($values as $val) {
            $redis_multied->lRem($key, $val, 0);
        }

        return $redis_multied->exec();
    }

    //////////////////////////////

    /**
     *
     * Enter description here ...
     * @param  $key
     */
    public function hLen($key)
    {
        $this->connectionFactory();

        return  $this->redis->hLen($key);

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     */
    public function hGetAll($key)
    {
        $this->connectionFactory();

        return  $this->redis->hGetAll($key);

    }

    public function hGet($key, $member_key)
    {
        $this->connectionFactory();

        return  $this->redis->hGet($key, $member_key);

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $param
     */
    public function hMset($key, $param)
    {
        $this->connectionFactory();

        return  $this->redis->hMset($key, $param);

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     */
    public function hDel($key, $member_key)
    {
        $this->connectionFactory();

        return  $this->redis->hDel($key, $member_key);

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     */
    public function hKeys($key)
    {
        $this->connectionFactory();

        return  $this->redis->hKeys($key);

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $member_key
     * @param  $value
     */
    public function hSet($key, $member_key, $value)
    {
        $this->connectionFactory();

        return $this->redis->hSet($key, $member_key, $value);

    }

    /**
     *
     * @param  $key
     * @param  $member_key
     * @param  $value
     */
    public function hSetNx($key, $member_key, $value)
    {
        $this->connectionFactory();

        return $this->redis->hSetNx($key, $member_key, $value);

    }




    /**
     *
     * Enter description here ...
     * @param  $key_arry
     * @param  $member_key
     * @param  $value
     */
    public function hSet_multi($key_arry, $member_key, $value)
    {
        $this->connectionFactory();

        $redis_multied = $this->redis->multi();

        foreach ($key_arry as $key) {
            $redis_multied->hSet($key, $member_key, $value);
        }

        $redis_multied->exec();

    }

    /**
     *
     * Enter description here ...
     * @param  $key_arry
     * @param  $member_key
     */
    public function hDel_multi($key_arry, $member_key)
    {
        $this->connectionFactory();

        $redis_multied = $this->redis->multi();

        foreach ($key_arry as $key) {
            $redis_multied->hDel($key, $member_key);
        }

        $redis_multied->exec();

    }

    /////////////////


    /**
     * 複数のkeyにlPushする。（トランザクション付き）
     *
     * @param  $key_arry
     * @param  $value
     */
    public function lPush_multi($key_arry, $value)
    {
        $this->connectionFactory();

        $redis_multied = $this->redis->multi();

        foreach ($key_arry as $key) {
            $redis_multied->lPush($key, $value);
        }

        $redis_multied->exec();

    }

    /**
     * 複数のkeyにlRemする。（トランザクション付き）
     *
     * @param  $key_arry
     * @param  $value
     */
    public function lRem_multi($key_arry, $value)
    {
        $this->connectionFactory();

        $redis_multied = $this->redis->multi();

        foreach ($key_arry as $key) {
            $redis_multied->lRem($key, $value, 0);
        }

        $redis_multied->exec();

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $start
     * @param  $end
     */
    public function lRange($key, $start, $end)
    {
        $this->connectionFactory();

        return  $this->redis->lRange($key, $start, $end);

    }

    ////////////////////////////////////////

    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $value
     */
    public function sAdd($key, $value)
    {
        $this->connectionFactory();

        return  $this->redis->sAdd($key, $value);

    }

    /**
     *
     * Enter description here ...
     * @param  $key
     * @param  $value
     */
    public function sIsMember($key, $value)
    {
        $this->connectionFactory();

        return  $this->redis->sIsMember($key, $value);

    }

    /////////////////////

    /**
     * キーの列挙
     *
     * @param $str
     */
    public function keys($str = '*')
    {
        $this->connectionFactory();

        return  $this->redis->keys($str);

    }

    /**
     * トランザクションの開始
     *
     * @return Redisインスタンス
     */
    public function startTransaction()
    {
        $this->connectionFactory();

        return  $this->redis->multi();
    }

    /**
     *
     * Enter description here ...
     * @return Redisインスタンス
     */
    public function multi()
    {
        return  $this->startTransaction();
    }

    /**
     *
     * Enter description here ...
     */
    public function exec()
    {
        return  $this->redis->exec();
    }

    /**
     *
     * Enter description here ...
     */
    public function discard()
    {
        return  $this->redis->discard();
    }

    /**
     *
     * Enter description here ...
     * @param  $key
     */
    public function exists($key)
    {
        $this->connectionFactory();

        return  $this->redis->exists($key);

    }

}
