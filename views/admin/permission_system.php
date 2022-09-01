<?php

// Security Check
defined('ABSPATH') or die();

global $wp_roles, $wpdb, $FileManager, $FMP;

// Processing Post data
if (!empty($_POST)) {
    // Checks if the current user have enough authorization to operate.
    if (!wp_verify_nonce($_POST['file_manager_pro_settings_security_token'], 'file-manager-pro-settings-security-token') || !current_user_can('manage_options')) wp_die();
    check_ajax_referer('file-manager-pro-settings-security-token', 'file_manager_pro_settings_security_token');
    update_option('fmp_permission_system', $_POST);
}
// delete_option('fmp_permission_system');
$previous_settings = get_option('fmp_permission_system');
// pr($previous_settings);

// Extracting user role
$roles = array_keys($wp_roles->roles);

$users = $wpdb->get_results("SELECT id, user_login FROM {$wpdb->prefix}users;", ARRAY_A);

// Listing all operations
$operations = $FMP->list_of_operations;

// File Types
$file_types = array('text', 'image', 'application', 'video', 'audio');

// Initializing root folder path
$default_root_folder_path = $FileManager->upload_path . DS;
if (isset($previous_settings['root_folder']) && !empty($previous_settings['root_folder'])) $root_folder_path = stripslashes($previous_settings['root_folder']);
else $root_folder_path = $default_root_folder_path;

// Initilizing root folder URL
$default_root_folder_url = $FileManager->upload_url;
if (isset($previous_settings['root_folder_url']) && !empty($previous_settings['root_folder_url'])) $root_folder_url = stripslashes($previous_settings['root_folder_url']);
else $root_folder_url = $default_root_folder_url;
?>

<?php

global $FileManager;

//auto::  $FileManager->admin_assets();

?>

<?php require_once 'header.php'; ?>

