<?php

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

namespace BitApps\FM\Views;

use BitApps\FM\Core\Shortcode\Shortcode as SWrapper;
use BitApps\FM\Plugin;

/**
 * The admin Layout and page handler class.
 */
class Shortcode
{
    public function __construct()
    {
        SWrapper::addShortcode('file-manager', [$this, 'shortCodeView']);
    }

    public function shortCodeView()
    {
        include_once BFM_ROOT_DIR
        . DIRECTORY_SEPARATOR
        . 'views'
        . DIRECTORY_SEPARATOR
        . 'shortcode'
        . DIRECTORY_SEPARATOR
        . 'file_manager_view.php';

        return $this->shortcode_output;
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
