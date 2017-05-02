<?php
/**
 * alpha 환경정보
 *
 * @package    env
 * @author     xing
 */
define('BASE_DIR', realpath( '/home1/irteam/html/pokopang_admin/' ).DIRECTORY_SEPARATOR);
define('BASE_DIR_ANIMAL', realpath( '/home1/irteam/html/pokopang_admin/www/data' ).DIRECTORY_SEPARATOR);

class Env {

    const APP_URL = 'http://133.186.133.231:10010/'; //

    const BANNER_URL = 'http://poko.dn.toastoven.net/smart/poko/Alpha/UpdateFile/';

    /** 관리 화면에서 클라이언트와 같은 POST 통신을하는 기능의 연결 */
    const SMAP_POST_URL = 'http://133.186.133.231:10010/';

    const APP_PATH = '/';

    const TEMPLATE_DIR = 'tmpl';

    const DATABASE_COUNT = 6;
    
    const IS_TEST = true;
    
    const HSP_URL = "http://httpgw.kakao.hangame.com:18080/hsp/httpgw/nomad.json";
    
    /** 인증여부 */
    const IS_LOGIN_CHECK = false;

    /** phpmyadmin */
    const TOOL_PHPMYADMIN_URL = '/phpmyadmin/';

    /** phpredisadmin */
    const TOOL_PHPREDISADMIN_URL = '/phpredisadmin/';

    /** rockmongo */
    const TOOL_ROCKMONGO_URL = '/rockmongo/';

    /** munin */
    const TOOL_MUNIN_URL = '';

    /** nagios */
    const TOOL_NAGIOS_URL = '';

    /** title **/
    const HEADER_TITLE = 'alpha';

    /** title_color **/
    const TITLE_COLOR = '#CCCC99';

    /** bg_color **/
    const BG_COLOR = '#336633';

    /** css type **/
    const CSS_TYPE = 2;

