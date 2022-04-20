<?php
/**
 *
 * @file logger.php
 * @description This file will hold the logging functionality of the plugin
 *
 * */

// Security check
defined('ABSPATH') || die();

if (!function_exists('fm_logger')):

/**
 *
 * @function logger
 *
 * Logs file library file manager actions
 *
 * */
    function fm_logger($cmd, $result, $args, $elfinder)
{

        global $FileManager;
        // error_log(print_r($result, true));
        $log['date'] = date('r');
        $log['cmd'] = strtoupper($cmd);
        foreach ($result as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $data = array();
            if (in_array($key, array('error', 'warning'))) {
                array_push($data, implode(' ', $value)); // logs only error and warning.
            }
            $log['key'] = $key;
            $log['err'] = $data;
        }
		
        $prev_logs = get_option('lfm_log', array());
		if(count($prev_logs)){
			if(count($prev_logs) == 1){
				$prev_logs[count($prev_logs)] = $log;	
			}else{
				$prev_logs[count($prev_logs)] = $log;	
			}
		}else{
			$prev_logs[] = $log;
		}

        update_option('lfm_log', $prev_logs);

    }

endif;
