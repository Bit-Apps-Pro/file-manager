<?php

use BitApps\FM\Config;

use function BitApps\FM\Functions\view;

use BitApps\FM\Providers\InstallerProvider;

\defined('ABSPATH') || exit();

function bfmActivate()
{
    include_once BFM_BASEDIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

    $installerProvider = new InstallerProvider();
    $installerProvider->register();

    do_action(Config::withPrefix('activate'));

    // Creating necessary folders for library file manager
    $uploadDir = wp_upload_dir();
    $index     = $uploadDir['basedir'] . DS . 'file-manager' . DS . 'index.html';
    wp_mkdir_p(\dirname($index));

    // Creating index file
    if (!file_exists($index)) {
        $fp = fopen($index, 'a');
        fwrite($fp, ' ');
        fclose($fp);
    }
}

function bfmUninstall()
{
    include_once BFM_BASEDIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    $installerProvider = new InstallerProvider();
    $installerProvider->register();
    do_action(Config::withPrefix('uninstall'));
}

function bfmDeactivate()
{
    include_once BFM_BASEDIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

    $installerProvider = new InstallerProvider();
    $installerProvider->register();
    do_action(Config::withPrefix('deactivate'));
}

function bfmLoaded()
{
    do_action('file_manager_init');
    do_action('bit_fm_loaded');

    // Including elFinder class
    include_once BFM_FINDER_DIR . 'php' . DS . 'autoload.php';

    // Autoload vendor files.
    if (!is_readable(BFM_BASEDIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload_packages.php')) {
        error_log('Failed to load File Manager. Cause: autoload does not exists');

        return;
    }

    include_once BFM_BASEDIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload_packages.php';
    include_once BFM_BASEDIR . 'backend' . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'common.php';

    // Initialize the plugin.
    BitApps\FM\Plugin::load();
}

// Simple function API to invoke the file manager about anywhere
if (!\function_exists('file_manager_frontend')) {
    function file_manager_frontend()
    {
        if (!is_user_logged_in()) {
            return;
        }

        view('finder');
    }
}
