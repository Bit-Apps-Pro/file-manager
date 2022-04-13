<?php
if (!defined('ABSPATH')) {
    die();
}

global $FileManager;

// Settings processing
if (isset($_POST) && !empty($_POST)) {

    if (!wp_verify_nonce($_POST['file-manager-settings-security-token'], 'file-manager-settings-security-token') || !current_user_can('manage_options')) {
        wp_die();
    }

    $_POST['show_url_path'] = sanitize_text_field($_POST['show_url_path']);
    if (isset($_POST['show_url_path']) && ($_POST['show_url_path'] == 'show' || $_POST['show_url_path'] == 'hide')) {
        $FileManager->options['file_manager_settings']['show_url_path'] = $_POST['show_url_path'];
    }
    $FileManager->options['file_manager_settings']['root_folder_path'] = sanitize_text_field($_POST['root_folder_path']) ? sanitize_text_field(truepath($_POST['root_folder_path'])) : '';
    $FileManager->options['file_manager_settings']['root_folder_url'] = esc_url_raw($_POST['root_folder_url']) ? esc_url_raw($_POST['root_folder_url']) : '';

    $FileManager->options['file_manager_settings']['language'] = sanitize_text_field($_POST['language']);

    $FileManager->options['file_manager_settings']['size']['width'] = filter_var($_POST['width'], FILTER_VALIDATE_INT) ? $_POST['width'] : 'auto';
    $FileManager->options['file_manager_settings']['size']['height'] = filter_var($_POST['height'], FILTER_VALIDATE_INT) ? $_POST['height'] : 600;
    $FileManager->options['file_manager_settings']['fm-show-hidden-files'] = isset($_POST['fm-show-hidden-files']) && !empty($_POST['fm-show-hidden-files']) ? filter_var($_POST['fm-show-hidden-files'], FILTER_SANITIZE_STRING ) : '';
    $FileManager->options['file_manager_settings']['fm-create-hidden-files-folders'] = isset($_POST['fm-create-hidden-files-folders']) && !empty($_POST['fm-create-hidden-files-folders']) ? filter_var($_POST['fm-create-hidden-files-folders'], FILTER_SANITIZE_STRING) : '';
    $FileManager->options['file_manager_settings']['fm-create-trash-files-folders'] = isset($_POST['fm-create-trash-files-folders']) && !empty($_POST['fm-create-trash-files-folders']) ? filter_var($_POST['fm-create-trash-files-folders'], FILTER_SANITIZE_STRING) : '';
    $FileManager->options['file_manager_settings']['fm_root_folder_name'] = isset($_POST['fm_root_folder_name']) && !empty($_POST['fm_root_folder_name']) ? filter_var($_POST['fm_root_folder_name'], FILTER_SANITIZE_STRING) : 'WP Root';
	$FileManager->options['file_manager_settings']['fm_default_view_type'] = isset($_POST['fm_default_view_type']) && !empty($_POST['fm_default_view_type']) ? filter_var($_POST['fm_default_view_type'], FILTER_SANITIZE_STRING) : 'icons';
	$FileManager->options['file_manager_settings']['fm-remember-last-dir'] = isset($_POST['fm-remember-last-dir']) && !empty($_POST['fm-remember-last-dir']) ? filter_var($_POST['fm-remember-last-dir'], FILTER_SANITIZE_STRING ) : 'checked';
	$FileManager->options['file_manager_settings']['fm-clear-history-on-reload'] = isset($_POST['fm-clear-history-on-reload']) && !empty($_POST['fm-clear-history-on-reload']) ? filter_var($_POST['fm-clear-history-on-reload'], FILTER_SANITIZE_STRING ) : 'checked';
	$FileManager->options['file_manager_settings']['fm_display_ui_options'] = isset($_POST['fm_display_ui_options']) && !empty($_POST['fm_display_ui_options']) ? filter_var_array($_POST['fm_display_ui_options']) : ['toolbar', 'places', 'tree', 'path', 'stat'];

}

/**
 * This function is to replace PHP's extremely buggy realpath().
 * @param string The original path, can be relative etc.
 * @return string The resolved path, it might not exist.
 */
