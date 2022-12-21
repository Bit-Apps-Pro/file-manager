<?php

use BitApps\FM\Core\Database\Blueprint;
use BitApps\FM\Core\Database\Migration;
use BitApps\FM\Core\Database\Schema;

if (!defined('ABSPATH')) {
    exit;
}

final class ProcessTableMigration extends Migration
{
    public function up()
    {
        Schema::create(
            'processes',
            function (Blueprint $table) {
                $table->string('id')->unique()->primary();
                $table->enum('status', ['success', 'processing', 'failed'])->default('processing');
                $table->bigint('flow_id', 20)->unsigned()->foreign('flows', 'id')->onDelete()->cascade();
                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::drop('logs');
    }
}
