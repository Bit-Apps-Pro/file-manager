<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Model\Log;

\defined('ABSPATH') or exit();

class Logger
{
    public function save($command, $b, $c, $d, $e)
    {
        /**
         * download: zipdl,file
         * rename: rename
         * view: get
         * update: put
         */
        error_log("command: $command");
        // echo json_encode([$command, $b, $c, $d, $e]);exit;
        // var_dump($command, \array_keys($b['changed']),$c['target'],\get_class($d),\get_class($e));
        $log = new Log();
    }
}