    /** 데이터베이스 연결 설정 */
    private static $database_config = array(
        'master' => array(
            'driver'        => 'mysql',
            'class_file'    => 'src/app/database/DatabaseAccess_App.php',
            'class_name'    => 'DatabaseAccess_App',
            'param'         => array(
                'host'      => '10.24.33.78',
                'port'      => '13306',
                'user'      => 'poco_svc',
                'password'  => 'poco!@#123',
                'database'  => 'ppdb',
                'master'    => true
            )
        ),
    		
		'master_0' => array(
    		'driver'        => 'mysql',
    		'class_file'    => 'src/app/database/DatabaseAccess_App.php',
    		'class_name'    => 'DatabaseAccess_App',
    		'param'         => array(
    			'host'      => '10.24.33.78',
    			'port'      => '13306',
    			'user'      => 'poco_svc',
    			'password'  => 'poco!@#123',
    			'database'  => 'ppdb',
    			'master'    => true
    		)
    	),
    		
    	'master_1' => array(
    			'driver'        => 'mysql',
    			'class_file'    => 'src/app/database/DatabaseAccess_App.php',
    			'class_name'    => 'DatabaseAccess_App',
    			'param'         => array(
    				'host'      => '10.24.33.78',
    				'port'      => '13306',
    				'user'      => 'poco_svc',
    				'password'  => 'poco!@#123',
    				'database'  => 'ppdb',
    				'master'    => true
    			)
    	),
    	
    	'master_2' => array(
    			'driver'        => 'mysql',
    			'class_file'    => 'src/app/database/DatabaseAccess_App.php',
    			'class_name'    => 'DatabaseAccess_App',
    			'param'         => array(
    				'host'      => '10.24.33.78',
    				'port'      => '13306',
    				'user'      => 'poco_svc',
    				'password'  => 'poco!@#123',
    				'database'  => 'ppdb',
    				'master'    => true
    			)
    	),
    	
    	'master_3' => array(
    			'driver'        => 'mysql',
    			'class_file'    => 'src/app/database/DatabaseAccess_App.php',
    			'class_name'    => 'DatabaseAccess_App',
    			'param'         => array(
    				'host'      => '10.24.33.78',
    				'port'      => '13306',
    				'user'      => 'poco_svc',
    				'password'  => 'poco!@#123',
    				'database'  => 'ppdb',
    				'master'    => true
    			)
    	),
    	
    	'master_4' => array(
    			'driver'        => 'mysql',
    			'class_file'    => 'src/app/database/DatabaseAccess_App.php',
    			'class_name'    => 'DatabaseAccess_App',
    			'param'         => array(
    				'host'      => '10.24.33.78',
    				'port'      => '13306',
    				'user'      => 'poco_svc',
    				'password'  => 'poco!@#123',
    				'database'  => 'ppdb',
    				'master'    => true
    			)
    	),
    	
    	'master_5' => array(
    			'driver'        => 'mysql',
    			'class_file'    => 'src/app/database/DatabaseAccess_App.php',
    			'class_name'    => 'DatabaseAccess_App',
    			'param'         => array(
    				'host'      => '10.24.33.78',
    				'port'      => '13306',
    				'user'      => 'poco_svc',
    				'password'  => 'poco!@#123',
    				'database'  => 'ppdb',
    				'master'    => true
    			)
    	),

        'slave' => array(
            'driver'        => 'mysql',
            'class_file'    => 'src/app/database/DatabaseAccess_App.php',
            'class_name'    => 'DatabaseAccess_App',
            'param'         => array(
                'host'      => '10.24.33.79',
                'port'      => '13306',
                'user'      => 'poco_svc',
                'password'  => 'poco!@#123',
                'database'  => 'ppdb',
                'master'    => false
            )
        ),
        
        'slave_0' => array(
        		'driver'        => 'mysql',
        		'class_file'    => 'src/app/database/DatabaseAccess_App.php',
        		'class_name'    => 'DatabaseAccess_App',
        		'param'         => array(
        			'host'      => '10.24.33.79',
        			'port'      => '13306',
        			'user'      => 'poco_svc',
        			'password'  => 'poco!@#123',
        			'database'  => 'ppdb',
        			'master'    => false
        		)
        ),
        
        'slave_1' => array(
        		'driver'        => 'mysql',
        		'class_file'    => 'src/app/database/DatabaseAccess_App.php',
        		'class_name'    => 'DatabaseAccess_App',
        		'param'         => array(
        			'host'      => '10.24.33.79',
        			'port'      => '13306',
        			'user'      => 'poco_svc',
        			'password'  => 'poco!@#123',
        			'database'  => 'ppdb',
        			'master'    => false
        		)
        ),
        
        'slave_2' => array(
        		'driver'        => 'mysql',
        		'class_file'    => 'src/app/database/DatabaseAccess_App.php',
        		'class_name'    => 'DatabaseAccess_App',
        		'param'         => array(
        			'host'      => '10.24.33.79',
        			'port'      => '13306',
        			'user'      => 'poco_svc',
        			'password'  => 'poco!@#123',
        			'database'  => 'ppdb',
        			'master'    => false
        		)
        ),
        
        'slave_3' => array(
        		'driver'        => 'mysql',
        		'class_file'    => 'src/app/database/DatabaseAccess_App.php',
        		'class_name'    => 'DatabaseAccess_App',
        		'param'         => array(
        			'host'      => '10.24.33.79',
        			'port'      => '13306',
        			'user'      => 'poco_svc',
        			'password'  => 'poco!@#123',
        			'database'  => 'ppdb',
        			'master'    => false
        		)
        ),
        
        'slave_4' => array(
        		'driver'        => 'mysql',
        		'class_file'    => 'src/app/database/DatabaseAccess_App.php',
        		'class_name'    => 'DatabaseAccess_App',
        		'param'         => array(
        			'host'      => '10.24.33.79',
        			'port'      => '13306',
        			'user'      => 'poco_svc',
        			'password'  => 'poco!@#123',
        			'database'  => 'ppdb',
        			'master'    => false
        		)
        ),
        
        'slave_5' => array(
        		'driver'        => 'mysql',
        		'class_file'    => 'src/app/database/DatabaseAccess_App.php',
        		'class_name'    => 'DatabaseAccess_App',
        		'param'         => array(
        			'host'      => '10.24.33.79',
        			'port'      => '13306',
        			'user'      => 'poco_svc',
        			'password'  => 'poco!@#123',
        			'database'  => 'ppdb',
        			'master'    => false
        		)
        ),
    );

    public static function getDatabaseConfig() {
        return self::$database_config;
    }


    /** 로그출력설정 */
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
    			'file'           => '/home1/irteam/logs/pokopang_admin/debug.log',
    			'single_line'    => true
    		)
    	),
        'file_warn' => array(
            'driver'    => 'file',
            'level'     => 4,
            'param'     => array(
                'file'           => '/home1/irteam/logs/pokopang_admin/warn.log',
                'single_line'    => true
            )
        ),
        'file_error' => array(
            'driver'        => 'file',
            'level'         => 5,
            'param'         => array(
                'file'      => '/home1/irteam/logs/pokopang_admin/error.log',
                'buffer'    => false,
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
    	'/^10\.77\.32\.217$/',     //10.77.32.217
    );

    const PERSON_GROUP_DATA_PATH = '/home1/irteam/deploy/files/';
    
    /** web 재기동 명령 */
    public static $WEB_RESTART_COMMAND_ARRAY = array(
//        'cap invoke -l STDOUT -f /home/smap/deploy/config/smapweb.rb COMMAND="sudo /etc/init.d/httpd graceful" ROLES=staging_httpd',
    );
}
