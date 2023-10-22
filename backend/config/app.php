<?php

/**
 * Constant Definition
 *
 * @since v5.2.0
 * */

// Directory Separator
if (!\defined('DS')) {
    \define('DS', DIRECTORY_SEPARATOR);
}

\define('BFM_BASEDIR', plugin_dir_path(BFM_MAIN_FILE));
\define('BFM_BASEURL', plugin_dir_url(BFM_MAIN_FILE));

// define('BFM_ROOT_DIR', dirname(BFM_BASEDIR) . DIRECTORY_SEPARATOR);
// define('BFM_ROOT_URL', plugin_dir_url(BFM_BASEDIR));

\define('BFM_ROOT_DIR', BFM_BASEDIR);
\define('BFM_VIEW_DIR', BFM_BASEDIR . 'views');

\define('BFM_ROOT_URL', BFM_BASEURL);
\define('BFM_ASSET_URL', BFM_BASEURL . 'assets/');

\define('BFM_FINDER_DIR', BFM_ROOT_DIR . 'libs' . DIRECTORY_SEPARATOR . 'elFinder' . DIRECTORY_SEPARATOR);
\define('BFM_FINDER_URL', BFM_BASEURL . 'libs/elFinder/');

// Upload dir path
if (!\defined('FM_WP_UPLOAD_DIR')) {
    \define('FM_WP_UPLOAD_DIR', wp_upload_dir());
}

// Upload dir path
if (!\defined('FM_UPLOAD_DIR_PATH')) {
    \define('FM_UPLOAD_DIR_PATH', FM_WP_UPLOAD_DIR['path']);
}

// Upload dir url
if (!\defined('FM_UPLOAD_DIR_URL')) {
    \define('FM_UPLOAD_DIR_URL', FM_WP_UPLOAD_DIR['url']);
}

// Media basedir
if (!\defined('FM_MEDIA_BASE_DIR_PATH')) {
    \define('FM_MEDIA_BASE_DIR_PATH', FM_WP_UPLOAD_DIR['basedir']);
}

// Media baseurl
if (!\defined('FM_MEDIA_BASE_DIR_URL')) {
    \define('FM_MEDIA_BASE_DIR_URL', FM_WP_UPLOAD_DIR['baseurl']);
}

// File manager upload dir basedir
\defined('FM_UPLOAD_BASE_DIR') || \define('FM_UPLOAD_BASE_DIR', FM_WP_UPLOAD_DIR['basedir'] . DS . 'file-manager');

// File manager upload dir baseurl
\defined('FM_UPLOAD_BASE_URL') || \define('FM_UPLOAD_BASE_URL', FM_WP_UPLOAD_DIR['baseurl'] . '/file-manager');

// File manager trash dir path
if (!\defined('FM_TRASH_DIR_PATH')) {
    /**
     * Custom Trash Directory.
     */
    $fmTrashDir = FM_WP_UPLOAD_DIR['basedir'] . '/file-manager/trash/';
    \define('FM_TRASH_DIR_PATH', $fmTrashDir);
    if (!file_exists($fmTrashDir) && is_writable(FM_WP_UPLOAD_DIR['basedir'])) {
        mkdir($fmTrashDir, 0777, true);
        // Protect files from public access.
        touch(FM_TRASH_DIR_PATH . '.htaccess');
        $content = 'deny from all';
        $fp      = fopen(FM_TRASH_DIR_PATH . '.htaccess', 'wb');
        fwrite($fp, $content);
        fclose($fp);
    } elseif (!file_exists($fmTrashDir) && !is_writable($fmTrashDir) && !is_writable(FM_WP_UPLOAD_DIR['basedir'])) {
        add_action(
            'admin_notices',
            function () {
                ?>
            <div class="notice notice-error is-dismissible">
                <p>
                    <?php
                                    esc_html_e(
                                        '<h1>File Manager</h1>
                        <b>Your uploads folder is not writable. Please make
                        <code style="color: red;">wp-content/uploads</code>
                         folder writable to create trash folder.
                        </b>',
                                        'file-manager'
                                    );
                ?>
                </p>
            </div>
<?php
            }
        );
    }
}

// File manager trash tmb dir url
if (!\defined('FM_TRASH_TMB_DIR_URL')) {
    \define('FM_TRASH_TMB_DIR_URL', FM_WP_UPLOAD_DIR['baseurl'] . '/file-manager/trash/.tmb/');
}
