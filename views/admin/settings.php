<?php
if(!defined('ABSPATH')) die();
global $FileManager;

// Settings processing
if( isset( $_POST ) && !empty( $_POST ) ){

	if( ! wp_verify_nonce( $_POST['file-manager-settings-security-token'] ,'file-manager-settings-security-token') || !current_user_can( 'manage_options' ) ) wp_die();

	$_POST['show_url_path'] = sanitize_text_field($_POST['show_url_path']);
	if( isset($_POST['show_url_path']) && ($_POST['show_url_path'] == 'show' || $_POST['show_url_path'] == 'hide') ) $FileManager->options['file_manager_settings']['show_url_path'] = $_POST['show_url_path'];
	
	$FileManager->options['file_manager_settings']['root_folder_path']  = sanitize_file_name($_POST['root_folder_path']) ? sanitize_file_name($_POST['root_folder_path']) : '';
	$FileManager->options['file_manager_settings']['root_folder_url']  = esc_url_raw($_POST['root_folder_url']) ? esc_url_raw($_POST['root_folder_url']) : '';

	$FileManager->options['file_manager_settings']['language'] = sanitize_text_field($_POST['language']);

	$FileManager->options['file_manager_settings']['size']['width']  = filter_var($_POST['width'], FILTER_VALIDATE_INT) ? $_POST['width'] : 'auto';
	$FileManager->options['file_manager_settings']['size']['height']  = filter_var($_POST['height'], FILTER_VALIDATE_INT) ? $_POST['height'] : 600;
	$FileManager->options['file_manager_settings']['fm-show-hidden-files']  = isset($_POST['fm-show-hidden-files']) && !empty($_POST['fm-show-hidden-files']) ? filter_var($_POST['fm-show-hidden-files'], FILTER_SANITIZE_STRING) : '';

}

$admin_page_url = admin_url()."admin.php?page={$FileManager->prefix}";

if( !isset($_GET['sub_page']) || empty($_GET['sub_page']) ) $_GET['sub_page'] = 'files';
// Escaping data
$_GET['sub_page'] = preg_replace( "/[<>#$%]/", "", $_GET['sub_page']);
// Sanitizing data
$_GET['sub_page'] = sanitize_text_field($_GET['sub_page']);

// Enqueing admin assets
$FileManager->admin_assets();

