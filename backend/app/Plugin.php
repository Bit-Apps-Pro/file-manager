<?php

namespace BitApps\FM;

// Main class for the plugin.

use BitApps\FM\Core\Database\Connection;
use BitApps\FM\Core\Hooks\Hooks;
use BitApps\FM\Core\Http\RequestType;
use BitApps\FM\HTTP\Middleware\NonceCheckerMiddleware;
use BitApps\FM\Providers\AccessControlProvider;
use BitApps\FM\Providers\FileManager;
use BitApps\FM\Providers\HookProvider;
use BitApps\FM\Providers\Logger;
use BitApps\FM\Providers\MediaSynchronizer;
use BitApps\FM\Providers\MimeProvider;
use BitApps\FM\Providers\PermissionsProvider;
use BitApps\FM\Providers\ReviewProvider;
use BitApps\FM\Providers\SyntaxChecker;
use BitApps\FM\Providers\VersionMigrationProvider;
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

    private $_container = [];

    /**
     * Initialize the Plugin with hooks.
     */
    public function __construct()
    {
        Hooks::addAction('init', [$this, 'registerProviders']);
        Hooks::addAction('admin_enqueue_scripts', [$this, 'registerAssets']);
        Hooks::addFilter('plugin_action_links_' . Config::get('BASENAME'), [$this, 'actionLinks']);
        $this->setPhpIniVars();
        $this->uploadFolder();
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
        if (isset($middlewares[$name])
         && class_exists($middlewares[$name])
         && method_exists($middlewares[$name], 'handle')) {
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
        Connection::setPluginDBPrefix(Config::DB_PREFIX);
        if (RequestType::is('admin')) {
            $this->_container['review_notifier'] = new ReviewProvider();
            new Admin();
        }

        global $FileManager, $FMP;
        $FileManager = new FileManager('File Manager');
        $FMP         = new FileManagerPermission();

        new HookProvider();

        $this->_container['access_control'] = new AccessControlProvider();
        $this->_container['logger']         = new Logger();
        $this->_container['permissions']    = new PermissionsProvider();
        $this->_container['mimes']          = new MimeProvider(BFM_FINDER_DIR . 'php/mime.types');
        $this->_container['media_sync']     = new MediaSynchronizer();
        $this->_container['syntax_checker'] = new SyntaxChecker();

        $migrationProvider = new VersionMigrationProvider();
        $migrationProvider->migrate();
    }

    /**
     * Provide php syntax checker
     *
     * @return SyntaxChecker
     */
    public function syntaxChecker()
    {
        if (!isset($this->_container['syntax_checker'])) {
            $this->_container['syntax_checker'] = new SyntaxChecker();
        }

        return $this->_container['syntax_checker'];
    }

    /**
     * Provide media synchronizer
     *
     * @return MediaSynchronizer
     */
    public function mediaSyncs()
    {
        if (!isset($this->_container['media_sync'])) {
            $this->_container['media_sync'] = new MediaSynchronizer();
        }

        return $this->_container['media_sync'];
    }

    /**
     * Provide finder mime types
     *
     * @return MimeProvider
     */
    public function mimes()
    {
        if (!isset($this->_container['mimes'])) {
            $this->_container['mimes'] = new MimeProvider(BFM_FINDER_DIR . 'php/mime.types');
        }

        return $this->_container['mimes'];
    }

    /**
     * Provide stored permissions settings
     *
     * @return PermissionsProvider
     */
    public function permissions()
    {
        if (!isset($this->_container['permissions'])) {
            $this->_container['permissions'] = new PermissionsProvider();
        }

        return $this->_container['permissions'];
    }

    /**
     * Provide access control for file manager
     *
     * @return AccessControlProvider
     */
    public function accessControl()
    {
        if (!isset($this->_container['access_control'])) {
            $this->_container['access_control'] = new AccessControlProvider();
        }

        return $this->_container['access_control'];
    }

    /**
     * Provide review notifier
     *
     * @return ReviewProvider
     */
    public function reviewNotifier()
    {
        if (!isset($this->_container['review_notifier'])) {
            $this->_container['review_notifier'] = new ReviewProvider();
        }

        return $this->_container['review_notifier'];
    }

    /**
     * Provide Logger for finder
     *
     * @return Logger
     */
    public function logger()
    {
        if (!isset($this->_container['logger'])) {
            $this->_container['logger'] = new Logger();
        }

        return $this->_container['logger'];
    }

    public function get($name)
    {
        if (isset($this->_container[$name])) {
            return $this->_container[$name];
        }

        return false;
    }

    /**
     * Load the asset libraries.
     *
     * @param string $currentScreen $top_level_page variable for current page
     */
    public function registerAssets()
    {
        $version = Config::VERSION;

        $this->registerFinderAssets(); // Loads all the assets necessary for elFinder

        wp_register_style('bfm-tippy-css', BFM_ROOT_URL . 'libs/js/tippy-v0.2.8/tippy.css', $version);

        // Admin scripts
        wp_register_script(
            'bfm-tippy-script',
            BFM_ROOT_URL . 'libs/js/tippy-v0.2.8/tippy.js',
            ['jquery'],
            $version
        );

        wp_register_script(
            'bfm-admin-script',
            BFM_ROOT_URL . 'assets/js/admin-script.js',
            ['bfm-tippy-script'],
            $version
        );

        // Including admin-style.css
        wp_register_style('bfm-admin-style', BFM_ROOT_URL . 'assets/css/style.min.css');

        // Including admin-script.js
        wp_register_script('bfm-admin-script', BFM_ROOT_URL . 'assets/js/admin-script.js', ['jquery']);
    }

    /**
     * Registers all the elfinder assets
     * */
    public function registerFinderAssets()
    {
        wp_register_style(
            'bfm-jquery-ui-css',
            Hooks::applyFilter(
                'fm_jquery_ui_theme_hook',
                BFM_ROOT_URL . 'libs/js/jquery-ui/jquery-ui.min.css'
            )
        );

        wp_register_style(
            'bfm-elfinder-css',
            BFM_FINDER_URL . 'css/elfinder.min.css',
            Config::VERSION
        );

        wp_register_style('bfm-elfinder-theme-css', BFM_ROOT_URL . 'libs/js/jquery-ui/jquery-ui.theme.min.css');

        // elFinder Scripts depends on jQuery UI core, selectable, draggable, droppable, resizable, dialog and slider.
        wp_register_script(
            'bfm-elfinder-script',
            BFM_FINDER_URL . 'js/elfinder.min.js',
            ['jquery', 'jquery-ui-core', 'jquery-ui-selectable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-resizable', 'jquery-ui-dialog', 'jquery-ui-slider', 'jquery-ui-tabs']
        );

        wp_register_script(
            'bfm-elfinder-editor-script',
            BFM_FINDER_URL . 'js/extras/editors.default.min.js',
            ['bfm-elfinder-script']
        );

        wp_localize_script(
            'bfm-elfinder-script',
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

    /**
     * Set the all necessary variables of php.ini file.
     * */
    protected function setPhpIniVars()
    {
        if (\defined('WP_DEBUG') && isset($_REQUEST['action']) && $_REQUEST['action'] === 'bit_fm_connector') {
            ini_set('post_max_size', '128M');
            ini_set('upload_max_filesize', '128M');
        }
    }

    /**
     * Checks if the upload folder is present. If not creates a upload folder.
     * */
    private function uploadFolder()
    {
        if (!is_dir(FM_UPLOAD_BASE_DIR)) {
            mkdir(FM_UPLOAD_BASE_DIR, 0777);
        }
    }
}
