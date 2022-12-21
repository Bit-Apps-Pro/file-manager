<?php

use BitApps\FM\Config;
use BitApps\FM\Core\Database\Blueprint;
use BitApps\FM\Core\Database\Migration;
use BitApps\FM\Core\Database\Schema;
use BitApps\FM\Core\Database\Connection as DB;


if (!defined('ABSPATH')) {
    exit;
}

final class PluginOptions extends Migration
{
    public function up()
    {
        Schema::create(
            'flow_logs',
            function (Blueprint $table) {
                $table->id();
                $table->string('step');
                $table->enum('status', ['success', 'error', 'failed']);
                $table->string('message')->nullable()->default(null);
                $table->longtext('input')->nullable()->default(null);
                $table->longtext('output')->nullable()->default(null);
                $table->tinyint('retry');
                $table->bigint('flow_id', 20)->unsigned()->foreign('flows', 'id')->onDelete()->cascade();
                $table->string('process_id')->foreign('processes', 'id')->onDelete()->cascade();
                $table->timestamps();
            }
        );
    }

    public function down()
    {
        $pluginOtions = [
            Config::withPrefix('db_version'),
            Config::withPrefix('installed'),
            Config::withPrefix('version'),
        ];

        DB::query(
            DB::prepare(
                "DELETE FROM `" . DB::wpPrefix() . "options` WHERE option_name in (" . implode(",", array_map(function () { return "%s"; }, $pluginOtions)). ")",
                $pluginOtions
            )
        );
    }
}
