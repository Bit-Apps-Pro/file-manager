<?php

namespace BitApps\FM\Http\Services;

use BitApps\FM\Config;
use BitApps\WPDatabase\QueryBuilder;
use BitApps\FM\Model\Log;
use BitApps\FM\Plugin;
use DateTime;
use Throwable;

\defined('ABSPATH') || exit();

class LogService
{
    public function all($skip = 0, $take = 20)
    {
        $logs  = [];
        $count = 0;

        try {
            $logs  = Log::skip($skip)->take($take)->desc()->get();
            $count = Log::count();
        } catch (Throwable $th) {
            // throw $th;
        }

        return compact('count', 'logs');
    }

    public function save($command, $details)
    {
        $log             = new Log();

        $log->created_at = gmdate(QueryBuilder::TIME_FORMAT);
        $log->user_id    = Plugin::instance()->permissions()->currentUserID();
        $log->command    = $command;
        $log->details    = $details;

        return $log->save();
    }

    public function delete($id)
    {
        return Log::where('id', $id)->delete();
    }

    public function deleteOlder()
    {
        $logRetention = (int) (\defined('BFM_LOG_RETENTION') && BFM_LOG_RETENTION ? BFM_LOG_RETENTION : 30);
        if ($logRetention > 200) {
            $logRetention = 200;
        }

        $currentDate = new DateTime();

        $dateToDelete = date_sub($currentDate, date_interval_create_from_date_string($logRetention . ' days'));
        $dateToDelete = date_format($dateToDelete, QueryBuilder::TIME_FORMAT);

        Config::updateOption('log_deleted_at', time());

        return Log::where('created_at', '<', $dateToDelete)->delete();
    }
}
