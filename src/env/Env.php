<?php
/**
 * 환경설정
 *
 * @package    env
 * @author     xing
 */

require __DIR__ . '/./EnvSwitch.php';


define('NOW_TIME', time());
define('ENCODE_TYPE', 'UTF-8');

$env = envSwitch();

$env_file = 'Env_' . $env . '.php';

require __DIR__ . '/' . $env_file;
