<?php
/**
 *
 * Security check. No one can access without Wordpress itself
 *
 * */
defined('ABSPATH') or die();
global $FileManager;
//auto::  pr($FileManager->options);
if(!is_array($FileManager->options['file_manager_settings']['language'])) $language_settings = unserialize(stripslashes($FileManager->options['file_manager_settings']['language']));
	else $language_settings = $FileManager->options['file_manager_settings']['language'];
//auto::  pr($language_settings);
if($language_settings['code'] != 'LANG'){
	$language_code = $language_settings['code'];
	$lang_file_url = $language_settings['file-url'];
}

wp_enqueue_style( 'fmp-jquery-ui-css' );
wp_enqueue_style( 'fmp-elfinder-css' );
wp_enqueue_style( 'fmp-elfinder-theme-css' );

wp_enqueue_script('fmp-elfinder-script');
wp_enqueue_script('fmp-elfinder-editor-script');

// Testing
$fm_php_syntax_checker = new FMPHPSyntaxChecker();
// $fm_php_syntax_checker->pl("Bal Chal");

// Loading lanugage file
if( isset($lang_file_url) ) wp_enqueue_script('fmp-elfinder-lang', $lang_file_url, array('fmp-elfinder-script'));
?>

<div id='file-manager'>

</div>

<script>

PLUGINS_URL = '<?php echo plugins_url();?>';

jQuery(document).ready(function(){

	jQuery('#file-manager').elfinder({
		url: ajaxurl,
		contextmenu : {
            // current directory file menu
            files  : ['getfile', '|' ,'open', 'opennew', 'download', 'opendir', 'quicklook', 'email', '|', 'upload', 'mkdir', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', 'empty', 'hide', '|', 'rename', 'edit', 'resize', '|', 'archive', 'extract', '|', 'selectall', 'selectinvert', '|', 'places', 'info', 'chmod', 'netunmount'
            ]
        },
		customData:{action: 'connector', file_manager_security_token: '<?php echo wp_create_nonce( "file-manager-security-token" ); ?>'},
		lang: '<?php if( isset($language_code) ) echo esc_js($language_code); ?>',
		requestType: 'post',
		width: '<?php if(isset($FileManager->options['file_manager_settings']['size']['width'])) echo esc_js($FileManager->options['file_manager_settings']['size']['width']); ?>',
		height: '<?php if(isset($FileManager->options['file_manager_settings']['size']['height'])) echo esc_js($FileManager->options['file_manager_settings']['size']['height']); ?>',
	});
});

</script>

<?php

if( isset( $FileManager->options->options['file_manager_settings']['show_url_path'] ) && !empty( $FileManager->options->options['file_manager_settings']['show_url_path']) && $FileManager->options->options['file_manager_settings']['show_url_path'] == 'hide' ){

?>
<style>
.elfinder-info-tb > tbody:nth-child(1) > tr:nth-child(2),
.elfinder-info-tb > tbody:nth-child(1) > tr:nth-child(3)
{
	display: none;
}
</style>
<?php

}

?>
