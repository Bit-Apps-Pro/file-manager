<?php
/**
 *
 * @file header.php Header file
 *
 * */

// Security Check
if( !defined( 'ABSPATH' ) ) die();
global $FileManager;
wp_enqueue_style( 'fmp-admin-style' );
wp_enqueue_script( 'fmp-admin-script' );
?>
<div class='fm-header'>

	<h1><img class='fm-logo' src='<?php echo plugin_dir_url(__FILE__) . '../../img/icon-128x128.png';?>'><?php echo $FileManager->name; ?></h1>

	<ul class='top-right-menu'>
		<li><a href='<?php echo $FileManager->support_page; ?>'><?php _e("Need help?", 'file-manager'); ?></a></li>
		<li><a href='<?php echo $FileManager->feedback_page; ?>'><?php _e("Leave us a feedback", 'file-manager'); ?></a></li>
		<li class='fm-marketing'><a href='<?php echo $FileManager->giribaz_landing_page; ?>'><?php _e("Extend", 'file-manager'); ?></a></li>
	</ul>

</div>
