<?php

namespace BitApps\FM\Http\Controllers;

use BitApps\FM\Config;
use BitApps\WPKit\Http\Request\Request;
use BitApps\WPTelemetry\Telemetry\Telemetry;

class TelemetryPopupController
{
    public function filterTrackingData($additional_data)
    {
        return $additional_data;
    }

    public function handleTelemetryPermission(Request $request)
    {
        if ($request->isChecked == true) {
            Telemetry::report()->trackingOptIn();
            update_option(Config::VAR_PREFIX . 'old_version', Config::VERSION);

            return true;
        }

        Telemetry::report()->trackingOptOut();
        update_option(Config::VAR_PREFIX . 'old_version', Config::VERSION);

        return false;
    }

    public function isPopupDisabled()
    {
        $allowed = Telemetry::report()->isTrackingAllowed();
        if ($allowed == true) {
            return true;
        }

        $popupSkipped             = get_option(Config::VAR_PREFIX . 'tracking_skipped');
        $adminNoticeSkipped       = get_option(Config::VAR_PREFIX . 'tracking_notice_dismissed');
        $getOldPluginVersion      = get_option(Config::VAR_PREFIX . 'old_version');

        return (bool) (($popupSkipped== true || $adminNoticeSkipped==true) && $getOldPluginVersion === Config::VERSION);
    }
}
