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
if (!wp_style_is('bfm-admin-style')) {
    wp_enqueue_style('bfm-admin-style');
}

wp_enqueue_script('bfm-admin-script');
?>
<div class='fm-header'>
    <h1>
        <img class='fm-logo' src='<?php echo esc_url(BFM_ROOT_URL) . 'assets/img/logo.svg'; ?>'>
    </h1>

    <ul class='top-right-menu'>
        <li><a href='<?php echo esc_url(Config::SUPPORT_URL); ?>'><?php esc_html_e('Need help?', 'file-manager'); ?></a></li>
        <li><a href='<?php echo esc_url(Config::REVIEW_URL); ?>'><?php esc_html_e('Leave us a feedback', 'file-manager'); ?></a></li>
    </ul>
</div>