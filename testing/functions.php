<?php
// This file is to declare useful function to log data.

if (!function_exists('pr')) :
    function pr($obj)
    {
        echo "<pre>";
        print_r($obj);
        echo "</pre>";
    }
endif;

// Log to a file
if (!function_exists('pl')) :
    function pl($obj)
    {
        ob_start();
        print_r($obj);
        echo "\n--------------------------------- x ---------------------------------\n";
        $content = ob_get_clean();

        $log_file = plugin_dir_path(__FILE__) . 'log.txt';
        $fp = fopen($log_file, "a+");
        fwrite($fp, $content . file_get_contents($log_file));
        fclose($fp);
    }
endif;
