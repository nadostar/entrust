<?php

require_once __DIR__.'/../exception/Exception_Database.php';

/**
 * @package    fw.core.database
 */
class DatabaseManager {

	private static $database_config = array();

	private static $database_pool = array();

	private function __construct() {}

	public static function configure($database_config) {
		self::$database_config = $database_config;
	}

	public static function getDB($key) {
		if (!isset(self::$database_pool[$key])) {
			if (isset(self::$database_config[$key])) {
				$config = self::$database_config[$key];
			} else {
				throw new Exception_Database('invalid database config');
			}

			if (isset($config['class_file']) && isset($config['class_name'])) {
				$class_name = $config['class_name'];
				$class_file = BASE_DIR.$config['class_file'];

			} elseif (isset($config['driver'])) {
				switch (strtolower($config['driver'])) {
					case 'mysql':
						$class_name = 'DatabaseAccessMySQL';
						break;

					default:
						throw new Exception_Database('not supported driver : '.$config['driver']);
				}
				$class_file = __DIR__.'/'.$class_name.'.php';

			} else {
				throw new Exception_Database('invalid database config');
			}

			require_once $class_file;
			self::$database_pool[$key] = new $class_name($config);
		}

		return self::$database_pool[$key];
	}
}