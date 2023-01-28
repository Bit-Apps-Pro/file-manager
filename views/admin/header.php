<?php

/**
 *
 * @file header.php Header file
 *
 * */

// Security Check
if (!defined('ABSPATH')) die();
global $FileManager;
wp_enqueue_style('fmp_permission-system-font-awsome-css');
wp_enqueue_style('fmp_permission-system-tippy-css');
wp_enqueue_style('fmp-admin-style');
wp_enqueue_script('fmp_permission-system-admin-script');
wp_enqueue_style('fmp-admin-style');
wp_enqueue_script('fmp-admin-script');
?>
<div class='fm-header'>
    <h1><img class='fm-logo' src='<?php echo BFM_ROOT_URL . 'assets/img/icon-256x256.png'; ?>'><?php _e('Bit File Manager', 'file-manager'); ?></h1>

    <ul class='top-right-menu'>
        <li><a href='<?php echo esc_url($FileManager->support_page); ?>'><?php _e("Need help?", 'file-manager'); ?></a></li>
        <li><a href='<?php echo esc_url($FileManager->feedback_page); ?>'><?php _e("Leave us a feedback", 'file-manager'); ?></a></li>
    </ul>
</div>