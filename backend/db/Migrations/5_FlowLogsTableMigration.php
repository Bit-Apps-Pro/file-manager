<?php

use BitApps\FM\Core\Database\Blueprint;
use BitApps\FM\Core\Database\Migration;
use BitApps\FM\Core\Database\Schema;

if (!defined('ABSPATH')) {
    exit;
}

final class FlowLogsTableMigration extends Migration
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
        Schema::drop('logs');
    }
}
