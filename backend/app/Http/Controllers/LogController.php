<?php

namespace BitApps\FM\Http\Controllers;

use BitApps\FM\Config;
use BitApps\WPKit\Http\Request\Request;
use BitApps\WPKit\Http\Response;
use BitApps\FM\Http\Services\LogService;

final class LogController
{
    private $logger;

    public function __construct()
    {
        $this->logger = new LogService();
        $currentTime  = time();
        $logDeletedAt = Config::getOption('log_deleted_at', $currentTime);

        if ((abs($logDeletedAt - $currentTime) / DAY_IN_SECONDS) > 30) {
            $this->logger->deleteOlder();
        }
    }

    public function all(Request $request)
    {
        $pageNo = \intval($request->pageNo) ?? 1;
        $limit  = \intval($request->limit)  ?? 14;

        return Response::success($this->logger->all((($pageNo - 1) * $limit), $limit));
    }

    public function delete(Request $request)
    {
        if (!$request->has('id')) {
            return Response::error(['id' => 'log id is required'])->message('failed to delete log');
        }

        $id = $request->id;
        if (!\is_array($id)) {
            return Response::error(['id' => 'array of log id is required'])->message('failed to delete log');
        }

        $status = $this->logger->delete($id);
        if ($status) {
            return Response::success([])->message('log deleted successfully');
        }

        return Response::error([])->message('failed to delete log');
    }
}
