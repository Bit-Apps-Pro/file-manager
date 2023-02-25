<?php

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

namespace BitApps\FM\Views;

use BitApps\FM\Config;
use BitApps\FM\Core\Hooks\Hooks;
use BitApps\FM\Core\Utils\Capabilities;

/**
 * The admin Layout and page handler class.
 */
class Admin
{
    public function __construct()
    {
        Hooks::addAction('in_admin_header', [$this, 'removeAdminNotices']);
        Hooks::addAction('admin_menu', [$this, 'sideBarMenuItem']);
        Hooks::addAction('admin_enqueue_scripts', [$this, 'head']);
        Hooks::addAction('admin_notices', [$this, 'adminNotice']);
    }

    /**
     * Register the admin left sidebar menu item.
     */
    public function sideBarMenuItem()
    {
        $menus = Hooks::applyFilter(Config::withPrefix('admin_sidebar_menu'), $this->sideBarMenus());
        global $submenu;
        foreach ($menus as $menu) {
            if (isset($menu['capability']) && Capabilities::check($menu['capability'])) {
                if ($menu['type'] == 'menu') {
                    add_menu_page(
                        $menu['title'],
                        $menu['name'],
                        $menu['capability'],
                        $menu['slug'],
                        $menu['callback'],
                        $menu['icon'],
                        $menu['position']
                    );
                } elseif (isset($menu['callback'])) {
                    add_submenu_page(
                        $menu['parent'],
                        $menu['title'],
                        $menu['name'],
                        $menu['capability'],
                        $menu['slug'],
                        $menu['callback'],
                    );
                } else {
                    $submenu[$menu['parent']][] = [
                        $menu['name'],
                        $menu['capability'],
                        'admin.php?page=' . $menu['slug'],
                    ];
                }
            }
        }
    }

    /**
     * Load the asset libraries.
     *
     * @param string $currentScreen $top_level_page variable for current page
     */
    public function head($currentScreen)
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