<div class='fm-container'>

    <div class='col-main col-main-permission-system'>

        <div class='gb-fm-row'>
            <h3>Shortcode</h3>
            <table class='file-manager-shortcode-table'>

                <tr>
                    <th>Description</th>
                    <th>Key</th>
                </tr>

                <tr>
                    <td>Paste the shortcode anywhere you want to see the file manager.</td>
                    <td class='file-manager-shortcode-table-shortcode'>[file_manager]</td>
                </tr>
                <tr>
                    <td>Copy and paste this php function anywhere in you template to show file manager.</td>
                    <td class='file-manager-shortcode-table-shortcode'>file_manager_permission_system_frontend()</td>
                </tr>
                <tr>
                    <td>Use File Manager as a widget from the widget panel.</td>
                    <td class='file-manager-shortcode-table-shortcode'>File Manager Widget</td>
                </tr>
            </table>
        </div>

        <div class='gb-fm-row'>
            <form method="post" action="">
                <input type='hidden' name='file_manager_pro_settings_security_token' value='<?php echo wp_create_nonce("file-manager-pro-settings-security-token"); ?>'>

                <label for='do-not-use-for-admin-id'>Do not use this settings for administrator </label>
                <input type='checkbox' name='do-not-use-for-admin' id='do-not-use-for-admin-id' value='do-not-use-for-admin' <?php if (isset($previous_settings['do-not-use-for-admin']) && !empty($previous_settings['do-not-use-for-admin'])) echo "checked"; ?>>

                <h3>Allowed MIME types and size</h3>
                <?php foreach ($file_types as $file_type) : ?>
                    <input type='checkbox' name="file_type[]" value="<?php echo $file_type; ?>" id='<?php echo $file_type . "_id"; ?>' <?php if (isset($file_type, $previous_settings['file_type']) && in_array($file_type, $previous_settings['file_type'])) echo "checked"; ?> />
                    <label for="<?php echo $file_type . "_id"; ?>"><?php echo $FMP->__p($file_type); ?></label>
                <?php endforeach; ?>
                <small><a href="http://www.iana.org/assignments/media-types/media-types.xhtml">What is MIME types?</a></small>
                </br>
                </br>
                <label for="file_size_id">Maximum File Size</label>
                <input type='number' name="file_size" id='file_size_id' value="<?php if (isset($previous_settings['file_size']) && !empty($previous_settings['file_size'])) echo $previous_settings['file_size'];
                                                                                else echo 2; ?>" />
                <small>MB(mega byte).</small><small> 0 for unlimited size</small>

                </br>
                </br>
                <h3>Root Folder</h3>
                <label for="root_folder_id">Root Folder Path</label>
                <input style="width:500px;" type='text' placeholder='<?php echo $default_root_folder_path; ?>' name="root_folder" id='root_folder_id' value="<?php echo $root_folder_path; ?>" />
                <small>default is <b><?php echo $default_root_folder_path; ?></b></small>

                </br>
                </br>
                <label for="root_folder_url_id">Root Folder URL</label>
                <input style="width:500px;" type='text' placeholder='<?php echo $default_root_folder_url; ?>' name="root_folder_url" id='root_folder_url_id' value="<?php echo $root_folder_url; ?>" />
                <small>default is <b><?php echo $default_root_folder_url; ?></b></small>

                <br />
                <br />

                <h3>Folder Options</h3>
                <input type='checkbox' name="folder_options-single" id='folder_options_single_id' value="single-folder" <?php if (isset($previous_settings['folder_options-single']) && !empty($previous_settings['folder_options-single'])) echo "checked"; ?> />
                <label for="folder_options_single_id">Enable a common folder for everyone</label>

                <div id='public-folder-wrapper' style="display: <?php if (isset($previous_settings['folder_options-single']) && !empty($previous_settings['folder_options-single'])) echo 'block;';
                                                                else echo 'none'; ?>">

                    <h4>Public Folder Settings</h4>
                    <label for='public-folder-path-id'>Path &nbsp;</label>
                    <input type='text' name='public-folder-path' id='public-folder-path-id' placeholder='<?php echo $root_folder_path . 'public'; ?>' value='<?php if (isset($previous_settings['public-folder-path']) && !empty($previous_settings['public-folder-path'])) echo $previous_settings['public-folder-path'];
                                                                                                                                                                else echo $root_folder_path . 'public'; ?>'>
                    <small>default is <b><?php echo $root_folder_path . 'public'; ?></b></small>

                    <br>
                    <label for='public-folder-url-id'>URL &nbsp;&nbsp;</label>
                    <input type='text' name='public-folder-url' id='public-folder-url-id' placeholder='<?php echo $root_folder_url . 'public'; ?>' value='<?php if (isset($previous_settings['public-folder-url']) && !empty($previous_settings['public-folder-url'])) echo $previous_settings['public-folder-url'];
                                                                                                                                                            else echo $root_folder_url . 'public'; ?>'>
                    <small>default is <b><?php echo $root_folder_path . 'public'; ?></b></small>

                </div>

                <br>
                <br>
                <input type='checkbox' name="folder_options-separate" id='folder_options_separate_id' value="separate-folder" <?php if (isset($previous_settings['folder_options-separate']) && !empty($previous_settings['folder_options-separate'])) echo "checked"; ?> />
                <label for="folder_options_separate_id">Enable separate folders for each user</label>

                <br>
                <br>
                <input type='checkbox' name="folder_options-userrole" id='folder_options_userrole_id' value="userrole-folder" <?php if (isset($previous_settings['folder_options-userrole']) && !empty($previous_settings['folder_options-userrole'])) echo "checked"; ?> />
                <label for="folder_options_userrole_id">Enable folders for each user role</label>

                <h3>Roles Permission</h3>

                <table>

                    <tr>
                        <th>Role </th>


                        <?php foreach ($operations as $operation) : ?>

                            <th><?php echo $FMP->__p($operation); ?></th>

                        <?php endforeach; ?>

                        <th>Path<small>(relative to root folder or absolute path)</small></th>

                    </tr>
                    <?php foreach ($roles as $role) : ?>
                        <tr>

                            <td><?php echo $FMP->__p($role); ?></td>

                            <?php foreach ($operations as $operation) : ?>
                                <td>
                                    <?php if ($operation == 'ban') { ?>
                                        <input type='checkbox' name='<?php echo $role; ?>[]' onClick='FMP.banned_notification("<?php echo $role . '_' . $operation; ?>", "<?php echo $role; ?>");' id='<?php echo $role . '_' . $operation; ?>' class='<?php echo $operation; ?>' value='<?php echo $operation; ?>' <?php if (isset($previous_settings[$role]) && in_array($operation, $previous_settings[$role])) echo "checked"; ?> />
                                    <?php } else { ?>
                                        <input type='checkbox' name='<?php echo $role; ?>[]' id='<?php echo $role . '_' . $operation; ?>' class='<?php echo $operation; ?>' value='<?php echo $operation; ?>' <?php if (isset($previous_settings[$role]) && in_array($operation, $previous_settings[$role])) echo "checked"; ?> />
                                    <?php }; ?>
                                </td>
                            <?php endforeach; ?>

                            <td>

                                <input type='text' name='<?php echo $role; ?>[path]' value='<?php if (isset($previous_settings[$role]['path']) && !empty($previous_settings[$role]['path'])) echo $previous_settings[$role]['path']; ?>' />

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </table>

                <h3>Guest User Settings</h3>
                <table>
                    <tr>
                        <th>Guests can Download file</th>
                        <td><input type='checkbox' name='fmp_guest[]' value='download' <?php if (!empty($previous_settings['fmp_guest']) && in_array('download', $previous_settings['fmp_guest'])) echo 'checked' ?>> <i class="fa fa-question-circle tippy" aria-hidden="true" title='Hello Help'></i> </td>

                        <th>Guests File Path</th>
                        <td><input type='text' name='fmp_guest[path]' value='<?php if (isset($previous_settings['fmp_guest']['path']) && !empty($previous_settings['fmp_guest']['path'])) echo $previous_settings['fmp_guest']['path']; ?>'></td>
                    </tr>
                </table>

                <!--
                User permission table
        -->
                <h3>User Permission</h3>
                <table>

                    <tr>
                        <th>User login</th>


                        <?php foreach ($operations as $operation) : ?>

                            <th><?php echo $FMP->__p($operation); ?></th>

                        <?php endforeach; ?>

                        <th>Path <small>(relative to root folder or absolute path)</small></th>

                    </tr>
                    <?php foreach ($users as $user) : ?>
                        <tr>

                            <td><?php echo $FMP->__p($user['user_login']); ?></td>

                            <?php foreach ($operations as $operation) : ?>
                                <td>
                                    <input type='checkbox' name='<?php echo $FMP->zip($user['user_login']); ?>[]' value="<?php echo $operation; ?>" <?php if (isset($previous_settings[$FMP->zip($user['user_login'])]) && in_array($operation, $previous_settings[$FMP->zip($user['user_login'])])) echo "checked"; ?> />
                                </td>
                            <?php endforeach; ?>

                            <td>

                                <input type='text' name='<?php echo $FMP->zip($user['user_login']); ?>[path]' value='<?php if (isset($previous_settings[$FMP->zip($user['user_login'])]['path']) && !empty($previous_settings[$FMP->zip($user['user_login'])]['path'])) echo $previous_settings[$FMP->zip($user['user_login'])]['path']; ?>' />

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </table>

                <input class='fmp-save' type='submit' value='Save' />

            </form>
        </div>
    </div>
    <?php require_once 'sidebar.php'; ?>
</div>

<?php require_once  'footer.php'; ?>

<style>
    .bootstart-admin-content {
        float: unset !important;
    }
</style>