<?php

namespace BitApps\FM\Http\Controllers;

use BitApps\FM\Config;
use BitApps\FM\Core\Http\Request\Request;
use BitApps\FM\Core\Http\Response;
use BitApps\FM\Http\Services\LogService;

final class LogController
{
    private $logger;

    public function __construct()
    {
        $this->logger = new LogService();
        $currentTime = time();
        if ((abs(Config::getOption('log_deleted_at', $currentTime) - $currentTime ) / DAY_IN_SECONDS) > 30) {
            $this->logger->deleteOlder();
        }   
    }

    public function all()
    {
        return Response::success($this->logger->all() || []);
    }

    public function delete(Request $request)
    {
        if (!$request->has('id')) {
            return Response::error(['id' => 'log id is required'])->message('failed to delete log');
        }
        $id = $request->id;
        if (!is_array($id)) {
            return Response::error(['id' => 'array of log id is required'])->message('failed to delete log');
        }

        $status = $this->logger->delete($id);
        if ($status) {
            return Response::success([])->message('log deleted successfully');
        }

        return Response::error([])->message('failed to delete log');

    }
}
