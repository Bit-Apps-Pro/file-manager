<?php
/**
 * 
 * @file utility.php Utility information about the plugin
 * 
 * */

// Security Check
if( !defined( 'ABSPATH' ) ) die();
global $FileManager;
?>
<?php

?>
<table>
	
	<tr>
		<td><?php esc_html_e("Current Media Directory", 'file-manager'); ?></td>
		<td>
			<?php 
				$wp_upload_dir = wp_upload_dir();
				echo $wp_upload_dir['path'];
			?>
		</td>
	</tr>
	<tr>
		<td><?php _e("PHP version", 'file-manager'); ?></td>
		<td><?php echo phpversion(); ?></td>
	</tr>
	
	<tr>
		<td><?php _e("Maximum file upload size", 'file-manager'); ?></td>
		<td><?php echo ini_get('upload_max_filesize'); ?></td>
	</tr>

	<tr>
		<td><?php _e("Post maximum file upload size", 'file-manager'); ?></td>
		<td><?php echo ini_get('post_max_size'); ?></td>
	</tr>
	
	<tr>
		<td><?php _e("Memory Limit", 'file-manager');?></td>
		<td><?php echo ini_get('memory_limit'); ?></td>
	</tr>
	
	<tr>
		<td><?php _e("Timeout", 'file-manager');?></td>
		<td><?php echo ini_get('max_execution_time'); ?></td>
	</tr>
	
	<tr>
		<td><?php _e("Browser and OS", 'file-manager'); ?></td>
		<td><?php echo $_SERVER['HTTP_USER_AGENT']; ?></td>
	</tr>
	
	<tr>
		<td>DISALLOW_FILE_EDIT</td>
		<td>
		<?php
			if(defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) echo "TRUE";
				else echo "FALSE";
		?>
		</td>
	</tr>
	
</table>
