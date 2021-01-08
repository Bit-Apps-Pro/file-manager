<?php
/**
 *
 * @file logger.php
 * @description This file will hold the logging functionality of the plugin
 *
 * */

// Security check
defined('ABSPATH') || die();

if(!function_exists('fm_logger')):

/**
 *
 * @function logger
 *
 * Logs file file manager actions
 *
 * */
function fm_logger($cmd, $result, $args, $elfinder) {

	global $FileManager;

	$log = sprintf("[%s] %s: \n", date('r'), strtoupper($cmd));

	foreach ($result as $key => $value) {
		if (empty($value)) {
			continue;
		}
		$data = array();
		if (in_array($key, array('error', 'warning'))) {
			array_push($data, implode(' ', $value)); // logs only error and warning.
		}
		$log .= sprintf(' %s(%s)', $key, implode(', ', $data));
	}
	$log .= "\n";

	$log = get_option('fm_log', '') . $log;
	update_option('fm_log', $log);

}

endif;
