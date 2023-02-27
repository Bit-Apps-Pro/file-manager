<?php

/**
 * @file logger.php
 *
 * @description This file will hold the logging functionality of the plugin
 *
 * */

// Security check
\defined('ABSPATH') || exit();

if (!\function_exists('fm_logger')) {
    /**
     * Logs file library file manager actions
     *
     * @param mixed $cmd
     * @param mixed $result
     * @param mixed $args
     * @param mixed $elfinder
     * */
    function fm_logger($cmd, $result, $args, $elfinder)
    {
        $log = sprintf("[%s] %s: \n", date('r'), strtoupper($cmd));

        foreach ($result as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $data = [];
            if (\in_array($key, ['error', 'warning'])) {
                $data[] = implode(' ', $value); // logs only error and warning.
            }
            $log .= sprintf(' %s(%s)', $key, implode(', ', $data));
        }
        $log .= "\n";

        $log = get_option('fm_log', '') . $log;
        update_option('fm_log', $log);
    }
}