function truepath($path)
{
    // whether $path is unix or not
    $unipath = strlen($path) == 0 || $path[0] != '/';
    // attempts to detect if path is relative in which case, add cwd
    if (strpos($path, ':') === false && $unipath) {
        $path = getcwd() . DIRECTORY_SEPARATOR . $path;
    }

    // resolve path parts (single dot, double dot and double delimiters)
    $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
    $absolutes = array();
    foreach ($parts as $part) {
        if ('.' == $part) {
            continue;
        }

        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }
    $path = implode(DIRECTORY_SEPARATOR, $absolutes);
    // resolve any symlinks
    if (file_exists($path) && linkinfo($path) > 0) {
        $path = readlink($path);
    }

    // put initial separator that could have been lost
    $path = !$unipath ? '/' . $path : $path;
    return $path;
}

$admin_page_url = admin_url() . "admin.php?page={$FileManager->prefix}";

// Enqueing admin assets
$FileManager->admin_assets();

// Language
include 'language-code.php';
global $fm_languages;
?>
<?php require_once 'header.php';?>
<div class='fm-container'>

	<div class='col-main'>

		<div class='gb-fm-row fmp-settings'>

			<h2><?php _e("Settings", 'file-manager');?></h2>

			<form action='' method='post' class='fmp-settings-form'>
					<input type='hidden' name='file-manager-settings-security-token' value='<?php echo wp_create_nonce('file-manager-settings-security-token'); ?>'>
					<table>
						<tr>
							<td rowspan="2"><h4><?php _e("URL and Path", 'file-manager');?></h4></td>
							<td>
								<label for='show_url_path_id'> <?php _e("Show", 'file-manager');?> </label>
								<input type='radio' name='show_url_path' id='show_url_path_id' value='show' <?php if (isset($FileManager->options['file_manager_settings']['show_url_path']) && !empty($FileManager->options['file_manager_settings']['show_url_path']) && $FileManager->options['file_manager_settings']['show_url_path'] == 'show') {
    echo 'checked';
}
?>/>

								<label for='hide_url_path_id'> Hide </label>
								<input type='radio' name='show_url_path' id='hide_url_path_id' value='hide' <?php if (isset($FileManager->options['file_manager_settings']['show_url_path']) && !empty($FileManager->options['file_manager_settings']['show_url_path']) && $FileManager->options['file_manager_settings']['show_url_path'] == 'hide') {
    echo 'checked';
}
?>/>
							</td>
						</tr>
						<tr>

							<td>
								<label for='root_folder_path_id'> <?php _e("Root Folder Path", 'file-manager');?> </label>
                                <input type='text' name='root_folder_path' onkeyup="pathVlidation()" id='root_folder_path_id' value='<?php if (isset($FileManager->options['file_manager_settings']['root_folder_path']) && !empty($FileManager->options['file_manager_settings']['root_folder_path'])) {
    echo esc_attr($FileManager->options['file_manager_settings']['root_folder_path']);
}
?>' /></br>
<span id="fm_path_err"></span>
								<script>
									function pathVlidation(){
										let path = document.getElementById('root_folder_path_id').value;
										let res = path.match(/^[a-zA-Z]:\\([a-zA-Z0-9.\-*.+]+([ ][a-zA-Z0-9.\-*.+]+)*\\)*([a-zA-Z0-9.\-*.+]+([ ][a-zA-Z0-9.\-*.+]+)*)*$/gi);
										let span = document.getElementById('fm_path_err');
										if(res == null){
											span.innerHTML = "Path is wrong! Please enter a valid Path.";
											span.style.color ='red';
											span.style.visibility = 'visible'
										}else if(res.length){
											span.style.visibility = 'hidden'
										}
									}
								</script>
								<br>
                                <small><?php _e("Default Path:", 'file-manager');?> <b><?php echo ABSPATH; ?></b></small>
                                <br><br>
								<label for='root_folder_url_id'> <?php _e("Root Folder URL", 'file-manager');?> </label>
                                &nbsp;
                                <input type='text' name='root_folder_url' onkeyup="validURL()"  id='root_folder_url_id' value='<?php if (isset($FileManager->options['file_manager_settings']['root_folder_url']) && !empty($FileManager->options['file_manager_settings']['root_folder_url'])) {
    echo esc_attr($FileManager->options['file_manager_settings']['root_folder_url']);
}
?>' /><br/>
								<span id="url_error"></span>
                                <br>
                                <small><?php _e("Default URL:", 'file-manager');?> <b><?php echo site_url(); ?></b></small>
								<script>
								function validURL() {
									string = document.getElementById('root_folder_url_id').value;
									if(string){
										var res = string.match(/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/g);
										var span = document.getElementById('url_error')

										if(res == null){
											span.innerHTML = "URL is wrong! Please enter a valid URL.";
											span.style.color ='red';
											span.style.visibility = 'visible'
										}else if(res.length){
											span.style.visibility = 'hidden'
										}
									}
								}
								</script>
                            </td>
						</tr>
						<!-- <tr>
							<td></td>
							<td><small><?php _e("Default Path:", 'file-manager');?> <b><?php echo ABSPATH; ?></b></small></td>
						</tr>
						<tr>
							<td></td>
							<td><small><?php _e("Default URL:", 'file-manager');?> <b><?php echo site_url(); ?></b></small></td>
						</tr> -->
						<tr>
							<td></td>
							<td style="text-align: center;"><small><?php _e("Root folder path and URL must be correct, otherwise it won't work.", 'file-manager');?></small></td>
						</tr>
						<tr>
							<td><h4><?php _e("Select Language", 'file-manager');?></h4></td>
							<td>
								<?php
