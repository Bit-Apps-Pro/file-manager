<?php

namespace BitApps\FM\Core\Http;

final class Request
{
    public static function Check($type)
    {
        switch ($type) {
        case 'admin':
            return is_admin();

        case 'ajax':
            return defined('DOING_AJAX');

        case 'cron':
            return defined('DOING_CRON');

        case 'api':
            return defined('REST_REQUEST');

        case 'frontend':
            return (! is_admin() || defined('DOING_AJAX')) && ! defined('DOING_CRON');
        }
    }
}
