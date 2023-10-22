<?php

/**
 *
 * @file utility.php Utility information about the plugin
 *
 * */

// Security Check
if (!defined('ABSPATH')) {
    die();
}

?>
<?php
wp_enqueue_style('bfm-admin-style');

?>
<div class='fm-container'>

    <div class='col-main'>
        <div class='gb-fm-row fm-data'>
            <table>
                <tr>
                    <td><?php esc_html_e("Current Media Directory", 'file-manager'); ?></td>
                    <td>
                        <?php
                        $wp_upload_dir = wp_upload_dir();
                        echo esc_url($wp_upload_dir['path']);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><?php esc_html_e("PHP version", 'file-manager'); ?></td>
                    <td><?php echo esc_html(phpversion()); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e("PHP ini file", 'file-manager'); ?></td>
                    <td><?php echo esc_html(php_ini_loaded_file()); ?></td>
                </tr>

                <tr>
                    <td><?php esc_html_e("Maximum file upload size", 'file-manager'); ?></td>
                    <td><?php echo esc_html(ini_get('upload_max_filesize')); ?></td>
                </tr>

                <tr>
                    <td><?php esc_html_e("Post maximum file upload size", 'file-manager'); ?></td>
                    <td><?php echo esc_html(ini_get('post_max_size')); ?></td>
                </tr>

                <tr>
                    <td><?php esc_html_e("Memory Limit", 'file-manager'); ?></td>
                    <td><?php echo esc_html(ini_get('memory_limit')); ?></td>
                </tr>

                <tr>
                    <td><?php esc_html_e("Timeout", 'file-manager'); ?></td>
                    <td><?php echo esc_html(ini_get('maxesc_html_execution_time')); ?></td>
                </tr>

                <tr>
                    <td><?php esc_html_e("Browser and OS", 'file-manager'); ?></td>
                    <td><?php echo esc_html($_SERVER['HTTP_USER_AGENT']); ?></td>
                </tr>

                <tr>
                    <td>DISALLOW_FILE_EDIT</td>
                    <td>
                        <?php
                        if (defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) {
                            echo "TRUE";
                        } else {
                            echo "FALSE";
                        }

                        ?>
                    </td>
                </tr>

            </table>
        </div>
    </div>
    <?php require_once 'sidebar.php'; ?>
</div>