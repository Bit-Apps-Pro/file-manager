<?php
/**
 * 
 * @file extensions.php A brief description of our extensions.
 * 
 * */

// Security Check
if( !defined( 'ABSPATH' ) ) die();
global $FileManager;
?>
<div class='fm-extensions'>
	
	<h2><?php _e("Extension", 'file-manager'); ?></h2>
	<p>
		<h4><a href='<?php echo esc_url($FileManager->giribaz_landing_page); ?>'>File Manager Pro</a></h4>
		<ul>
			<li><?php _e("Use file manager on the frontend", 'file-manager'); ?></li>
			<li><?php _e("Users can perform file operations", 'file-manager'); ?></li>
			<li><?php _e("Control what type of file can be uploaded", 'file-manager'); ?></li>
			<li><?php _e("Control which user/usergroup can perform file operation", 'file-manager'); ?></li>
			<li><?php _e("Control what type of file can be uploaded or downloaded", 'file-manager'); ?></li>
			<li><?php _e("Ban any user from performing any file operations", 'file-manager'); ?></li>
			<li><?php _e("Set individual folders for each user", 'file-manager'); ?></li>
			<li><?php _e("Set common folders for user roles.", 'file-manager'); ?></li>
			<li><?php _e("Set a common folders for all users.", 'file-manager'); ?></li>
			<li><?php _e("Use single shortcode to allow all logged in users to perform file operations.", 'file-manager'); ?></li>
			<li><?php _e("Show file manager for guest users.", 'file-manager'); ?></li>
			<li><?php _e("Control uploadable file size", 'file-manager'); ?></li>
			<li><?php _e("Add additional folder for user & user role", 'file-manager'); ?></li>
			<li><b><?php _e("Create any kind of file access strcture for your user's on the frontend.", 'file-manager'); ?></b></li>
		</ul>
	</p>
	<br/>
	<a class='fm-call-toaction' href='https://www.giribaz.com/wordpress-file-manager-plugin'><?php _e("Get it now!", 'file-manager'); ?></a>
	<br/>
	<br/>
</div>
