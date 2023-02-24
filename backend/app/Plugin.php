<?php

namespace BitApps\FM;

// Main class for the plugin.

use BitApps\FM\Core\Hooks\Hooks;
use BitApps\FM\Core\Http\RequestType;
use BitApps\FM\HTTP\Middleware\NonceCheckerMiddleware;
use BitApps\FM\Providers\FileManager;
use BitApps\FM\Providers\HookProvider;
use FileManagerPermission;

final class Plugin
{
    /**
     * Main instance of the plugin.
     *
     * @var null|Plugin
     */
    private static $_instance;

    private $_registeredMiddleware = [];

    /**
     * Initialize the Plugin with hooks.
     */
    public function __construct()
    {
        Hooks::addAction('init', [$this, 'registerProviders']);
        Hooks::addAction('admin_notices', [$this, 'admin_notice']);
        Hooks::addFilter('plugin_action_links_' . Config::get('BASENAME'), [$this, 'actionLinks']);
    }

    public function middlewares()
    {
        return [
            'nonce' => NonceCheckerMiddleware::class,
        ];
    }

    public function getMiddleware($name)
    {
        if (isset($this->_registeredMiddleware[$name])) {
            return $this->_registeredMiddleware[$name];
        }

        $middlewares = $this->middlewares();
        if (isset($middlewares[$name]) && class_exists($middlewares[$name]) && method_exists($middlewares[$name], 'handle')) {
            $this->_registeredMiddleware[$name] = new $middlewares[$name]();
        } else {
            return false;
        }

        return $this->_registeredMiddleware[$name];
    }

    /**
     * Instantiate the Provider class.
     */
    public function registerProviders()
    {
        if (RequestType::is('admin')) {
            // new Layout();
        }

        global $FileManager, $FMP;
        $FileManager = new FileManager('File Manager');
        $FMP         = new FileManagerPermission();

        new HookProvider();
    }

    /**
     * Plugin action links.
     *
     * @param array $links Array of links
     *
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


    /**
     * Adds admin notices to the admin page
     *
     * @return void
     * */
    public function admin_notice()
    {
        // DISALLOW_FILE_EDIT Macro checking
        if (\defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) {
            ?>
            <div class='update-nag fm-error notice notice-success is-dismissible'><b>DISALLOW_FILE_EDIT</b> <?php _e('is set to', 'file-manager'); ?> <b>TRUE</b>. <?php _e('You will not be able to edit files with', 'file-manager'); ?> <a href='admin.php?page=file-manager-settings'>Bit File Manager</a>. <?php _e('Please set', 'file-manager'); ?> <b>DISALLOW_FILE_EDIT</b> <?php _e('to', 'file-manager'); ?> <b>FALSE</b></div>
            <style>
                .fm-error {
                    border-left: 4px solid red;
                    display: block;
                }
            </style>
<?php
        }
    }
    /**
     * Retrieves the main instance of the plugin.
     *
     * @return Plugin plugin main instance
     */
    public static function instance()
    {
        return static::$_instance;
    }

    /**
     * Loads the plugin main instance and initializes it.
     *
     * @return bool True if the plugin main instance could be loaded, false otherwise
     */
    public static function load()
    {
        if (static::$_instance !== null) {
            return false;
        }

        static::$_instance = new static();

        return true;
    }
}
