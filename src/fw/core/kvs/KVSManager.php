<?php
/**
 * KVSマネージャ
 *
 * @package    fw.core.kvs
 * @author     Dai-Yamamoto
 * @copyright  (c) Copyright 2012 O-two, Inc. . All rights reserved.
 */

require_once __DIR__ . '/../exception/Exception_KVS.php';

/**
 * KVSマネージャ
 *
 * @package    fw.core.kvs
 */
class KVSManager
{
    /** KVS接続情報 */
    private static $kvs_config_map = array();

    /** KVSアクセスプール */
    private static $kvsaccess_map = array();

    /**
     * KVS接続設定
     *
     * @param   $kvs_config_map KVS接続情報
     */
    public static function configure($kvs_config_map)
    {
        self::$kvs_config_map = $kvs_config_map;
    }

    /**
     * KVSアクセスクラス取得
     *
     * @param   $key    識別子
     * @return  KVSアクセスクラス
     * @throws  Exception_KVS
     */
    public static function getKVS($key)
    {
        // KVS接続判定
        if (!isset(self::$kvsaccess_map[$key])) {
            // KVS未接続の場合、KVSアクセスクラス取得

            if (isset(self::$kvs_config_map[$key])) {
                $config = self::$kvs_config_map[$key];
            } else {
                throw new Exception_KVS('invalid kvs config');
            }

            if (!isset($config['enable']) ||  $config['enable'] !== false) {
                if (isset($config['class_file']) && isset($config['class_name'])) {
                    // クラスが指定されている場合はクラス優先
                    $class_name = $config['class_name'];
                    $class_file = BASE_DIR . $config['class_file'];

                } elseif (isset($config['driver'])) {
                    // driver毎のKVSアクセスクラス取得
                    switch (strtolower($config['driver'])) {
                        case 'memcache':
                            $class_name = 'KVSAccess_Memcache';
                            break;

                        case 'apc':
                            $class_name = 'KVSAccess_APC';
                            break;

                        case 'redis':
                            $class_name = 'KVSAccess_Redis';
                            break;

                        case 'nocache':
                            $class_name = 'KVSAccess_Nocache';
                            break;

                        default:
                            throw new Exception_KVS('not supported driver : ' . $config['driver']);
                    }
                    $class_file = __DIR__ . '/' . $class_name.'.php';

                } else {
                    throw new Exception_KVS('invalid kvs config');
                }

                // KVSアクセスクラスのプール
                require_once $class_file;
                self::$kvsaccess_map[$key] = new $class_name(set($config['param']));
            } else {
                self::$kvsaccess_map[$key] = null;
            }

        }

        return self::$kvsaccess_map[$key];
    }
}
