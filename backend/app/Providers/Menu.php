<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Config;
use BitApps\FM\Core\Utils\Capabilities;
use BitApps\FM\Core\Helpers\DateTimeHelper;
use BitApps\FM\Core\Utils\Hooks;

/**
 * The admin menu and page handler class
 */
class Menu
{
    public function register()
    {
        Hooks::add('in_admin_header', [$this, 'RemoveAdminNotices']);
        Hooks::add('admin_menu', [$this, 'sideBar']);
        Hooks::add('admin_enqueue_scripts', [$this, 'loadAdminAssets'], 9);
    }

    /**
     * Register the admin menu
     *
     * @return void
     */
    public function sideBar()
    {
        $menus = Hooks::apply(Config::withPrefix('admin_sidebar_menu'), Config::get('SIDE_BAR_MENU'));
        global $submenu;
        foreach ($menus as  $menu) {
            if (isset($menu['capability']) && Capabilities::Check($menu['capability'])) {
                if ($menu['type'] == 'menu') {
                    add_menu_page($menu['title'], $menu['name'], $menu['capability'], $menu['slug'], $menu['callback'], $menu['icon'], $menu['position']);
                } else {
                    $submenu[$menu['parent']][] = [$menu['name'], $menu['capability'], 'admin.php?page='.$menu['slug']];
                }
            }
        }
    }

    /**
     * Load the asset libraries
     *
     * @param string $current_screen $top_level_page variable for current page
     * 
     * @return void
     */
    public function loadAdminAssets($current_screen)
    {
        if (strpos($current_screen, Config::SLUG) === false) {
            return;
        }

        $version = Config::VERSION;
        $jsURI = Config::get('ASSET_JS_URI');
        $slug = Config::SLUG;
        wp_enqueue_script(
            $slug.'-vendors',
            $jsURI.'/vendors-main.js',
            null,
            $version,
            true
        );

        wp_enqueue_script(
            $slug.'-runtime',
            $jsURI.'/runtime.js',
            null,
            $version,
            true
        );

        if (wp_script_is('wp-i18n')) {
            $deps = [$slug.'-vendors', $slug.'-runtime', 'wp-i18n'];
        } else {
            $deps = [$slug.'-vendors', $slug.'-runtime'];
        }

        wp_enqueue_script(
            $slug.'-admin-script',
            $jsURI.'/index.js',
            $deps,
            $version,
            true
        );

        wp_enqueue_style(
            $slug.'-styles',
            Config::get('ASSET_CSS_URI').Config::SLUG.'.css',
            null,
            $version,
            'screen'
        );

        $frontend_vars = apply_filters(
            Config::withPrefix('localized_script'),
            [
                'nonce'      => wp_create_nonce(Config::withPrefix('nonce')),
                'assetsURL'  => Config::get('ASSET_URI'),
                'baseURL'    => Config::get('ADMIN_URL').'admin.php?page='.Config::SLUG.'#',
                'ajaxURL'    => admin_url('admin-ajax.php'),
                'api'        => Config::get('API_URL'),
                'settings'  => Config::getOption('settings'),
                'dateFormat' => Config::getOption('date_format', true),
                'timeFormat' => Config::getOption('time_format', true),
                'timeZone'   => DateTimeHelper::wp_timezone_string(),
            ]
        );
        if (get_locale() !== 'en_US' && file_exists(Config::get('BASEDIR').'/languages/generatedString.php')) {
            include_once Config::get('BASEDIR').'/languages/generatedString.php';
            $frontend_vars['translations'] = Config::withPrefix('i18n_strings');
        }
        wp_localize_script(Config::SLUG.'-admin-script', Config::VAR_PREFIX, $frontend_vars);
    }

    public function RemoveAdminNotices()
    {
        global $plugin_page;
        if (strpos($plugin_page, Config::SLUG) === false) {
            return;
        }
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
    }
}
