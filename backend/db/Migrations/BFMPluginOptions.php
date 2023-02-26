<?php

use BitApps\FM\Config;
use BitApps\FM\Core\Database\Connection as DB;
use BitApps\FM\Core\Database\Migration;

if (!\defined('ABSPATH')) {
    exit;
}

final class BFMPluginOptions extends Migration
{
    public function up()
    {
        Config::addOption('db_version', Config::DB_VERSION, true);
        Config::addOption('installed', time(), true);
        Config::addOption('version', Config::VERSION_ID, true);
    }

    public function down()
    {
        $pluginOptions = [
            Config::withPrefix('db_version'),
            Config::withPrefix('installed'),
            Config::withPrefix('version'),
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
