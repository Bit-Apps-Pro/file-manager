<?php

namespace BitApps\FM\Http\Controllers;

use Automatic_Upgrader_Skin;
use BitApps\FM\Config;
use BitApps\WPKit\Http\Request\Request;
use BitApps\WPTelemetry\Telemetry\Telemetry;
use Plugin_Upgrader;

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
            if (current_user_can('install_plugins') && $request->has('tryPlugin')) {
                $this->maybeInstallPlugins($request->tryPlugin);
            }

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

        return (bool) (($popupSkipped == true || $adminNoticeSkipped == true) && $getOldPluginVersion === Config::VERSION);
    }

    private function maybeInstallPlugins($plugins)
    {
        if (!\is_array($plugins) || empty($plugins)) {
            return;
        }

        foreach ($plugins as $pluginSlug => $isAccepted) {
            if ($isAccepted) {
                $this->installPlugin(sanitize_text_field($pluginSlug));
            }
        }
    }

    private function installPlugin($slug)
    {
        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';
        if (!\function_exists('plugins_api')) {
            include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        }

        $pluginInfo = plugins_api(
            'plugin_information',
            [
                'slug'   => wp_unslash($slug),
                'fields' => ['short_description' => false,'description' => false,'sections' => false,'contributors' => false, 'ratings' => false,'screenshots' => false,'tags' => false,'versions' => false]
            ]
        );

        if (is_wp_error($pluginInfo)) {
            return $pluginInfo;
        }

        $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());

        $installStatus = $upgrader->install($pluginInfo->download_link);

        if (is_wp_error($installStatus)) {
            return $installStatus;
        }

        if ($installStatus === true) {
            $activationStatus = activate_plugin($upgrader->plugin_info(), '', false, true);

            if (is_wp_error($activationStatus)) {
                return $activationStatus;
            }

            return $activationStatus === null;
        }

        return $installStatus;
    }
}
