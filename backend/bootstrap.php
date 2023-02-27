<?php

\defined('ABSPATH') or exit();

require_once 'config' . DIRECTORY_SEPARATOR . 'app.php';
require_once 'functions' . DIRECTORY_SEPARATOR . 'global.php';

register_activation_hook(BFM_MAIN_FILE, 'bfmActivate');
register_deactivation_hook(BFM_MAIN_FILE, 'bfmDeactivate');
register_uninstall_hook(BFM_MAIN_FILE, 'bfmUninstall');

add_action('plugins_loaded', 'bfmLoaded');
