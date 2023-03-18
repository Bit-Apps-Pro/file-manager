<?php

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

namespace BitApps\FM\Views;

use BitApps\FM\Config;
use BitApps\FM\Core\Hooks\Hooks;
use BitApps\FM\Core\Shortcode\Shortcode as SWrapper;

use function BitApps\FM\Functions\view;

use BitApps\FM\Plugin;

/**
 * The admin Layout and page handler class.
 */
class Shortcode
{
    public function __construct()
    {
        Hooks::addAction('wp_enqueue_scripts', [$this, 'registerAssets']);
        SWrapper::addShortcode('file-manager', [$this, 'shortCodeView']);
        Hooks::addFilter(Config::withPrefix('localized_script'), [$this, 'filterConfigVariable']);
    }

    public function filterConfigVariable($config)
    {
        return (array) $config + [
            'action'  => Config::withPrefix('connector_front'),
            'options' => Plugin::instance()->preferences()->finderOptions(),
        ];
    }

    public function registerAssets()
    {
        Plugin::instance()->registerAssets();
    }

    public function shortCodeView()
    {
        ob_start();
        view('admin.files');
        return ob_get_clean();
    }

    public function enqueueAssets()
    {
        wp_enqueue_style('bfm-jquery-ui-css');
        wp_enqueue_style('bfm-elfinder-css');
        if (\in_array(
            Plugin::instance()->preferences(),
            ['default', 'bootstrap']
        )) {
            wp_enqueue_style('bfm-elfinder-theme-css');
        }
        wp_enqueue_style('fm-front-style');
        wp_enqueue_script('bfm-elfinder-script');
        wp_enqueue_script('bfm-elfinder-editor-script');
        wp_enqueue_script('fm-front-script');
        if (isset($lang_file_url)) {
            wp_enqueue_script('bfm-elfinder-lang', $lang_file_url, ['bfm-elfinder-script']);
        }
    }
}
