<?php

/**
 *
 * Constant Definition
 *
 * @since v5.2.0
 *
 * */
// Directory Separator
if (!defined('DS')) define("DS", DIRECTORY_SEPARATOR);


// file manger path
if (!defined('FILE_MANAGER_PATH')) define("FILE_MANAGER_PATH", plugin_dir_path(__FILE__));

// file manger url
if (!defined('FILE_MANAGER_URL')) define("FILE_MANAGER_URL", plugin_dir_url(__FILE__));

// Elfinder path
if (!defined('ELFINDER_PATH')) define("ELFINDER_PATH", plugin_dir_path(__FILE__) . 'elFinder');

// Elfinder url
if (!defined('ELFINDER_URL')) define("ELFINDER_URL", plugin_dir_url(__FILE__) . 'elFinder/');

// FM_WP_UPLOAD_DIR = wp_upload_dir();

// Upload dir path
if (!defined('FM_WP_UPLOAD_DIR')) define("FM_WP_UPLOAD_DIR", wp_upload_dir());

// Upload dir path
if (!defined('FM_UPLOAD_DIR_PATH')) define("FM_UPLOAD_DIR_PATH", FM_WP_UPLOAD_DIR['path']);

// Upload dir url
if (!defined('FM_UPLOAD_DIR_URL')) define("FM_UPLOAD_DIR_URL", FM_WP_UPLOAD_DIR['url']);

// Media basedir
if (!defined('FM_MEDIA_BASE_DIR_PATH')) define("FM_MEDIA_BASE_DIR_PATH", FM_WP_UPLOAD_DIR['basedir']);

// Media baseurl
if (!defined('FM_MEDIA_BASE_DIR_URL')) define("FM_MEDIA_BASE_DIR_URL", FM_WP_UPLOAD_DIR['baseurl']);

// File manager upload dir basedir
defined('FM_UPLOAD_BASE_DIR') || define('FM_UPLOAD_BASE_DIR', FM_WP_UPLOAD_DIR['basedir'] . DS . 'file-manager' . DS);

// File manager upload dir baseurl
defined('FM_UPLOAD_BASE_URL') || define('FM_UPLOAD_BASE_URL', FM_WP_UPLOAD_DIR['baseurl'] . DS . 'file-manager' . DS);

// File manager trash dir path
if (!defined('FM_TRASH_DIR_PATH')) {
    /**
     * Custom Trash Directory.
     */
    $fm_trash_dir = FM_WP_UPLOAD_DIR['basedir'] . '/file-manager/trash/';
    define('FM_TRASH_DIR_PATH', $fm_trash_dir);
    if (!file_exists($fm_trash_dir) && is_writable(FM_WP_UPLOAD_DIR['basedir'])) {
        mkdir($fm_trash_dir, 0777, true);
        // Protect files from public access.
        touch(FM_TRASH_DIR_PATH . '.htaccess');
        $content = 'deny from all';
        $fp      = fopen(FM_TRASH_DIR_PATH . '.htaccess', 'wb');
        fwrite($fp, $content);
        fclose($fp);
    } elseif (!file_exists($fm_trash_dir) && !is_writable($fm_trash_dir) && !is_writable(FM_WP_UPLOAD_DIR['basedir'])) {
        add_action('admin_notices', function () { ?>
            <div class="notice notice-error is-dismissible">
                <p>
                    <?php
                    _e('<h1>File Manager</h1>  <b>Your uploads folder is not writable. Please make <code style="color: red;">wp-content/uploads</code> folder writable to create trash folder.</b>', 'file-manager');
                    ?>
                </p>
            </div> <?php
                });
            }
        }

        // File manager trash tmb dir url
        defined('FM_TRASH_TMB_DIR_URL') || define('FM_TRASH_TMB_DIR_URL', FM_WP_UPLOAD_DIR['baseurl'] . '/file-manager/trash/.tmb/');
