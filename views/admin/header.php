<?php

/**
 * Header file
 * */

use BitApps\FM\Config;

// Security Check
if (!\defined('ABSPATH')) {
    exit();
}

// wp_enqueue_style('bfm-font-awsome-css');
wp_enqueue_style('bfm-tippy-css');
// wp_enqueue_style('bfm-admin-style');
wp_enqueue_script('bfm-admin-script');
wp_enqueue_style('bfm-admin-style');
wp_enqueue_script('bfm-admin-script');
?>
<div class='fm-header'>
    <h1><img class='fm-logo' src='<?php echo BFM_ROOT_URL . 'assets/img/icon-256x256.png'; ?>'><?php _e('Bit File Manager', 'file-manager'); ?></h1>

    <ul class='top-right-menu'>
        <li><a href='<?php echo esc_url(Config::SUPPORT_URL); ?>'><?php _e('Need help?', 'file-manager'); ?></a></li>
        <li><a href='<?php echo esc_url(Config::REVIEW_URL); ?>'><?php _e('Leave us a feedback', 'file-manager'); ?></a></li>
    </ul>
</div>