<?php

use BitApps\WPDatabase\Blueprint;
use BitApps\WPDatabase\Schema;
use BitApps\WPKit\Migration\Migration;

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
