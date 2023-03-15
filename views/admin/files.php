<?php

/**
 * Security check. No one can access without Wordpress itself
 *
 * */

use BitApps\FM\Plugin;

\defined('ABSPATH') || exit();
$preferences = Plugin::instance()->preferences();

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
      themes: fm.options.themes,
      theme: fm.options.theme,
      cssAutoLoad: fm.options.cssAutoLoad,
      contextmenu: fm.options.contextmenu,
      customData: {
        action: 'bit_fm_connector',
        nonce: fm.nonce
      },
      lang: fm.options.lang,
      requestType: fm.options.requestType,
      width: fm.options.width,
      height: fm.options.height,
      commandsOptions: fm.options.commandsOptions,
      rememberLastDir: fm.options.rememberLastDir,
      reloadClearHistory: fm.options.reloadClearHistory,
      defaultView: fm.options.defaultView,
      ui: fm.options.ui,
      sortOrder: fm.options.sortOrder,
      sortStickFolders: fm.options.sortStickFolders,
      dragUploadAllow: fm.options.dragUploadAllow,
      fileModeStyle: fm.options.fileModeStyle,
      resizable: fm.options.resizable
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