<?php

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

namespace BitApps\FM\Views;

use BitApps\FM\Config;
use BitApps\FM\Core\Hooks\Hooks;
use BitApps\FM\Core\Shortcode\Shortcode as SWrapper;
use BitApps\FM\Core\Utils\Capabilities;

/**
 * The admin Layout and page handler class.
 */
class Shortcode
{
    public function __construct()
    {
        SWrapper::addShortcode('file-manager', [$this, 'shortCodeView'])
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

    public function settingsPage()
    {
        include_once BFM_ROOT_DIR
        . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'settings.php';
    }

}
