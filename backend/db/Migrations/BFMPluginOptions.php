<?php

use BitApps\FM\Config;
use BitApps\WPKit\Database\Connection as DB;
use BitApps\WPKit\Database\Migration;

if (!\defined('ABSPATH')) {
    exit;
}

final class BFMPluginOptions extends Migration
{
    public function up()
    {
        Config::addOption('installed', time(), true);
        Config::updateOption('version', Config::VERSION_ID, true);
        Config::updateOption('db_version', Config::DB_VERSION, true);
    }

    public function down()
    {
        $pluginOptions = [
            Config::withPrefix('db_version'),
            Config::withPrefix('installed'),
            Config::withPrefix('version'),
            Config::withPrefix('preferences'),
            Config::withPrefix('permissions'),
            Config::withPrefix('log_deleted_at'),
        ];

        DB::query(
            DB::prepare(
                'DELETE FROM `' . DB::wpPrefix() . 'options` WHERE option_name in ('
                    . implode(
                        ',',
                        array_map(
                            function () {
                                return '%s';
                            },
                            $pluginOptions
                        )
                    ) . ')',
                $pluginOptions
            )
        );
    }
}
