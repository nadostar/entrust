<?php
/**
 * local 환경정보
 *
 * @package    env
 * @author     xing
 */
define('BASE_DIR', realpath( '/Users/nhn/Documents/backlog/Github/entrust/' ).DIRECTORY_SEPARATOR);

class Env {

    const APP_URL = 'http://127.0.0.1:10010/';

    const APP_PATH = '/';

    const TEMPLATE_DIR = 'tmpl';
	
    const HSP_URL = "http://alpha-httpgw.hangame.com:18080/hsp/httpgw/nomad.json";
    
    const SURVEY_URL = "http://127.0.0.1:10010/api/survey/?r={accesskey}&esid={1}";
    
    const RECEIVE_URL = "http://127.0.0.1:10010/api/receive/?rs={receive}&esid={1}";

    /** 인증여부 */
    const IS_LOGIN_CHECK = true;

    const ACCESSKEY_SIZE = 16;
    
    const PAGE_LIST = 7;
	
    private static $database_config = array(
        'system' => array(
            'driver'        => 'mysql',
            'class_file'    => 'src/app/database/DatabaseAccess_App.php',
            'class_name'    => 'DatabaseAccess_App',
            'param'         => array(
                'host'      => '127.0.0.1',
                'port'      => '3306',
                'user'      => 'root',
                'password'  => 'xingli00',
                'database'  => 'survey_admin',
                'master'    => true,
            	'charset'   => 'utf8'
            )
        ),

        'master' => array(
            'driver'        => 'mysql',
            'class_file'    => 'src/app/database/DatabaseAccess_App.php',
            'class_name'    => 'DatabaseAccess_App',
            'param'         => array(
                'host'      => '127.0.01',
                'port'      => '3306',
                'user'      => 'root',
                'password'  => 'xingli00',
                'database'  => 'survey',
                'master'    => true,
            	'charset'   => 'utf8'
            )
        ),
    		
		'slave' => array(
			'driver'        => 'mysql',
			'class_file'    => 'src/app/database/DatabaseAccess_App.php',
			'class_name'    => 'DatabaseAccess_App',
			'param'         => array(
				'host'      => '127.0.0.1',
				'port'      => '3306',
				'user'      => 'root',
				'password'  => 'xingli00',
				'database'  => 'survey',
				'master'    => false,
				'charset'   => 'utf8'
			)
		),
    		
		'log' => array(
			'driver'        => 'mysql',
			'class_file'    => 'src/app/database/DatabaseAccess_App.php',
			'class_name'    => 'DatabaseAccess_App',
			'param'         => array(
				'host'      => '127.0.01',
				'port'      => '3306',
				'user'      => 'root',
				'password'  => 'xingli00',
				'database'  => 'survey_log',
				'master'    => true,
				'charset'   => 'utf8'
			)
		),
    );

    public static function getDatabaseConfig() {
        return self::$database_config;
    }

    /** KVS接続設定 */
    private static $kvs_config = array(
    		// redis
    		// 本番はの負荷を考慮してslaveに向けてます。
    		'redis' => array(
				'driver'        => 'redis',
				'class_file'    => 'src/fw/core/kvs/KVSAccess_Redis.php',
				'class_name'    => 'KVSAccess_Redis',
				'param'         => array(
					'host'      => '127.0.0.1',
					'port'      => '6379',
					'prefix'    => 'lo:'
				)
    		),
    
    		// redis マスター
    		'redis_master' => array(
				'driver'        => 'redis',
				'class_file'    => 'src/fw/core/kvs/KVSAccess_Redis.php',
				'class_name'    => 'KVSAccess_Redis',
				'param'         => array(
					'host'      => '127.0.0.1',
					'port'      => '6379',
					'prefix'    => 'lo:'
				)
    		),
    
    		// apc
    		'apc' => array(
				'driver'        => 'nocache',
    		)
    );
    
    /**
     * KVS接続設定取得
     *
     * @return KVS接続設定
     */
    public static function getKVSConfig() {
    	return self::$kvs_config;
    }
    
    /** ログ出力設定 */
    private static $log_config = array(
        'html' => array(
            'driver'        => 'html',
            'level'         => 0,
            'param'         => array()
        ),
    	'file_debug' => array(
    		'driver'    => 'file',
    		'level'     => 2,
    		'param'     => array(
    			'file'           => '/Users/nhn/Documents/www/logs/debug.log',
    			'single_line'    => true
    		)
    	),
        'file_warn' => array(
            'driver'    => 'file',
            'level'     => 4,
            'param'     => array(
                'file'           => '/Users/nhn/Documents/www/logs/warn.log',
                'single_line'    => true
            )
        ),
        'file_error' => array(
            'driver'        => 'file',
            'level'         => 5,
            'param'         => array(
                'file'      	=> '/Users/nhn/Documents/www/logs/error.log',
                'buffer'    	=> false,
            )
        ),
    );

    public static function getLogConfig() {
        return self::$log_config;
    }

    const SEND_URL_BASE = '/';


    public static $WEB_SERVER_ARRAY = array(
        '127.0.0.1',
    );

    public static $BATCH_SERVER_ARRAY = array(
        '127.0.0.1',

    );

    public static $ALLOWED_SERVER_PTN_ARRAY = array(
        '/^localhost$/',        //localhost-1
        '/^127\.0\.0\.1$/',     //127.0.0.1
    );

    /** web 재기동 명령 */
    public static $WEB_RESTART_COMMAND_ARRAY = array(
//        'cap invoke -l STDOUT -f /home/smap/deploy/config/smapweb.rb COMMAND="sudo /etc/init.d/httpd graceful" ROLES=staging_httpd',
    );
}