// Language
include 'language-code.php';
global $fm_languages;
?>
<?php require_once( 'header.php' ); ?>
<div class='fm-container'>

	<div class='col-main'>

		<div class='gb-fm-row fmp-settings'>

			<h2><?php _e("Settings", 'file-manager'); ?></h2>

			<form action='' method='post' class='fmp-settings-form'>
					<input type='hidden' name='file-manager-settings-security-token' value='<?php echo wp_create_nonce('file-manager-settings-security-token'); ?>'>
					<table>
						<tr>
							<td rowspan="2"><h4><?php _e("URL and Path", 'file-manager'); ?></h4></td>
							<td>
								<label for='show_url_path_id'> <?php _e("Show", 'file-manager'); ?> </label>
								<input type='radio' name='show_url_path' id='show_url_path_id' value='show' <?php  if( isset( $FileManager->options['file_manager_settings']['show_url_path'] ) && !empty( $FileManager->options['file_manager_settings']['show_url_path'] ) && $FileManager->options['file_manager_settings']['show_url_path'] == 'show' ) echo 'checked'; ?>/>

								<label for='hide_url_path_id'> Hide </label>
								<input type='radio' name='show_url_path' id='hide_url_path_id' value='hide' <?php  if( isset( $FileManager->options['file_manager_settings']['show_url_path'] ) && !empty( $FileManager->options['file_manager_settings']['show_url_path'] ) && $FileManager->options['file_manager_settings']['show_url_path'] == 'hide' ) echo 'checked'; ?>/>
							</td>
						</tr>
						<tr>
							
							<td>
								<label for='root_folder_path_id'> <?php _e("Root Folder Path", 'file-manager'); ?> </label>
								<input type='text' name='root_folder_path' id='root_folder_path_id' value='<?php  if( isset( $FileManager->options['file_manager_settings']['root_folder_path'] ) && !empty( $FileManager->options['file_manager_settings']['root_folder_path'] ) ) echo esc_attr($FileManager->options['file_manager_settings']['root_folder_path']); ?>' />

								<label for='root_folder_url_id'> <?php _e("Root Folder URL", 'file-manager'); ?> </label>
								<input type='text' name='root_folder_url' id='root_folder_url_id' value='<?php  if( isset( $FileManager->options['file_manager_settings']['root_folder_url'] ) && !empty( $FileManager->options['file_manager_settings']['root_folder_url'] ) ) echo esc_attr($FileManager->options['file_manager_settings']['root_folder_url']); ?>' />
							</td>
						</tr>
						<tr>
							<td></td>
							<td><small><?php _e("Default Path:", 'file-manager'); ?> <b><?php echo ABSPATH;?></b></small></td>
						</tr>
						<tr>
							<td></td>
							<td><small><?php _e("Default URL:", 'file-manager'); ?> <b><?php echo site_url();?></b></small></td>
						</tr>
						<tr>
							<td></td>
							<td style="text-align: center;"><small><?php _e("Root folder path and URL must be correct, otherwise it won't work.", 'file-manager'); ?></small></td>
						</tr>
						<tr>
							<td><h4><?php _e("Select Language", 'file-manager'); ?></h4></td>
							<td>
								<?php
									$lang = $fm_languages->available_languages();
									if(!isset($FileManager->options['file_manager_settings']['language'])) $FileManager->options['file_manager_settings']['language'] = array();
									if(!is_array($FileManager->options['file_manager_settings']['language'])) $language_settings = unserialize(stripslashes($FileManager->options['file_manager_settings']['language']));
										else $language_settings = $FileManager->options['file_manager_settings']['language'];
									$language_code = $language_settings['code'];

								?>
								<select name='language'>
									<?php foreach($lang as $L): ?>
									<option <?php if($language_code == $L['code']) echo "selected='selected'"; ?> value='<?php echo esc_attr(serialize($L)); ?>'><?php echo $L['name']?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><h4><?php _e("Size", 'file-manager'); ?></h4></td>
							<td>
								<label for='fm-width-id'><?php _e("Width", 'file-manager'); ?></label><input id='fm-width-id' type='text' name='width' value='<?php if(isset($FileManager->options['file_manager_settings']['size']['width']) && !empty($FileManager->options['file_manager_settings']['size']['width'])) echo esc_attr($FileManager->options['file_manager_settings']['size']['width']); else echo 'auto';?>'>
								<label for='fm-height-id'><?php _e("Height", 'file-manager'); ?></label><input id='fm-height-id' type='text' name='height' value='<?php if(isset($FileManager->options['file_manager_settings']['size']['height']) && !empty($FileManager->options['file_manager_settings']['size']['height'])) echo esc_attr($FileManager->options['file_manager_settings']['size']['height']); else echo 400;?>'>
							</td>
						</tr>
						<tr>
							<td><h4><label for='fm-width-id'><?php _e("Show Hidden Files", 'file-manager'); ?></label></h4></td>
							<td>
								<input id='fm-media-sync-id' type='checkbox' name='fm-show-hidden-files' <?php if(isset($FileManager->options['file_manager_settings']['fm-show-hidden-files']) && !empty($FileManager->options['file_manager_settings']['fm-show-hidden-files'])) echo "checked";?> value="fm-show-hidden-files">
								<small><?php _e("When checked hidden files and folders will be shown to the users.", 'file-manager'); ?></small>
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<input type='submit' value='<?php _e("Save", 'file-manager'); ?>' />
							</td>
						</tr>
					</table>

			</form>

		</div>

		<div class='gb-fm-row fm-data'>
			<?php require_once('utility.php'); ?>
		</div>

	</div>

	<?php require_once('sidebar.php'); ?>

</div>

<?php require_once('footer.php'); ?>
<!--

-->
