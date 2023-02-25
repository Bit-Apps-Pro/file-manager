<?php

namespace BitApps\FM;

// Main class for the plugin.

use BitApps\FM\Core\Hooks\Hooks;
use BitApps\FM\Core\Http\RequestType;
use BitApps\FM\HTTP\Middleware\NonceCheckerMiddleware;
use BitApps\FM\Providers\FileManager;
use BitApps\FM\Providers\HookProvider;
use BitApps\FM\Views\Admin;
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
        Hooks::addAction('admin_enqueue_scripts', [$this, 'registerAssets']);
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
            new Admin();
        }

        global $FileManager, $FMP;
        $FileManager = new FileManager('File Manager');
        $FMP         = new FileManagerPermission();

        new HookProvider();
    }

    /**
     * Load the asset libraries.
     *
     * @param string $currentScreen $top_level_page variable for current page
     */
    public function registerAssets($currentScreen)
    {
        if (strpos($currentScreen, Config::SLUG) === false) {
            return;
        }

        $version = Config::VERSION;

        $this->loadFinderAssets(); // Loads all the assets necessary for elFinder

        wp_register_style('fmp_permission-system-tippy-css', BFM_ROOT_URL . 'libs/js/tippy-v0.2.8/tippy.css', $version);

        // Admin scripts
        wp_register_script(
            'fmp_permission-system-tippy-script',
            BFM_ROOT_URL . 'libs/js/tippy-v0.2.8/tippy.js',
            ['jquery'],
            $version
        );

        wp_register_script(
            'fmp_permission-system-admin-script',
            BFM_ROOT_URL . 'assets/js/admin-script.js',
            ['fmp_permission-system-tippy-script'],
            $version
        );

        // Including admin-style.css
        wp_register_style('fmp-admin-style', BFM_ROOT_URL . 'assets/css/style.min.css');

        // Including admin-script.js
        wp_register_script('fmp-admin-script', BFM_ROOT_URL . 'assets/js/admin-script.js', ['jquery']);

        // wp_localize_script(Config::SLUG . '-index-MODULE', Config::VAR_PREFIX, self::createConfigVariable());
    }

    /**
     * Registers all the elfinder assets
     * */
    public function loadFinderAssets()
    {
        wp_register_style(
            'fmp-jquery-ui-css',
            Hooks::applyFilter(
                'fm_jquery_ui_theme_hook',
                BFM_ROOT_URL . 'libs/js/jquery-ui/jquery-ui.min.css'
            )
        );

        wp_register_style(
            'fmp-elfinder-css',
            BFM_FINDER_URL . 'css/elfinder.min.css',
            Config::VERSION
        );

        wp_register_style('fmp-elfinder-theme-css', BFM_ROOT_URL . 'libs/js/jquery-ui/jquery-ui.theme.min.css');

        // elFinder Scripts depends on jQuery UI core, selectable, draggable, droppable, resizable, dialog and slider.
        wp_register_script(
            'fmp-elfinder-script',
            BFM_FINDER_URL . 'js/elfinder.min.js',
            ['jquery', 'jquery-ui-core', 'jquery-ui-selectable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-resizable', 'jquery-ui-dialog', 'jquery-ui-slider', 'jquery-ui-tabs']
        );

        wp_register_script(
            'fmp-elfinder-editor-script',
            BFM_FINDER_URL . 'js/extras/editors.default.min.js',
            ['fmp-elfinder-script']
        );

        wp_localize_script(
            'fmp-elfinder-script',
            'fm',
            $this->createConfigVariable()
        );
    }

    public function createConfigVariable()
    {
        return apply_filters(
            Config::withPrefix('localized_script'),
            [
                'ajax_url'   => admin_url('admin-ajax.php'),
                'nonce'      => wp_create_nonce('bfm_nonce'),
                'plugin_dir' => BFM_ROOT_DIR,
                'plugin_url' => BFM_ROOT_URL,
                'js_url'     => BFM_FINDER_URL . 'js/',
                'elfinder'   => BFM_FINDER_URL,
            ]
        );
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
