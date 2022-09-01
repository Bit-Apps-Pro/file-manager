<?php

/**
 * 
 * File Manager Frontend View page
 * 
 * */

// Security Check
defined('ABSPATH') or die();
global $FMP, $FileManager;

//~ pr($data);

if( !is_user_logged_in() && empty($settings['guest']) ) die();

// Adding necessary Scripts these will be added to the footer section.
// Jquery UI CSS
wp_enqueue_style( 'fmp_jquery-ui-css', $FMP->url('jquery-ui-1.11.4/jquery-ui.min.css') );

// Jquery UI theme
wp_enqueue_style( 'fmp_jquery-ui-css-theme', $FMP->url('jquery-ui-1.11.4/jquery-ui.theme.css') );

// elFinder CSS
wp_enqueue_style( 'fmp_elfinder-css', $FMP->url('elFinder/css/elfinder.min.css') );

// elFinder theme CSS
wp_enqueue_style( 'fmp_elfinder-theme-css', $FMP->url('elFinder/css/theme.css') );

// elFinder Scripts depends on jQuery UI core, selectable, draggable, droppable, resizable, dialog and slider.
wp_enqueue_script( 'fmp_elfinder-script', $FMP->url('elFinder/js/elfinder.full.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-selectable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-resizable', 'jquery-ui-dialog', 'jquery-ui-slider', ) );

$ajax_url = site_url() . "/wp-admin/admin-ajax.php";

?>

<div id='file-manager-pro-wrapper'></div>

<script>

PLUGINS_URL = '<?php echo plugins_url();?>';
ajaxurl = '<?php echo $ajax_url; ?>';

jQuery(document).ready(function(){
	
	jQuery('#file-manager-pro-wrapper').elfinder({
		url: ajaxurl,
		debug : ['error', 'warning', 'event-destroy'],
		customData:{action: 'connector_public'},
	});
});

</script>

<style>
	div.ui-widget-content:nth-child(11) > div:nth-child(1){
		display: none !important;
	}
	
	.elfinder-info-tb > tbody:nth-child(1) > tr:nth-child(3){
		display: none !important;
	}
</style>
