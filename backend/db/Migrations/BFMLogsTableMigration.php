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
            'logs',
            function (Blueprint $table) {
                $table->id();
                $table->int('user_id', 11);
                $table->string('command')->length(32);
                $table->longtext('details');
                $table->datetime('created_at');
            }
        );
    }

    public function down()
    {
        Schema::drop('logs');
    }
}
