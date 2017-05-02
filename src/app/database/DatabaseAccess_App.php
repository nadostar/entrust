<?php
/**
 * 응용 프로그램 데이터베이스 엑세스 클래스.
 *
 * @package    app.database
 * @author     xing
 */

require __DIR__ . '/../../fw/core/database/DatabaseAccessMySQL.php';

class DatabaseAccess_App extends DatabaseAccessMySQL {
	protected $driver_options_array = array(
	);
}
