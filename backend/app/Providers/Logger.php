<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Model\Log;
use elFinderVolumeDriver;

\defined('ABSPATH') or exit();

class Logger
{
    public function log(...$args)
    {
        /**
         * download: zipdl,file
         * rename: rename
         * view: get
         * update: put
         */
        error_log(
            'bind: ' . $args[0] . ' >> count: ' . \func_num_args() . " ] \n"
            . print_r($args, true) . "\n"
            // . print_r($args, true) . "\n"
            // . print_r($finder, true) . "\n"
            // . print_r($volume, true) . "\n"
        );
        if ($volume instanceof elFinderVolumeDriver && !empty($args['target'])) {
            error_log("command: {$command} " . $volume->getPath($args['target']));
        } else {
            error_log("command: {$command} |");
        }
        error_log('command:result: ' . print_r($commandResult, true));
        // echo json_encode([$command, $b, $c, $d, $e]);exit;
        // var_dump($command, \array_keys($b['changed']),$c['target'],\get_class($d),\get_class($e));
        $log = new Log();
    }
}
