<?php

namespace BitApps\FM\Core;

if (! defined('ABSPATH')) {
    exit;
}

use BitApps\FM\Config;
use BitApps\FM\Core\Database\Operator as DBOperator;
use BitApps\FM\Core\Utils\Hooks;

/**
 * Class handling plugin activation, deactivation, uninstall
 *
 * @since 1.0.0
 */
final class Installer
{
    public function register()
    {
        Hooks::add(Config::withPrefix('activate'), [$this, 'activate']);
        Hooks::add(Config::withPrefix('deactivate'), [$this, 'deactivate']);
        Hooks::add(Config::withPrefix('uninstall'), [$this, 'uninstall']);
    }

    public static function activate($isNetworkActivation)
    {
        if (version_compare(PHP_VERSION, Config::REQUIRED_PHP_VERSION, '<')) {
            /* Str From WP install script */
            wp_die(
                esc_html__(
                    sprintf(
                        /* translators: 1: Current PHP version, 2: Version required by the uploaded plugin. */
                        __('The PHP version on your server is %1$s, however the uploaded plugin requires %2$s.'),
                        phpversion(),
                        Config::REQUIRED_PHP_VERSION
                    )
                ),
                esc_html__('Requirements Not Met')
            );
        }
        if (version_compare(get_bloginfo('version'), Config::REQUIRED_WP_VERSION, '<')) {
            wp_die(
                esc_html__(
                    sprintf(
                        /* translators: 1: Current WordPress version, 2: Version required by the uploaded plugin. */
                        __('Your WordPress version is %1$s, however the uploaded plugin requires %2$s.'),
                        get_bloginfo('version'),
                        Config::REQUIRED_WP_VERSION
                    ),
                    esc_html__('Requirements Not Met')
                )
            );
        }
        $installed = Config::getOption('installed');
        if ($installed) {
            $oldVersion = Config::getOption('version');
        }
        // if (!$installed || version_compare($oldVersion, Config::VERSION, '!=')) {
        if (! $installed || version_compare($oldVersion, Config::VERSION, '=')) {
            DBOperator::migrate();
        }
        update_site_option(Config::getOption('installed'), Config::VERSION);
    }

    public function deactivate($isNetworkDeactivation = false)
    {
        if ($isNetworkDeactivation) {
            return;
        }
        delete_site_option(Config::getOption('installed'));
    }

    public static function uninstall()
    {
        DBOperator::drop();
    }

    public static function registerActivator()
    {
        Hooks::do(Config::withPrefix('activate'));
    }

    public static function registerDeactivator()
    {
        Hooks::do(Config::withPrefix('deactivate'));
    }

    public static function registerUninstaller()
    {
        Hooks::do(Config::withPrefix('uninstall'));
    }
}
