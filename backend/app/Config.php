<?php

namespace BitApps\FM;

use BitApps\FM\Views\Admin;

defined('ABSPATH') || exit;
/**
 * Provides App configurations
 */
class Config
{
    const SLUG = 'file-manager';

    const TITLE = ' Bit Flow';

    const VAR_PREFIX = 'bit_fm_';

    const VERSION = '1.0';

    const DB_VERSION = '1.0';

    const REQUIRED_PHP_VERSION = '5.6';

    const REQUIRED_WP_VERSION = '5.0';

    const API_VERSION = '1.0';

    const APP_BASE = '../../file-manager.php';

    /**
     * Provides configuration for plugin
     *
     * @param string $type    Type of conf
     * @param string $default Default value
     * 
     * @return string|null|array
     */
    public static function get($type, $default = null)
    {
        switch ($type) {
            case 'MAIN_FILE':
                return realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . self::APP_BASE);

            case 'BASENAME':
                return plugin_basename(trim(self::get('MAIN_FILE')));

            case 'BASEDIR':
                return plugin_dir_path(self::get('MAIN_FILE')) . 'backend';

            case 'SITE_URL':
                $parsed_url = parse_url(get_admin_url());
                $site_url = $parsed_url['scheme'] . '://' . $parsed_url['host'];
                $site_url .= empty($parsed_url['port']) ? null : ':' . $parsed_url['port'];

                return $site_url;

            case 'ADMIN_URL':
                return str_replace(self::get('SITE_URL'), '', get_admin_url());

            case 'API_URL':

                return [
                    'base' => get_rest_url(),
                    'separator' => strpos(get_rest_url(), '?rest_route') !== false
                     ? '?' : '&',
                ];

            case 'ROOT_URI':
                return set_url_scheme(plugins_url('', self::get('MAIN_FILE')), parse_url(home_url())['scheme']);

            case 'ASSET_URI':
                return self::get('ROOT_URI') . '/assets';

            case 'ASSET_JS_URI':
                return self::get('ASSET_URI') . '/js';

            case 'ASSET_CSS_URI':
                return self::get('ASSET_URI') . '/css';

            case 'PLUGIN_PAGE_LINKS':
                return self::pluginPageLinks();

            case 'SIDE_BAR_MENU':
                return self::sideBarMenu();

            default:
                return $default;
        }
    }
    /**
     * Prefixed variable name with prefix
     * 
     * @param string $option Variable name
     * 
     * @return array
     */
    public static function withPrefix($option)
    {
        return self::VAR_PREFIX . $option;
    }

    /**
     * Retrieves options from option table
     * 
     * @param string $option Option name
     * @param bool   $wp     Whether option is default wp option
     * 
     * @return array
     */
    public static function getOption($option, $wp = false)
    {
        if ($wp) {
            return get_option($option);
        }
        return get_option(self::withPrefix($option));
    }

    /**
     * Saves option to option table
     * 
     * @param string $option Option name
     * @param bool   $autoload     Whether option will autoload
     * 
     * @return array
     */
    public static function addOption($option, $value, $autoload = false)
    {
        return add_option(self::withPrefix($option), $value, '', $autoload ? 'yes' : 'no');
    }

    /**
     * Save or update option to option table
     * 
     * @param string $option   Option name
     * @param mixed  $value    Option value
     * @param bool   $autoload Whether option will autoload
     * 
     * @return array
     */
    public static function updateOption($option, $value, $autoload = null)
    {
        return update_option(self::withPrefix($option), $value, !is_null($autoload) ? 'yes' : null);
    }

    /**
     * Provides links for plugin pages. Those links will bi displayed in
     * all plugin pages under the plugin name.
     *
     * @return array
     */
    private static function pluginPageLinks()
    {
        return [
            'settings' => [
                'title' => __('Settings', 'bit-flow'),
                'url' => self::get('ADMIN_URL') . 'admin.php?page=' . self::SLUG . '#settings',
            ],
            'help' => [
                'title' => __('Help', 'bit-flow'),
                'url' => self::get('ADMIN_URL') . 'admin.php?page=' . self::SLUG . '#help',
            ],
        ];
    }

    /**
     * Provides menus for wordpress admin sidebar.
     * should return an array of menus with the following structure:
     * [
     *   'type' => menu | submenu,
     *  'name' => 'Name of menu will shown in sidebar',
     *  'capability' => 'capability required to access menu',
     *  'slug' => 'slug of menu after ?page=',
     *
     *  'title' => 'page title will be shown in browser title if type is menu',
     *  'callback' => 'function to call when menu is clicked',
     *  'icon' =>   'icon to display in menu if menu type is menu',
     *  'position' => 'position of menu in sidebar if menu type is menu',
     *
     * 'parent' => 'parent slug if submenu'
     * ]
     *
     * @return array
     */
    private static function sideBarMenu()
    {
        $admin_views = new Admin();

        return [
            'Home' => [
                'type' => 'menu',
                'title' => __("Bit Flow - Your flow of automation's", 'bit-flow'),
                'name' => __('Bit Flow', 'bit-flow'),
                'capability' => 'manage_options',
                'slug' => self::SLUG,
                'callback' => [$admin_views, 'home'],
                'icon' => 'dashicons-admin-home',
                'position' => '20',
            ],
            'All Flows' => [
                'parent' => self::SLUG,
                'type' => 'submenu',
                'name' => 'All Flows',
                'capability' => 'manage_options',
                'slug' => self::SLUG . '#/',
            ],
        ];
    }
}
