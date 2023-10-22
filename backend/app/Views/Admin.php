<?php

namespace BitApps\FM\Views;

use BitApps\FM\Config;
use BitApps\FM\Core\Hooks\Hooks;
use BitApps\FM\Core\Utils\Capabilities;

use function BitApps\FM\Functions\view;

use BitApps\FM\Plugin;

/**
 * The admin Layout and page handler class.
 */
class Admin
{
    private $_reviewBlock;

    public function __construct()
    {
        Hooks::addAction('in_admin_header', [$this, 'removeAdminNotices']);
        Hooks::addAction('admin_menu', [$this, 'sideBarMenuItem']);
        Hooks::addAction('admin_notices', [$this, 'adminNotice']);
        Hooks::addFilter(Config::withPrefix('localized_script'), [$this, 'filterConfigVariable']);
       /*
       * // For testing --
       * Hooks::addFilter('can_access_fm_home', [$this, 'filterCapabilityForHomeMenu']);
       */

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
                        $menu['callback']
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

    public function removeAdminNotices()
    {
        // phpcs:disable
        global $plugin_page;
        if (empty($plugin_page) || strpos($plugin_page, Config::SLUG) === false) {
            return;
        }
        // phpcs:enable
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');

        $this->adminNotice();
    }

    public function filterConfigVariable($config)
    {
        return (array) $config + [
            'action'  => Config::withPrefix('connector'),
            'options' => Plugin::instance()->preferences()->finderOptions(),
        ];
    }

    public function enqueueAssets()
    {
        $preferences = Plugin::instance()->preferences();

        wp_enqueue_style('bfm-jquery-ui-css');
        if (\in_array($preferences->getTheme(), ['default', 'bootstrap'])) {
            wp_enqueue_style('bfm-elfinder-theme-css');
        }

        wp_enqueue_script('bfm-elfinder-script');
        wp_enqueue_script('bfm-elfinder-editor-script');
        wp_enqueue_script('bfm-elfinder-lang', $preferences->getLangUrl(), ['bfm-elfinder-script']);
        wp_enqueue_script('bfm-finder-loader');
    }

    public function homePage()
    {
        return view('admin.index');
    }

    public function settingsPage()
    {
        return view('admin.settings');
    }

    public function logsPage()
    {
        return view('admin.logs');
    }

    public function systemInfoPage()
    {
        return view('admin.utility');
    }

    public function permissionsPage()
    {
        return view('admin.permission_system');
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
                'capability' => Hooks::applyFilter('can_access_fm_home', 'manage_options'),//fm_capabilities
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
                'capability' => Hooks::applyFilter('can_access_fm_home', 'manage_options'),
                'slug'       => Config::SLUG,
                'callback'   => [$this, 'homePage'],
                'icon'       => BFM_ROOT_URL . 'assets/img/icon-24x24.png',
                'position'   => '2',
            ],
            'Logs'           => [
                'parent'     => Config::SLUG,
                'type'       => 'submenu',
                'name'       => 'Logs',
                'title'      => __('Logs | Bit File Manager', 'file-manager'),
                'capability' => Hooks::applyFilter('can_access_fm_logs', 'install_plugins'),
                'slug'       => Config::SLUG . '-logs',
                'callback'   => [$this, 'logsPage'],
            ],
            'Settings'           => [
                'parent'     => Config::SLUG,
                'type'       => 'submenu',
                'name'       => 'Settings',
                'title'      => __('Settings | Bit File Manager', 'file-manager'),
                'capability' => Hooks::applyFilter('can_change_fm_settings', 'install_plugins'),
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
                'capability' => Hooks::applyFilter('can_change_fm_permissions', 'install_plugins'),
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
                'capability' => Hooks::applyFilter('can_view_fm_sys_info', 'install_plugins'),
                'slug'       => Config::SLUG . '-system-info',
                'callback'   => [$this, 'systemInfoPage'],
            ],
        ];
    }

    public function filterCapabilityForHomeMenu()
    {
        if (Plugin::instance()->permissions()->isCurrentUserHasPermission()
        || Plugin::instance()->permissions()->isCurrentRoleHasPermission()
        ) {
            return Plugin::instance()->permissions()->currentUserRole();
        }

        return false;
    }
}
