<?php

/**
 * Footer file of the plugin
 * */

use BitApps\FM\Config;

if (!\defined('ABSPATH')) {
    exit();
}
?>
<div class='fm-footer'>
    <ul>
        <li>
            <a href='https://wordpress.org/plugins/file-manager/changelog/'>
                <?php _e('Changelog', 'file-manager'); ?>
            </a>
        </li>
        <li>
            <a href='<?php echo esc_url(Config::SUPPORT_URL); ?>'>
                <?php _e('Contacts', 'file-manager'); ?>
            </a>
        </li>
        <li>
            <a href='<?php echo esc_url(Config::REVIEW_URL); ?>'>
                <?php _e('Review', 'file-manager'); ?>
            </a>
        </li>
        <li>
            <a href='https://bitapps.pro'>
                 Bit Apps
            </a>
        </li>
    </ul>
</div>