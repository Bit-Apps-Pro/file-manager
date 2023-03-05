<?php

/**
 * Security check. No one can access without Wordpress itself
 *
 * */

use BitApps\FM\Plugin;
use BitApps\FM\Providers\FileManager;

\defined('ABSPATH') or exit();
/**
 * Global object of FM
 *
 * @var FileManager $FileManager
 */
global $FileManager;
// Command options modifier
$commandOptions                                 = [];
$commandOptions['info']                         = [];
$commandOptions['info']['hideItems']            = ['md5', 'sha256'];
$commandOptions['download']['maxRequests']      = 10;
$commandOptions['download']['minFilesZipdl']    = 2; // need to check
$commandOptions['quicklook']['googleDocsMimes'] = ['application/pdf', 'image/tiff', 'application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

if (Plugin::instance()->preferences()->getUrlPathView() == 'hide') {
    $commandOptions['info']['hideItems'][] = 'link';
    $commandOptions['info']['hideItems'][] = 'path';
}
wp_enqueue_style('bfm-jquery-ui-css');
// wp_enqueue_style($FileManager->is_minified_file_load('fmp-elfinder-css')['handle']);
// var_dump('$FileManager->selectedTheme()', $FileManager->selectedTheme());
if (\in_array(Plugin::instance()->preferences()->getTheme(), ['default', 'bootstrap'])) {
    wp_enqueue_style('bfm-elfinder-theme-css');
}

wp_enqueue_script('bfm-elfinder-script');
wp_enqueue_script('bfm-elfinder-editor-script');

// Testing
// $fm_php_syntax_checker = new FMPHPSyntaxChecker();

// Loading lanugage file
wp_enqueue_script('bfm-elfinder-lang', Plugin::instance()->preferences()->getLangUrl(), ['bfm-elfinder-script']);
?>

<div id='file-manager'>

</div>

<script>
  PLUGINS_URL = '<?php echo esc_js(plugins_url()); ?>';

  jQuery(document).ready(function() {
    jQuery('#file-manager').elfinder({
      url: ajaxurl,
      themes: fm.themes,
      theme: fm.theme,
      cssAutoLoad: true,
      contextmenu: {
        commands: ['*'],

        // current directory file menu
        files: ['getfile', '|', 'emailto', 'open', 'opennew', 'download', 'opendir', 'quicklook', 'email', '|', 'upload', 'mkdir', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', 'empty', 'hide', '|', 'rename', 'edit', 'resize', '|', 'archive', 'extract', '|', 'selectall', 'selectinvert', '|', 'places', 'info', 'chmod', 'netunmount']
      },
      customData: {
        action: 'bit_fm_connector',
        bfm_nonce: fm.nonce
      },
      lang: '<?php echo esc_js(Plugin::instance()->preferences()->getLangCode()); ?>',
      requestType: 'post',
      width: '<?php if (isset($FileManager->preferences['size']['width'])) {
          echo esc_js($FileManager->preferences['size']['width']);
      } ?>',
      height: '<?php if (isset($FileManager->preferences['size']['height'])) {
          echo esc_js($FileManager->preferences['size']['height']);
      } ?>',
      commandsOptions: <?php echo json_encode($commandOptions); ?>,
      rememberLastDir: '<?php if (isset($FileManager->preferences['fm-remember-last-dir'])) {
          echo esc_js($FileManager->preferences['fm-remember-last-dir']);
      } ?>',
      reloadClearHistory: '<?php if (isset($FileManager->preferences['fm-clear-history-on-reload'])) {
          echo esc_js($FileManager->preferences['fm-clear-history-on-reload']);
      } ?>',
      defaultView: '<?php if (isset($FileManager->preferences['fm_default_view_type'])) {
          echo esc_js($FileManager->preferences['fm_default_view_type']);
      } ?>', //  'list'  @ref:https://github.com/Studio-42/elFinder/wiki/Client-configuration-options-2.1#defaultView
      ui: <?php if (isset($FileManager->preferences['fm_display_ui_options'])) {
          echo json_encode($FileManager->preferences['fm_display_ui_options']);
      } else {
          echo json_encode(['toolbar', 'places', 'tree', 'path', 'stat']);
      } ?>,



      sortOrder: 'asc', //'desc'
      sortStickFolders: true, // https://github.com/Studio-42/elFinder/wiki/Client-configuration-options-2.1#sortStickFolders
      dragUploadAllow: 'auto',
      fileModeStyle: "both",
      resizable: true


    });
  });
</script>

<?php
if (Plugin::instance()->preferences()->getUrlPathView() == 'hide') {
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