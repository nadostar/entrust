<?php

class MasterDataAccess
{
    private static $DatabaseAccess = null;
    private static $now_time = null;
    private static $data_pool = array();

    public static function configure(_DatabaseAccess $DatabaseAccess)
    {
        self::$DatabaseAccess = $DatabaseAccess;
        self::$now_time = NOW_TIME;
    }

    public static function getMasterDataOne($table, $condition = null, $condition_param_map = null, $order = null, $is_filter = true)
    {
        $record_array = self::getMasterDataArray($table, $condition, $condition_param_map, $order, $is_filter);
        if (!isset($record_array[0])) {
            return false;
        }

        return $record_array[0];
    }

    public static function getMasterDataArray($table, $condition = null, $condition_param_map = null, $order = null, $is_filter = true)
    {
        // SQLの構築
        $query = "SELECT *, UNIX_TIMESTAMP(start_date) AS start_date_unix, UNIX_TIMESTAMP(end_date) AS end_date_unix FROM $table";
        if (!is_null($condition)) {
            $query .= " WHERE $condition";
        }
        if (is_null($condition_param_map)) {
            $condition_param_map = array();
        }
        if (!is_null($order)) {
            $query .= " ORDER BY $order";
        }

        // プールするためのキーを生成
        $pool_key = md5($query . print_r($condition_param_map, true));

        if (isset(self::$data_pool[$pool_key])) {
            return self::$data_pool[$pool_key];
        }

        $record_array = self::$DatabaseAccess->selectArray($query, $condition_param_map);

        if ($is_filter) {
            $record_array = self::filterActiveRecord($record_array);
        }

        foreach ($record_array as $k => $v) {
            unset($record_array[$k]['start_date_unix']);
            unset($record_array[$k]['end_date_unix']);
        }

        // 結果をプール
        self::$data_pool[$pool_key] = $record_array;

        return $record_array;
    }
    /**
     * 検索結果の連想配列の取得
     *
     * @param   $table                  テーブル名
     * @param   $key                    連想配列のキー
     * @param   $condition              検索クエリ
     * @param   $condition_param_map    クエリパラメータ連想配列
     * @param   $order                  ソート順序
     * @param   $is_filter              有効期間でのフィルタリング（名称取得ならfalseに設定を想定）
     * @param   $cache_key              明示的にキャッシュする際のキー（指定しな場合はSQLをハッシュ化したものが採用される。）
     * @return  マスターデータ連想配列
     */
    public static function getMasterDataMap($table, $key, $condition = null, $condition_param_map = null, $order = null, $is_filter = true)
    {
        $record_array = self::getMasterDataArray($table, $condition, $condition_param_map, $order, $is_filter);

        $record_map = array();
        foreach ($record_array as $record) {
            $record_map[$record[$key]] = $record;
        }

        return $record_map;
    }

    /**
     * 検索結果の連想配列のマルチマップ取得
     *
     * @param   $table                  テーブル名
     * @param   $key                    連想配列のキー
     * @param   $condition              検索クエリ
     * @param   $condition_param_map    クエリパラメータ連想配列
     * @param   $order                  ソート順序
     * @param   $is_filter              有効期間でのフィルタリング（名称取得ならfalseに設定を想定）
     * @return  マスターデータ連想配列
     */
    public static function getMasterDataMMap($table, $key, $condition = null, $condition_param_map = null, $order = null, $is_filter = true)
    {
        $record_array = self::getMasterDataArray($table, $condition, $condition_param_map, $order, $is_filter);
        $record_map = array();
        foreach ($record_array as $record) {
            $record_map[$record[$key]][] = $record;
        }

        return $record_map;
    }

    /**
     * 有効期間内レコード情報に絞る
     *
     * @param   $record_array   レコード情報配列
     * @return  有効期間内レコード情報配列
     */
    public static function filterActiveRecord($record_array)
    {
        foreach ($record_array as $index => $record) {
            // 期間以外の場合
            if (!(($record['start_date_unix'] <= self::$now_time || $record['start_date_unix'] === null) &&
                ($record['end_date_unix'] === null || $record['end_date_unix'] > self::$now_time))) {
                unset($record_array[$index]);
            }
        }

        $record_array = array_merge($record_array);

        return $record_array;
    }

    /**
     * 有効期間判定の対象時間設定
     *
     * @param $time タイムスタンプ
     */
    public static function setNowTime($time)
    {
        self::$now_time = $time;
    }

    /**
     * 有効期間判定の対象時間取得
     *
     * @return タイムスタンプ
     */
    public static function getNowTime()
    {
        return self::$now_time;
    }


}
