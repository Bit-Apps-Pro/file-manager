<?php

use BitApps\FM\Core\Database\Blueprint;
use BitApps\FM\Core\Database\Migration;
use BitApps\FM\Core\Database\Schema;

if (! defined('ABSPATH')) {
    exit;
}

final class FlowsTableMigration extends Migration
{
    public function up()
    {
        Schema::create(
            'flows',
            function (Blueprint $table) {
                $table->id();
                $table->json('details');
                $table->boolean('status')->default(false);
                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::drop('flows');
    }
}
