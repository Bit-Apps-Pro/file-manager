<?php
/**
 * Class For Database.
 *
 * @license GPL-2.0-or-later
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Dependencies\BitApps\WPKit\Migration;

/**
 * Helps to migrate tables on plugin activate.
 */
abstract class Migration
{
    /**
     * Migrate tables.
     */
    abstract public function up();

    /**
     * Drop tables, columns.
     */
    abstract public function down();
}
