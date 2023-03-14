<?php

/**
 * Security check. No one can access without Wordpress itself
 *
 * */

use BitApps\FM\Plugin;

\defined('ABSPATH') or exit();
$preferences = Plugin::instance()->preferences();
// Command options modifier
$commandOptions                                 = [];
$commandOptions['info']                         = [];
$commandOptions['info']['hideItems']            = ['md5', 'sha256'];
$commandOptions['download']['maxRequests']      = 10;
$commandOptions['download']['minFilesZipdl']    = 2; // need to check
$commandOptions['quicklook']['googleDocsMimes'] = ['application/pdf', 'image/tiff', 'application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

if ($preferences->getUrlPathView() == 'hide') {
    $commandOptions['info']['hideItems'][] = 'link';
    $commandOptions['info']['hideItems'][] = 'path';
}
wp_enqueue_style('bfm-jquery-ui-css');
if (\in_array($preferences->getTheme(), ['default', 'bootstrap'])) {
    wp_enqueue_style('bfm-elfinder-theme-css');
}

wp_enqueue_script('bfm-elfinder-script');
wp_enqueue_script('bfm-elfinder-editor-script');

// Testing
// $fm_php_syntax_checker = new FMPHPSyntaxChecker();

// Loading lanugage file
wp_enqueue_script('bfm-elfinder-lang', $preferences->getLangUrl(), ['bfm-elfinder-script']);
?>

<div id='file-manager'>

</div>

<script>
  PLUGINS_URL = '<?php echo esc_js(plugins_url()); ?>';

  jQuery(document).ready(function() {

    const finder = jQuery('#file-manager').elfinder({
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
        nonce: fm.nonce
      },
      lang: '<?php echo esc_js($preferences->getLangCode()); ?>',
      requestType: 'post',
      width: '<?php echo $preferences->getWidth()?>',
      height: '<?php echo esc_js($preferences->getHeight()); ?>',
      commandsOptions: <?php echo json_encode($commandOptions); ?>,
      rememberLastDir: '<?php echo esc_js($preferences->getRememberLastDir());?>',
      reloadClearHistory: '<?php echo esc_js($preferences->getClearHistoryOnReload()); ?>',
      defaultView: '<?php echo esc_js($preferences->getViewType());?>', //  'list'  @ref:https://github.com/Studio-42/elFinder/wiki/Client-configuration-options-2.1#defaultView
      ui: <?php echo json_encode($preferences->getUiOptions()); ?>,
      sortOrder: 'asc', //'desc'
      sortStickFolders: true, // https://github.com/Studio-42/elFinder/wiki/Client-configuration-options-2.1#sortStickFolders
      dragUploadAllow: 'auto',
      fileModeStyle: "both",
      resizable: true
    });
    
    $('#file-manager').on('change',"select.elfinder-tabstop",function (e) {
      if (e.currentTarget[0]
       && e.currentTarget[0].className
      && e.currentTarget[0].className.indexOf('elfinder-theme-option') !== -1
      ) {
        jQuery.ajax(
          ajaxurl,
          {
          method: 'POST',
          data: {
            action: 'bit_fm_lang',
            nonce: fm.nonce,
            theme: e.currentTarget.value
          }
        }
        ).done(()=>location.reload())
      }
      console.log('select.elfinder-tabstop', e.currentTarget.value)
    });

  });
</script>

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