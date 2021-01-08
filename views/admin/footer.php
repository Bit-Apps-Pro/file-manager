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
		<li><a href='http://giribaz.com/faq/'><?php _e("FAQ", 'file-manager'); ?></a></li>
		<li><a href='https://wordpress.org/plugins/file-manager/changelog/'><?php _e("Changelog", 'file-manager'); ?></a></li>
		<li><a href='http://giribaz.com/contacts/'><?php _e("Contacts", 'file-manager'); ?></a></li>
		<li><a href='http://giribaz.com/documentations/'><?php _e("Docs", 'file-manager'); ?></a></li>
		<li><a href='<?= $FileManager->feedback_page; ?>'><?php _e("Review", 'file-manager'); ?></a></li>
		<li><a href='<?= $FileManager->support_page; ?>'><?php _e("Help & Support", 'file-manager'); ?></a></li>
		<li><a href='<?= $FileManager->site; ?>'>Giribaz</a></li>
	</ul>

</div>
