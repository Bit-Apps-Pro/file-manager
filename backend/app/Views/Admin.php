<?php

namespace BitApps\FM\Views;

use BitApps\FM\Config;

use function BitApps\FM\Functions\view;

use BitApps\FM\Plugin;

use BitApps\FM\Vendor\BitApps\WPKit\Hooks\Hooks;

use BitApps\FM\Vendor\BitApps\WPKit\Utils\Capabilities;

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
        Hooks::removeAction('admin_notices', [Plugin::instance()->telemetryReport(), 'adminNotice']);
        Hooks::addFilter(Config::withPrefix('localized_script'), [$this, 'filterConfigVariable']);
        Hooks::addFilter('script_loader_tag', [$this, 'filterScriptTag'], 0, 3);

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
        if (Capabilities::check('install_plugins')) {
            $config['sys_info'] = $this->getSystemInfo();
        }

        if ($adBanner = Config::adBanner()) {
            $config['adBanner'] = $adBanner;
        }

        return (array) $config + [
            'nonce'         => wp_create_nonce('wp_rest'),
            'apiBase'       => get_rest_url(null, '/file-manager/v1'),
            'baseURL'       => Config::get('ADMIN_URL') . 'admin.php?page=' . Config::SLUG . '#/home',
            'pluginSlug'    => Config::SLUG,
            'rootURL'       => Config::get('ROOT_URI'),
            'assetsURL'     => Config::get('ASSET_URI'),
            'routePrefix'   => Config::VAR_PREFIX,
            'plugin_dir'    => BFM_ROOT_DIR,
            'plugin_url'    => BFM_ROOT_URL,
            'action'        => Config::withPrefix('connector'),
            'options'       => Plugin::instance()->preferences()->finderOptions(),
            'telemetry'     => ['tryPlugin' => Config::tryPlugin()],
        ];
    }

    public function enqueueAssets()
    {
        $preferences = Plugin::instance()->preferences();

        wp_enqueue_style('bfm-jquery-ui-css');
        if (\in_array($preferences->getTheme(), ['default', 'bootstrap'])) {
            wp_enqueue_style(Config::SLUG . 'elfinder-css');
            wp_enqueue_style(Config::SLUG . 'theme-css');
        }

        wp_enqueue_script(Config::SLUG . 'elfinder-script');
        wp_enqueue_script(Config::SLUG . 'elfinder-editor-script');
        wp_enqueue_script(Config::SLUG . 'elfinder-lang', $preferences->getLangUrl(), [Config::SLUG . 'elfinder-script']);

        if (Config::isDev()) {
            $port   = file_get_contents(Config::get('BASEDIR') . '/port');
            $devUrl = 'http://localhost:' . $port;
            wp_enqueue_script(
                Config::SLUG . '-MODULE-vite-client-helper',
                $devUrl . '/config/devHotModule.js',
                [],
                null
            );
            wp_enqueue_script(Config::SLUG . '-MODULE-vite-client', $devUrl . '/@vite/client', [], null);
            wp_enqueue_script(Config::SLUG . '-MODULE-index', $devUrl . '/main.tsx', [], null);
        } else {
            wp_enqueue_script(
                Config::SLUG . '-MODULE-main',
                Config::get('ASSET_JS_URI') . '/main.' . Config::VERSION . '.js',
                [Config::SLUG . 'elfinder-script'],
                Config::VERSION
            );

            wp_enqueue_style(
                Config::SLUG . '-style-main',
                Config::get('ASSET_JS_URI') . '/main.' . Config::VERSION . '.css'
            );
        }
    }

    public function dashboard()
    {
        $this->enqueueAssets();
        // phpcs:ignore
        echo "<div id='bit-fm-root'></div>";
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
                <b>DISALLOW_FILE_EDIT</b> <?php esc_html_e('is set to', 'file-manager'); ?>
                <b>TRUE</b>.
                <?php esc_html_e('You will not be able to edit files with', 'file-manager'); ?>
                 <a href='admin.php?page=file-manager#/settings'>Bit File Manager</a>.
                <?php esc_html_e('Please set', 'file-manager'); ?>
                <b>DISALLOW_FILE_EDIT</b> <?php esc_html_e('to', 'file-manager'); ?>
                <b>FALSE</b>
                in wp-config.php
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

    public function filterCapabilityForHomeMenu()
    {
        if (Plugin::instance()->permissions()->isCurrentUserHasPermission()
        || Plugin::instance()->permissions()->isCurrentRoleHasPermission()
        ) {
            return Plugin::instance()->permissions()->currentUserRole();
        }

        return false;
    }

    /**
     * Modify script tags.
     *
     * @param string $html   script tag
     * @param mixed  $handle
     * @param mixed  $href
     *
     * @return string new script tag
     */
    public function filterScriptTag($html, $handle)
    {
        $newTag = $html;
        if (str_contains($handle, Config::SLUG . '-MODULE')) {
            $newTag = preg_replace('/<script /', '<script type="module" ', $newTag);
        }

        return $newTag;
    }

    private function getSystemInfo()
    {
        return [
            'currentMediaDir'    => esc_url(wp_upload_dir()['path']),
            'phpVersion'         => esc_html(PHP_VERSION),
            'iniPath'            => esc_html(php_ini_loaded_file()),
            'uploadMaxFilesize'  => esc_html(\ini_get('upload_max_filesize')),
            'postMaxSize'        => esc_html(\ini_get('post_max_size')),
            'memoryLimit'        => esc_html(\ini_get('memory_limit')),
            'maxExecutionTime'   => esc_html(\ini_get('max_execution_time')),
            'ua'                 => esc_html($_SERVER['HTTP_USER_AGENT']),
            'fileEditNotAllowed' => \defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT,
        ];
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
                'capability' => Hooks::applyFilter('can_access_fm_home', 'manage_options'),// fm_capabilities
                'slug'       => Config::SLUG,
                'callback'   => [$this, 'dashboard'],
                'icon'       => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 34 30"><g fill="#606060"><path d="M13.54 24.53a7.39 7.39 0 0 1-3.34-9.42Zm5.64.54a7.46 7.46 0 0 1-2.18.32 7.16 7.16 0 0 1-2.28-.39L17 19Z"/><path d="M20.53 14c0 .78 2.28 1.87 1 5.37l-1 2.73L18 14.63h1v-.72h-4.54v.72h1l1.15 3.26-1.49 4.25-2.66-7.51h1v-.72h-2.61a7.4 7.4 0 0 1 11.22-1.28H22A1.42 1.42 0 0 0 20.53 14Zm3.86 4a7.4 7.4 0 0 1-3.92 6.53L23 17.41a7.4 7.4 0 0 0 .25-.7l.11-.32a5.41 5.41 0 0 0 .09-2 7.38 7.38 0 0 1 .94 3.61Z"/><path d="M32.82.76H18.64a1 1 0 0 0-.83.46l-1.6 2.5-2.32 3.9a1 1 0 0 1-.89.48H1.19a1 1 0 0 0-1 1v15c0 2.86 1.82 5.17 4 5.17h25.52c2.23 0 4-2.32 4-5.18l.06-22.32a1 1 0 0 0-.95-1.01ZM17 26.21A8.21 8.21 0 1 1 25.21 18 8.22 8.22 0 0 1 17 26.21Z"/><path d="M20.53 14c0 .78 2.28 1.87 1 5.37l-1 2.73L18 14.63h1v-.72h-4.54v.72h1l1.15 3.26-1.49 4.25-2.66-7.51h1v-.72h-2.61a7.4 7.4 0 0 1 11.22-1.28H22A1.42 1.42 0 0 0 20.53 14Zm-6.99 10.53a7.39 7.39 0 0 1-3.34-9.42ZM24.39 18a7.4 7.4 0 0 1-3.92 6.53L23 17.41a7.4 7.4 0 0 0 .25-.7l.11-.32a5.41 5.41 0 0 0 .09-2 7.38 7.38 0 0 1 .94 3.61Z"/><path d="M19.18 25.07a7.46 7.46 0 0 1-2.18.32 7.16 7.16 0 0 1-2.28-.39L17 19Z"/><path d="M23.24 11.76a8.83 8.83 0 1 0-12.48 12.49 8.83 8.83 0 1 0 12.48-12.49ZM17 26.21A8.21 8.21 0 1 1 25.21 18 8.22 8.22 0 0 1 17 26.21Z"/></g></svg>'),
                'position'   => '2',
            ],
            'Home'               => [
                'parent'     => Config::SLUG,
                'type'       => 'submenu',
                'title'      => __('Dashboard | Bit File Manager', 'file-manager'),
                'name'       => __('Home', 'file-manager'),
                'capability' => Hooks::applyFilter('bitapps_fm_can_access_home', 'manage_options'),
                'slug'       => Config::SLUG . '#/home',
                'position'   => '2',
            ],
            'Logs'           => [
                'parent'     => Config::SLUG,
                'type'       => 'submenu',
                'name'       => 'Logs',
                'title'      => __('Logs | Bit File Manager', 'file-manager'),
                'capability' => Hooks::applyFilter('bitapps_fm_can_access_logs', 'install_plugins'),
                'slug'       => Config::SLUG . '#/logs',
            ],
            'Settings'           => [
                'parent'     => Config::SLUG,
                'type'       => 'submenu',
                'name'       => 'Settings',
                'title'      => __('Settings | Bit File Manager', 'file-manager'),
                'capability' => Hooks::applyFilter('bitapps_fm_can_change_settings', 'install_plugins'),
                'slug'       => Config::SLUG . '#/settings',
            ],
            'Permissions'        => [
                'parent'     => Config::SLUG,
                'type'       => 'submenu',
                'name'       => 'Permissions',
                'title'      => __(
                    'Permissions - Sets permission for specific user or by user role | Bit File Manager',
                    'file-manager'
                ),
                'capability' => Hooks::applyFilter('bitapps_fm_can_change_permissions', 'install_plugins'),
                'slug'       => Config::SLUG . '#/permissions',
            ],
            'Support' => [
                'parent'     => Config::SLUG,
                'type'       => 'submenu',
                'name'       => 'Support',
                'title'      => __(
                    'Support | Bit File Manager',
                    'file-manager'
                ),
                'capability' => Hooks::applyFilter('bitapps_fm_can_access_home', 'manage_options'),
                'slug'       => Config::SLUG . '#/support',
            ],
            'System Information' => [
                'parent'     => Config::SLUG,
                'type'       => 'submenu',
                'name'       => 'System Information',
                'title'      => __(
                    'System Information | Bit File Manager',
                    'file-manager'
                ),
                'capability' => Hooks::applyFilter('bitapps_fm_can_view_sys_info', 'install_plugins'),
                'slug'       => Config::SLUG . '#/system-info',
            ],
        ];
    }
}
