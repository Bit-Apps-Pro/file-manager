<?php

use BitApps\FM\Core\Database\Blueprint;
use BitApps\FM\Core\Database\Migration;
use BitApps\FM\Core\Database\Schema;

if (!defined('ABSPATH')) {
    exit;
}

final class AppAuthTableMigration extends Migration
{
    public function up()
    {
        Schema::create(
            'auths',
            function (Blueprint $table) {
                $table->id();
                $table->string('app');
                $table->string('type');
                $table->json('data');
                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::drop('logs');
    }
}
