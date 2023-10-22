<?php

namespace BitApps\FM\Functions;

use BitApps\FM\Plugin;

function view($path)
{
    $pathString = BFM_VIEW_DIR;

    foreach (explode('.', $path) as $dir) {
        $pathString = $pathString . DIRECTORY_SEPARATOR . $dir;
    }

    $pathString = $pathString . '.php';

    if (file_exists($pathString) && is_file($pathString)) {
        include_once $pathString;
    }
}

function pr($obj)
{
    if (\defined('BITAPPS_DEV') && BITAPPS_DEV) {
        return;
    }

    echo '<pre>';
    print_r($obj);
    echo '</pre>';
}


function validatePath($path, $for = '')
{
    if (!class_exists('BitApps\FM\Plugin') || !defined('ABSPATH')) {
        return;
    }

    $realPath = Plugin::instance()->preferences()->realPath($path);

    $realPath = strpos($realPath, ABSPATH) === false && file_exists(ABSPATH . $realPath) ? ABSPATH . $realPath : $realPath;
    $error = null;
    if (strpos($realPath, ABSPATH) === false) {
        $error = 'Directory path must be within WordPress root directory. '. $for .' path: ' .  $realPath;
    }

    if (!is_readable($realPath)) {
        $error = 'Directory is not readable or not exits. '. $for .' path: ' .  $realPath;
    }

    if(!is_null($error))
    {
        view('header.php'); ?>

<div class='fm-container'>
    <div class='col-main col-main-permission-system'>
        <div class='gb-fm-row'>
            <?php echo esc_html($error)?>
        </div>
    </div><?php view('sidebar.php'); ?>
</div>
<?php view('footer.php'); ?>
<?php
wp_die();
    }
    return $realPath;
}
