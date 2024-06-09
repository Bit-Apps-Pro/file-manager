<?php

use BitApps\FM\Config;
use BitApps\FM\Plugin;

\defined('ABSPATH') || exit();
$preferences = Plugin::instance()->preferences();

wp_enqueue_style('bfm-jquery-ui-css');
if (\in_array($preferences->getTheme(), ['default', 'bootstrap'])) {
    wp_enqueue_style(Config::SLUG . 'elfinder-css');
    wp_enqueue_style(Config::SLUG . 'theme-css');
}

wp_enqueue_script(Config::SLUG . 'elfinder-script');
wp_enqueue_script(Config::SLUG . 'elfinder-editor-script');

wp_enqueue_script(Config::SLUG . 'elfinder-lang', $preferences->getLangUrl(), [Config::SLUG . 'elfinder-script']);
wp_enqueue_script('bfm-finder-loader');
?>

<!-- <div id='file-manager'>

</div> -->

<div id='file-manager'>
</div>
<?php
if ($preferences->isLinkPathVisibleInInfo() == 'hide') {
    ?>
  <style>
    .elfinder-info-tb>tbody:nth-child(1)>tr:nth-child(2),
    .elfinder-info-tb>tbody:nth-child(1)>tr:nth-child(3) {
      display: none;
    }
  </style>
<?php

}

?>