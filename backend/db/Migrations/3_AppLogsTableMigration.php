<?php

use BitApps\FM\Core\Database\Blueprint;
use BitApps\FM\Core\Database\Migration;
use BitApps\FM\Core\Database\Schema;

if (!defined('ABSPATH')) {
    exit;
}

final class AppLogsTableMigration extends Migration
{
    public function up()
    {
        Schema::create(
            'app_logs',
            function (Blueprint $table) {
                $table->id();
                $table->string('type');
                $table->json('message');
                $table->bigint('flow_id', 20)->unsigned()->foreign('flows', 'id')->onDelete()->restrict();
                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::drop('logs');
    }
}
