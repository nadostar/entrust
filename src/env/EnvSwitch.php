<?php
/**
 * 현재 환경 설정 취득
 * 
 * 자동으로 local, dev, alpha, beta, real 구분
 *
 * @package    env
 * @author     xing
 */

function envSwitch()
{
    $hostname = $_SERVER["HTTP_HOST"];
    error_log("HTTP_HOST : ".$hostname, 0);

    if ( preg_match('/^133.186.133.231/', $hostname)) {
        return 'dev';
    }

    elseif(preg_match('/^10.99.194.87/', $hostname)){
    	return 'alpha';
    }
    
    elseif(preg_match('/^103.194.108.8/', $hostname)){
    	return 'beta';
    }
    
    elseif(preg_match('/^47.91.170.199/', $hostname)){
        return 'real';
    }

    elseif(preg_match('/^47.52.23.236/', $hostname)){
        return 'real';
    }

    return 'local';
}
