<?php
use BitApps\FM\Plugin;

\defined('ABSPATH') || exit();
$preferences = Plugin::instance()->preferences();

wp_enqueue_style('bfm-jquery-ui-css');
if (\in_array($preferences->getTheme(), ['default', 'bootstrap'])) {
    wp_enqueue_style('bfm-elfinder-theme-css');
}

wp_enqueue_script('bfm-elfinder-script');
wp_enqueue_script('bfm-elfinder-editor-script');

wp_enqueue_script('bfm-elfinder-lang', $preferences->getLangUrl(), ['bfm-elfinder-script']);
wp_enqueue_script('bfm-finder-loader');

?>

<div id='file-manager'>

</div>

<?php
if ($preferences->getUrlPathView() == 'hide') {
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