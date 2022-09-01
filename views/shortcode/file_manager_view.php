<?php

/**
 *
 * File Manager Frontend View page
 *
 * */

// Security Check
defined('ABSPATH') or die();
global $FMP, $FileManager;

// language
$language = $FileManager->options['file_manager_settings']['language'];
$language_settings = is_array($language) ? $language : unserialize(stripslashes($language));
if ($language_settings['code'] != 'LANG') {
    $language_code = $language_settings['code'];
    $lang_file_url = $language_settings['file-url'];
}
$settings = get_option('fmp_permission_system');
// pr($settings);
if (!is_user_logged_in() && count($settings['fmp_guest']) <= 1) return;
if ($FMP->is_bannned()) {
    ob_start();
    include 'file_manager_access_not_allowed.php';
    $FMP->shortcode_output = ob_get_clean();
    $FMP->shortcode_output = apply_filters('bfm_acces_not_allowed', $FMP->shortcode_output);
    return;
}

$ajax_url = site_url() . "/wp-admin/admin-ajax.php";

wp_enqueue_style('fmp-jquery-ui-css');
wp_enqueue_style('fmp-elfinder-css');
wp_enqueue_style('fmp-elfinder-theme-css');
wp_enqueue_style('fm-front-style');

wp_enqueue_script($FileManager->is_minified_file_load('fmp-elfinder-script')['handle']);
wp_enqueue_script($FileManager->is_minified_file_load('fmp-elfinder-editor-script')['handle']);
wp_enqueue_script('fm-front-script');
if (isset($lang_file_url)) wp_enqueue_script('fmp-elfinder-lang', $lang_file_url, array('fmp-elfinder-script'));
var_dump($FMP->no_permission());
ob_start();
?>

<div id='file-manager-pro-wrapper'></div>

<script>
    PLUGINS_URL = '<?php echo plugins_url(); ?>';
    ajaxurl = '<?php echo $ajax_url; ?>';

    jQuery(document).ready(function() {
        let bfmElm = jQuery('#file-manager-pro-wrapper');
        let bfm = bfmElm.elfinder({

            url: ajaxurl,
            <?php if ($FMP->no_permission()) : ?>
                handlers: {
                    dblclick: function(event, elfinderInstance) {
                        console.log(elfinderInstance);
                        return false;
                    },
                    //auto::  open: function(event, ElfinderInstance){return false;},
                },
                contextmenu: {
                    navbar: [],
                    // current directory menu
                    cwd: ['reload', 'back', 'sort'],
                    // current directory file menu
                    files: [],
                },
                uiOptions: {
                    toolbar: false,
                },
            <?php endif; ?>
            debug: ['error', 'warning', 'event-destroy'],
            customData: {
                action: 'gb_file_manager_pro_connector',
                file_manager_pro_security_token: '<?php echo wp_create_nonce("file-manager-pro-security-token"); ?>'
            },
            lang: '<?php if (isset($language_code)) echo $language_code ?>',
            requestType: 'post',
            messages: {
                aa: "Hell of Error",
            },
            width: '<?php echo $FileManager->options['file_manager_settings']['size']['width'] ?>',
            height: '<?php echo $FileManager->options['file_manager_settings']['size']['height'] ?>',

        }).elfinder('instance');
        console.log('first', bfm)
        bfmElm.on('load', function(event) {
            console.log('sec', event)
         });
    });
</script>

<style>
    div.ui-widget-content:nth-child(11)>div:nth-child(1) {
        display: none !important;
    }

    .elfinder-info-tb>tbody:nth-child(1)>tr:nth-child(3) {
        display: none !important;
    }
</style>
<?php $FMP->shortcode_output = ob_get_clean(); ?>