    public function removeAdminNotices()
    {
        global $plugin_page;
        if (empty($plugin_page) || strpos($plugin_page, Config::SLUG) === false) {
            return;
        }

        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');

        $this->adminNotice();
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
                'themes'     => $this->themes(),
                'theme'      => $this->selectedTheme(),
            ]
        );
    }

    /**
     * Returns all available themes
     *
     * @return array
     */
    public function themes()
    {
        $themeBase = BFM_ROOT_DIR . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'themes';
        $themeDirs = scandir($themeBase);
        $themes    = [];
        foreach ($themeDirs as $theme) {
            if ($theme === '.' || $theme === '..') {
                continue;
            }
            $variants = scandir($themeBase . DIRECTORY_SEPARATOR . $theme);
            foreach ($variants as $variant) {
                if ($variant === '.' || $variant === '..') {
                    continue;
                }

                if (is_readable($themeBase . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . $variant . DIRECTORY_SEPARATOR . $variant . '.json')) {
                    $themes[$variant] = BFM_ASSET_URL . "themes/{$theme}/{$variant}/{$variant}.json";
                }
            }
        }

        return $themes;
    }

    /**
     * Returns selected theme from settings
     *
     * @return array
     */
    public function selectedTheme()
    {
        return 'default';
        // if (isset($this->options['file_manager_settings']['theme'])) {
        //     $theme = $this->options['file_manager_settings']['theme'];
        // }
    }

    public function homePage()
    {
        include_once BFM_ROOT_DIR
        . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'index.php';
    }

    public function settingsPage()
    {
        include_once BFM_ROOT_DIR
        . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'settings.php';
    }

    public function systemInfoPage()
    {
        include_once BFM_ROOT_DIR
        . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'utility.php';
    }

    public function permissionsPage()
    {
        include_once BFM_ROOT_DIR
        . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'permission_system.php';
    }

    /**
     * Adds admin notices to the admin page
     *
     * @return void
     * */
    public function adminNotice()
    {
        // DISALLOW_FILE_EDIT Macro checking
        if (\defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) {
            ?>
            <div class='update-nag fm-error notice notice-success is-dismissible'>
                <b>DISALLOW_FILE_EDIT</b> <?php _e('is set to', 'file-manager'); ?>
                <b>TRUE</b>.
                <?php _e('You will not be able to edit files with', 'file-manager'); ?>
                 <a href='admin.php?page=file-manager-settings'>Bit File Manager</a>.
                <?php _e('Please set', 'file-manager'); ?>
                <b>DISALLOW_FILE_EDIT</b> <?php _e('to', 'file-manager'); ?>
                <b>FALSE</b>
            </div>
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
     * Provides menus for wordpress admin sidebar.
     * should return an array of menus with the following structure:
     * [
     *   'type' => menu | submenu,
     *  'name' => 'Name of menu will shown in sidebar',
     *  'capability' => 'capability required to access menu',
     *  'slug' => 'slug of menu after ?page=',.
     *
     *  'title' => 'page title will be shown in browser title if type is menu',
     *  'callback' => 'function to call when menu is clicked',
     *  'icon' =>   'icon to display in menu if menu type is menu',
     *  'position' => 'position of menu in sidebar if menu type is menu',
     *
     * 'parent' => 'parent slug if submenu'
     * ]
     *
     * @return array<string,array{type:string,title:string,name:string,slug:string,callback:callable}>
     */
    private function sideBarMenus()
    {
        return [
            'Bit File Manager'   => [
                'type'       => 'menu',
                'title'      => __('Dashboard | Bit File Manager', 'file-manager'),
                'name'       => __('Bit File Manager', 'file-manager'),
                'capability' => Hooks::applyFilter('fm_capabilities', 'manage_options'),
                'slug'       => Config::SLUG,
                'callback'   => [$this, 'homePage'],
                'icon'       => BFM_ROOT_URL . 'assets/img/icon-24x24.png',
                'position'   => '2',
            ],
            'Home'               => [
                'parent'     => Config::SLUG,
                'type'       => 'submenu',
                'title'      => __('Dashboard | Bit File Manager', 'file-manager'),
                'name'       => __('Home', 'file-manager'),
                'capability' => Hooks::applyFilter('fm_capabilities', 'manage_options'),
                'slug'       => Config::SLUG,
                'callback'   => [$this, 'homePage'],
                'icon'       => BFM_ROOT_URL . 'assets/img/icon-24x24.png',
                'position'   => '2',
            ],
            'Settings'           => [
                'parent'     => Config::SLUG,
                'type'       => 'submenu',
                'name'       => 'Settings',
                'title'      => __('Settings | Bit File Manager', 'file-manager'),
                'capability' => Hooks::applyFilter('can_change_fm_settings', 'manage_options'),
                'slug'       => Config::SLUG . '-settings',
                'callback'   => [$this, 'settingsPage'],
            ],
            'Permissions'        => [
                'parent'     => Config::SLUG,
                'type'       => 'submenu',
                'name'       => 'Permissions',
                'title'      => __(
                    'Permissions - Sets permission for specific user or by user role | Bit File Manager',
                    'file-manager'
                ),
                'capability' => Hooks::applyFilter('can_change_fm_permissions', 'manage_options'),
                'slug'       => Config::SLUG . '-permissions',
                'callback'   => [$this, 'permissionsPage'],
            ],
            'System Information' => [
                'parent'     => Config::SLUG,
                'type'       => 'submenu',
                'name'       => 'System Information',
                'title'      => __(
                    'System Information | Bit File Manager',
                    'file-manager'
                ),
                'capability' => Hooks::applyFilter('can_view_fm_sys_info', 'manage_options'),
                'slug'       => Config::SLUG . '-system-info',
                'callback'   => [$this, 'systemInfoPage'],
            ],
        ];
    }
}
