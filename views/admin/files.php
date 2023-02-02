<?php

/**
 *
 * Security check. No one can access without Wordpress itself
 *
 * */
defined('ABSPATH') or die();
global $FileManager;
//auto::  pr($FileManager->options);
if (!is_array($FileManager->options['file_manager_settings']['language'])) $language_settings = unserialize(stripslashes($FileManager->options['file_manager_settings']['language']));
else $language_settings = $FileManager->options['file_manager_settings']['language'];
//auto::  pr($language_settings);
if ($language_settings['code'] != 'LANG') {
  $language_code = $language_settings['code'];
  $lang_file_url = $language_settings['file-url'];
} else {
  $language_code = get_lang_code();
  // Check if the file is exsits.
  $lang_file_url =  BFM_FINDER_URL . 'js/i18n/elfinder.' . $language_code . '.js';
  $lang_file_path = BFM_FINDER_DIR . 'js/i18n/elfinder.' . $language_code . '.js';
  if (!file_exists($lang_file_path)) {
    $lang_file_url = BFM_FINDER_URL . 'js/i18n/elfinder.en.js';
  }
}
function get_lang_code()
{
  $code = 'en';
  if ('en_US' == get_locale()) {
    $code = 'en';
  } else {
    $code = get_locale();
  }

  return $code;
}


// Command options modifier
$commandOptions = [];
$commandOptions['info'] = [];
$commandOptions['info']['hideItems'] = ['md5', 'sha256'];
$commandOptions['download']['maxRequests'] = 10;
$commandOptions['download']['minFilesZipdl'] = 2; //need to check
$commandOptions['quicklook']['googleDocsMimes'] = ['application/pdf', 'image/tiff', 'application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

if ($FileManager->options['file_manager_settings']['show_url_path'] && $FileManager->options['file_manager_settings']['show_url_path'] == 'hide') {
  $commandOptions['info']['hideItems'][] = 'link';
  $commandOptions['info']['hideItems'][] = 'path';
}
// wp_enqueue_style('fmp-jquery-ui-css');
wp_enqueue_style($FileManager->is_minified_file_load('fmp-elfinder-css')['handle']);
// wp_enqueue_style('fmp-elfinder-theme-css');


wp_enqueue_script($FileManager->is_minified_file_load('fmp-elfinder-script')['handle']);
wp_enqueue_script($FileManager->is_minified_file_load('fmp-elfinder-editor-script')['handle']);

// Testing
$fm_php_syntax_checker = new FMPHPSyntaxChecker();

// Loading lanugage file
if (isset($lang_file_url)) wp_enqueue_script('fmp-elfinder-lang', $lang_file_url, array('fmp-elfinder-script'));
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
      cssAutoLoad: false,
      contextmenu: {
        commands: ['*'],

        // current directory file menu
        files: ['getfile', '|', 'emailto', 'open', 'opennew', 'download', 'opendir', 'quicklook', 'email', '|', 'upload', 'mkdir', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', 'empty', 'hide', '|', 'rename', 'edit', 'resize', '|', 'archive', 'extract', '|', 'selectall', 'selectinvert', '|', 'places', 'info', 'chmod', 'netunmount']
      },
      customData: {
        action: 'connector',
        file_manager_security_token: fm.nonce
      },
      lang: '<?php if (isset($language_code)) echo esc_js($language_code); ?>',
      requestType: 'post',
      width: '<?php if (isset($FileManager->options['file_manager_settings']['size']['width'])) echo esc_js($FileManager->options['file_manager_settings']['size']['width']); ?>',
      height: '<?php if (isset($FileManager->options['file_manager_settings']['size']['height'])) echo esc_js($FileManager->options['file_manager_settings']['size']['height']); ?>',
      commandsOptions: <?php echo json_encode($commandOptions); ?>,
      rememberLastDir: '<?php if (isset($FileManager->options['file_manager_settings']['fm-remember-last-dir'])) echo esc_js($FileManager->options['file_manager_settings']['fm-remember-last-dir']); ?>',
      reloadClearHistory: '<?php if (isset($FileManager->options['file_manager_settings']['fm-clear-history-on-reload'])) echo esc_js($FileManager->options['file_manager_settings']['fm-clear-history-on-reload']); ?>',
      defaultView: '<?php if (isset($FileManager->options['file_manager_settings']['fm_default_view_type'])) echo esc_js($FileManager->options['file_manager_settings']['fm_default_view_type']); ?>', //  'list'  @ref:https://github.com/Studio-42/elFinder/wiki/Client-configuration-options-2.1#defaultView
      ui: <?php if (isset($FileManager->options['file_manager_settings']['fm_display_ui_options'])) echo json_encode($FileManager->options['file_manager_settings']['fm_display_ui_options']);
          else echo json_encode(['toolbar', 'places', 'tree', 'path', 'stat']) ?>,



      sortOrder: 'asc', //'desc'
      sortStickFolders: true, // https://github.com/Studio-42/elFinder/wiki/Client-configuration-options-2.1#sortStickFolders
      dragUploadAllow: 'auto',
      fileModeStyle: "both",
      resizable: true


    });
  });
</script>

<?php

if (isset($FileManager->options->options['file_manager_settings']['show_url_path']) && !empty($FileManager->options->options['file_manager_settings']['show_url_path']) && $FileManager->options->options['file_manager_settings']['show_url_path'] == 'hide') {

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