$lang = $fm_languages->available_languages();
if (!isset($FileManager->options['file_manager_settings']['language'])) {
    $FileManager->options['file_manager_settings']['language'] = array();
}

if (!is_array($FileManager->options['file_manager_settings']['language'])) {
    $language_settings = unserialize(stripslashes($FileManager->options['file_manager_settings']['language']));
} else {
    $language_settings = $FileManager->options['file_manager_settings']['language'];
}

$language_code = $language_settings['code'];

?>
								<select name='language'>
									<?php foreach ($lang as $L): ?>
									<option <?php selected($L['code'], $language_code);?> value='<?php echo esc_attr(serialize($L)); ?>'><?php echo esc_html($L['name']); ?></option>
									<?php endforeach;?>
								</select>
							</td>
						</tr>
						<tr>
							<td><h4><?php _e("Size", 'file-manager');?></h4></td>
							<td>
								<label for='fm-width-id'><?php _e("Width", 'file-manager');?></label>
								&nbsp;&nbsp;&nbsp;<input id='fm-width-id' type='text' name='width' value='<?php if (isset($FileManager->options['file_manager_settings']['size']['width']) && !empty($FileManager->options['file_manager_settings']['size']['width'])) {
    echo esc_attr($FileManager->options['file_manager_settings']['size']['width']);
} else {
    echo 'auto';
}
?>'>
<br/>
								<label for='fm-height-id'><?php _e("Height", 'file-manager');?></label>
								&nbsp;&nbsp;<input id='fm-height-id' type='text' name='height' value='<?php if (isset($FileManager->options['file_manager_settings']['size']['height']) && !empty($FileManager->options['file_manager_settings']['size']['height'])) {
    echo esc_attr($FileManager->options['file_manager_settings']['size']['height']);
} else {
    echo 400;
}
?>'>
							</td>
						</tr>
						<tr>
							<td><h4><label for='fm-width-id'><?php _e("Show Hidden Files", 'file-manager');?></label></h4></td>
							<td>
								<input id='fm-media-sync-id' type='checkbox' name='fm-show-hidden-files' <?php if (isset($FileManager->options['file_manager_settings']['fm-show-hidden-files']) && !empty($FileManager->options['file_manager_settings']['fm-show-hidden-files'])) {
    echo "checked";
}
?> value="fm-show-hidden-files">
								<small><?php _e("When checked hidden files and folders will be shown to the users.", 'file-manager');?></small>
							</td>
						</tr>
						<tr>
							<td><h4><label for='fm-width-id'><?php _e("Allow Create/Upload Hidden Files/Folders", 'file-manager');?></label></h4></td>
							<td>
								<input id='fm-hidden-file-id' type='checkbox' name='fm-create-hidden-files-folders' <?php if (isset($FileManager->options['file_manager_settings']['fm-create-hidden-files-folders']) && !empty($FileManager->options['file_manager_settings']['fm-create-hidden-files-folders'])) {
    echo "checked";
}
?> value="fm-create-hidden-files-folders">
								<small><?php _e("When checked hidden files and folders will be create by the users.", 'file-manager');?></small>
							</td>
						</tr>
						<tr>
							<td><h4><label for='fm-width-id'><?php _e("Allow Trash", 'file-manager');?></label></h4></td>
							<td>
								<input id='fm-trash-id' type='checkbox' name='fm-create-trash-files-folders' <?php if (isset($FileManager->options['file_manager_settings']['fm-create-trash-files-folders']) && !empty($FileManager->options['file_manager_settings']['fm-create-trash-files-folders'])) {
    echo "checked";
}
?> value="fm-create-trash-files-folders">
								<small><?php _e("When checked deleted files and folder will save here.", 'file-manager');?></small>
								<br/>
								<small><?php _e("Default Path:", 'file-manager');?> <b><?php echo FM_TRASH_DIR_PATH; ?></b></small>
							</td>
						</tr>
						<tr>
							<td><h4><?php _e("Root Folder Name", 'file-manager');?></h4></td>
							<td>
								<label for='fm-root-folder-name-id'></label>
								<input id='fm-root-folder-name-id' type='text' name='fm_root_folder_name' value='<?php if (isset($FileManager->options['file_manager_settings']['fm_root_folder_name']) && !empty($FileManager->options['file_manager_settings']['fm_root_folder_name'])) {
    echo esc_attr($FileManager->options['file_manager_settings']['fm_root_folder_name']);
} else {
    echo 'WP Root';
}
?>'>
							</td>
						</tr>
						<tr>
							<td><h4><?php _e("Default View Type", 'file-manager');?></h4></td>
							<td>
								<label for='fm-root-folder-name-id'></label>
								<select id="fm_default_view_type" name="fm_default_view_type" >
								<option disabled>Select Defualt View Type</option>
								<option <?php selected('icons', $FileManager->options['file_manager_settings']['fm_default_view_type']);?> value='icons'>Icons</option>
								<option <?php selected('list', $FileManager->options['file_manager_settings']['fm_default_view_type']);?> value='list'>List</option>
								</select>
						</td>
						</tr>
						<tr>
							<td><h4><label for='fm-remember-last-dir'><?php _e("Remember Last Directory", 'file-manager');?></label></h4></td>
							<td>
								<input id='fm-remember-last-dir' type='checkbox' name='fm-remember-last-dir' <?php if (isset($FileManager->options['file_manager_settings']['fm-remember-last-dir']) && !empty($FileManager->options['file_manager_settings']['fm-remember-last-dir'])) echo "checked"?> value="fm-remember-last-dir">
								<small><?php _e("Remeber last opened dir to open it after reload.", 'file-manager');?></small>
							</td>
						</tr>
						<tr>
							<td><h4><label for='fm-clear-history-on-reload'><?php _e("Clrear History On Reload", 'file-manager');?></label></h4></td>
							<td>
								<input id='fm-clear-history-on-reload' type='checkbox' name='fm-clear-history-on-reload' <?php if (isset($FileManager->options['file_manager_settings']['fm-clear-history-on-reload']) && !empty($FileManager->options['file_manager_settings']['fm-clear-history-on-reload'])) echo "checked"?> value="fm-clear-history-on-reload">
								<small><?php _e("Clear historys(elFinder) on reload(not browser).", 'file-manager');?></small>
							</td>
						</tr>
						<tr>
							<td><h4><?php _e("Default View Type", 'file-manager');?></h4></td>
							<td>
								<label for='fm-root-folder-name-id'></label>
								<select id="fm_display_ui_options" name="fm_display_ui_options[]" multiple>
								<option disabled>Select Defualt View Type</option>
								<?php
								$uioptions = ['toolbar', 'places', 'tree', 'path', 'stat'];
								foreach($uioptions as $place){ ?>
									<option <?php if(in_array($place, isset($FileManager->options['file_manager_settings']['fm_display_ui_options'])? $FileManager->options['file_manager_settings']['fm_display_ui_options']: $uioptions)) echo 'selected' ?> value='<?php echo $place; ?>' ><?php echo $place; ?></option>

								<?php } ?>
								</select>
						</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<input type='submit' value='<?php _e("Save", 'file-manager');?>' />
							</td>
						</tr>
					</table>

			</form>

		</div>


	</div>

	<?php require_once 'sidebar.php';?>

</div>

<?php require_once 'footer.php';?>
<!--

-->
