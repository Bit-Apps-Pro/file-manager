<?php
/**
 *
 * @file footer.php Footer file of the plugin
 *
 * */
// Security check
if( !defined('ABSPATH') ) die();
global $FileManager;
?>
<div class='fm-footer'>
	
	<ul>
		<li><a href='https://wordpress.org/plugins/file-manager/changelog/'><?php _e("Changelog", 'file-manager'); ?></a></li>
		<li><a href='https://www.bitapps.pro/contact'><?php _e("Contacts", 'file-manager'); ?></a></li>
		<!-- <li><a href='https://wpjos.com/documentations/'><?php _e("Docs", 'file-manager'); ?></a></li> -->
		<li><a href='<?php esc_url($FileManager->feedback_page); ?>'><?php _e("Review", 'file-manager'); ?></a></li>
		<!-- <li><a href='<?php esc_url($FileManager->support_page); ?>'><?php _e("Help & Support", 'file-manager'); ?></a></li> -->
		<li><a href='<?php echo esc_url($FileManager->site); ?>'>Bit Apps</a></li>
	</ul>

</div>
