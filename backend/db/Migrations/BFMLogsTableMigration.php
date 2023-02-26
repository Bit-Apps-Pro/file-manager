<?php

use BitApps\FM\Core\Database\Blueprint;
use BitApps\FM\Core\Database\Migration;
use BitApps\FM\Core\Database\Schema;

if (! \defined('ABSPATH')) {
    exit;
}

final class BFMLogsTableMigration extends Migration
{
    public function up()
    {
        Schema::create(
            'log',
            function (Blueprint $table) {
                $table->id();
                $table->int('user_id', 11);
                $table->string('operation_id')->length(32);
                $table->string('file_path')->length(1024);
                $table->datetime('time');
            }
        );
    }

    public function down()
    {
        Schema::drop('log');
    }
}
