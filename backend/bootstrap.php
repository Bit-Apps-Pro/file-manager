<?php

defined('ABSPATH') or die();

require_once 'config' . DIRECTORY_SEPARATOR . 'app.php';
require_once 'functions' . DIRECTORY_SEPARATOR . 'installer.php';

register_activation_hook(__FILE__, 'bfmActivate');
register_deactivation_hook(__FILE__, 'bfmDeactivate');
register_uninstall_hook(__FILE__, 'bfmUninstall');

add_action('plugins_loaded', 'bfmLoaded');
