<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Dependencies\BitApps\WPKit\Http;

final class RequestType
{
    const API = 'api';

    const ADMIN = 'admin';

    const AJAX = 'ajax';

    const CRON = 'cron';

    const FRONTEND = 'frontend';

    /**
     * Returns if request is for specific $type.
     *
     * @param string $type admin | ajax | cron | api | frontend
     *
     * @return bool
     */
    public static function is($type)
    {
        switch ($type) {
            case self::ADMIN:
                return is_admin();

            case self::AJAX:
                return \defined('DOING_AJAX');

            case self::CRON:
                return \defined('DOING_CRON');

            case self::API:
                return \defined('REST_REQUEST');

            case self::FRONTEND:
                return (!is_admin() || \defined('DOING_AJAX')) && !\defined('DOING_CRON');
        }
    }
}
