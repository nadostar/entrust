<?php
/**
 * auto load
 *
 * @package    fw.core
 * @author     xing
 */

function regist_autoload_dir($root_dir)
{
    spl_autoload_register(
        function ($class_name) use ($root_dir) {
            static $required_class_map = array();
            if (isset($required_class_map[$class_name]) && $required_class_map[$class_name]  === true) {
                return true;
            }



            if (preg_match_all('/_*([A-Za-z0-9]+)/', $class_name, $tmp_arry) > 0) {
                $matches_arry = $tmp_arry[1];
                if (!isset($matches_arry[0])) {
                    return false;
                }

                $class_type = strtolower(str_replace('_', '', $matches_arry[0]));


                if ( preg_match('/^_*[A-Za-z]+_([0-9A-Za-z_]+)$/', $class_name, $matches ) ){


                    if ( $class_type === 'action' ){

                        if ( $matches[1] === 'WithPager' || $matches[1] === 'App'  ){
                            $filename = $root_dir . '/' . $class_type . '/' . $class_name . '.php';

                        }
                        else {


                            $action_type = strtolower($matches[1]);



                            $filename = $root_dir . '/' . $class_type . '/' . $action_type . '/' . $class_name . '.php';

                        }
                    }
					//例外
                   else  if ( $class_type === 'exception' ){
                   		$tmp_arry = explode('_', $matches[1]);

                   		if ( !isset( $tmp_arry[0] ) || empty( $tmp_arry[0] )){

                   			return false;
                   		}
                    	$action_type = strtolower($matches[1]);

                    	$class_name = ucfirst($class_type).'_'.$tmp_arry[0];



                    	$filename = $root_dir . '/' . $class_type  . '/' . $class_name . '.php';



                    }

                    else {


                        $filename = $root_dir . '/' . $class_type . '/'. $class_name . '.php';

                    }
                }



                if (!empty($filename) && is_readable($filename)) {
                    require $filename;
                    $required_class_map[$class_name] = true;
                }
            }
        }
    );
}
