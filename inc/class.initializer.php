<?php
defined('ABSPATH') || die();

if (!class_exists('BFMSettingsInitializer')) :
    class BFMSettingsInitializer
    {

        public static $settings = [];

        static function init_settings()
        {
            if (!get_option("file_manager_permissions")) {
                return;
            }

            update_option(
                'file_manager_permissions',
                FileManagerPermissionSettings::defaultPermissions(),
                'yes'
            );
        }
    }
endif;
