<?php

namespace BitApps\FM;

/**
 * Main class for the plugin.
 *
 * @since 1.0.0-alpha
 */

use BitApps\FM\Core\Database\Operator as DBOperator;
use BitApps\FM\Core\HookService;
use BitApps\FM\Core\Installer;
use BitApps\FM\Core\Utils\Capabilities;
use BitApps\FM\Core\Utils\Hooks;
use BitApps\FM\Core\Http\Request;
use BitApps\FM\Providers\HookProvider;
use BitApps\FM\Providers\Menu;

final class Plugin
{


    /**
     * Main instance of the plugin.
     *
     * @since 1.0.0-alpha
     *
     * @var Plugin|null
     */
    private static $_instance = null;

    /**
     * Initialize the Plugin with hooks
     *
     * @return void
     */
    public function __construct()
    {
        $this->registerInstaller();
        Hooks::add('plugins_loaded', [$this, 'loaded'], 12);
    }

    public function registerInstaller()
    {
        (new Installer)->register();
    }

    /**
     * Load the plugin.
     *
     * @return void
     */
    public function loaded()
    {
        Hooks::do(Config::withPrefix('loaded'));
        Hooks::add('init', [$this, 'registerProviders'], 11);
        Hooks::filter('plugin_action_links_' . Config::get('BASENAME'), [$this, 'actionLinks']);
        $this->maybeMigrateDB();
    }

    /**
     * Instantiate the Provider class.
     *
     * @return void
     */
    public function registerProviders()
    {
        if (Request::Check('admin')) {
            (new Menu())->register();
        }
        new HookProvider();
    }

    /**
     * Plugin action links
     *
     * @param  array  $links Array of links
     * @return array
     */
    public function actionLinks($links)
    {
        $linksToAdd = Config::get('PLUGIN_PAGE_LINKS');
        foreach ($linksToAdd as $link) {
            $links[] = '<a href="' . $link['url'] . '">' . $link['title'] . '</a>';
        }

        return $links;
    }

    public static function maybeMigrateDB()
    {
        if (!Capabilities::Check('manage_options')) {
            return;
        }
        DBOperator::migrate();
    }

    /**
     * Retrieves the main instance of the plugin.
     *
     * @since 1.0.0-alpha
     *
     * @return Plugin Plugin main instance.
     */
    public static function instance()
    {
        return static::$_instance;
    }

    /**
     * Loads the plugin main instance and initializes it.
     *
     * @param  string  $main_file Absolute path to the plugin main file.
     * @return bool True if the plugin main instance could be loaded, false otherwise
     *
     * @since 1.0.0-alpha
     */
    public static function load($main_file)
    {
        if (null !== static::$_instance) {
            return false;
        }
        static::$_instance = new static($main_file);

        register_activation_hook(Config::get('MAIN_FILE'), [Installer::class, 'registerActivator']);
        register_uninstall_hook(Config::get('MAIN_FILE'), [Installer::class, 'registerDeactivator']);

        return true;
    }
}
