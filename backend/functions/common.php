<?php

namespace BitApps\FM\Functions;

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
