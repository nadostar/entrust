<?php

date_default_timezone_set("Asia/Seoul");

require_once __DIR__.'/../src/env/Env.php';
require_once __DIR__.'/../src/fw/core/action/ActionHandler.php';
require __DIR__ . '/../src/fw/core/autoload.php';
require_once __DIR__.'/../src/fw/core/util/Util_core.php';
require_once __DIR__.'/../src/fw/core/database/DatabaseManager.php';
require_once __DIR__.'/../src/fw/core/log/LogManager.php';

regist_autoload_dir(BASE_DIR . 'src/app');
regist_autoload_dir(BASE_DIR . 'src/fw/core');

try {
	DatabaseManager::configure(Env::getDatabaseConfig());
	LogManager::configure(Env::getLogConfig());
	
	$mode = set($_GET['mode']);
	$action = set($_GET['action']);
	LogManager::debug("mode=".$mode.", action=".$action);
	$Action = ActionHandler::getActionInstance($mode, $action);
	$Action->doAct();
	
} catch (Exception $e) {
	LogManager::error($e->getMessage());
	echo $e;
}