<?php

use BitApps\FM\Config;
use BitApps\FM\Providers\InstallerProvider;

\defined('ABSPATH') || exit();

function bfmActivate()
{
    include_once BFM_BASEDIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

    $installerProvider = new InstallerProvider();
    $installerProvider->register();

    do_action(Config::withPrefix('activate'));

    // Initilizing the option to store logging
    if (!get_option('fm_log', false)) {
        add_option('fm_log', '');
    }

    // Creating necessary folders for library file manager
    $uploadDir = wp_upload_dir();
    $index     = $uploadDir['basedir'] . DS . 'file-manager' . DS . 'index.html';
    wp_mkdir_p(\dirname($index));

    // Creating indexfile
    if (!file_exists($index)) {
        $fp = fopen($index, 'a');
        fwrite($fp, ' ');
        fclose($fp);
    }

    // ------------------------------ Initilizing Statistical Data ------------------------------
    $statistics = [
        'start-time' => time(),
        'review'     => [
            'initial-popup'     => time() + 7 * 24 * 60 * 60,
            'popup-interval'    => 2          * 24 * 60 * 60,
            'most-recent-popup' => 0, // Last when the popup was triggered.
            'current-status'    => 0, // 1 = initial-popup, 2 = remind-me-later, 3 = already-provided-feedback, 4 = don't show this message
        ],
    ];
    // ------------------------------ Initilizing Statistical Data ENDS -------------------------

    // Logger table
    // global $wpdb;
    // $tablePrefix = $wpdb->prefix;
    // $sql         = "
    // CREATE TABLE {$tablePrefix}fm_log (
    //     id int(11) NOT NULL,
    //     user_id int(11) NOT NULL,
    //     operation_id varchar(32) NOT NULL,
    //     file_path varchar(1024) NOT NULL,
    //     time datetime NOT NULL
    // ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    // ALTER TABLE {$tablePrefix}fm_log ADD PRIMARY KEY (id);
    // ALTER TABLE {$tablePrefix}fm_log MODIFY id int(11) NOT NULL AUTO_INCREMENT;
    // ";

    // include_once ABSPATH . 'wp-admin/includes/upgrade.php';

    // dbDelta($sql);

    BFMSettingsInitializer::init_settings();
}

function bfmUninstall()
{
    do_action(Config::withPrefix('uninstall'));
}

function bfmDeactivate()
{
    do_action(Config::withPrefix('deactivate'));
}

function bfmLoaded()
{
    do_action('file_manager_init');

    // Including elFinder class
    require_once BFM_FINDER_DIR . 'php' . DS . 'autoload.php';

    // Including Boot Starter
    require_once BFM_BASEDIR . DS . 'BootStart' . DS . 'BootStart.php';

    // Including other necessary files
    require_once BFM_BASEDIR . DS . 'inc/__init__.php';

    // Autoload vendor files.
    require_once BFM_BASEDIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

    // Initialize the plugin.
    BitApps\FM\Plugin::load();

    // After library Loaded
    // Activation Deactivation hook
}
