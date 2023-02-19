<?php

// Security Check
defined('ABSPATH') or die();

global $wp_roles, $wpdb, $FileManager, $FMP;

// Processing Post data
if (!empty($_POST)) {
    // Checks if the current user have enough authorization to operate.
    if (!wp_verify_nonce($_POST['bfm_permissions_nonce'], 'bfm_permissions_nonce') || !current_user_can('manage_options')) wp_die();
    check_ajax_referer('bfm_permissions_nonce', 'bfm_permissions_nonce');
    update_option('file_manager_permissions', $_POST, 'yes');
}
$previous_settings = get_option('file_manager_permissions', []);

$permissionSettings = new BFMFileManagerPermissionSettings();

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
            </table>
        </div>

        <div class='gb-fm-row'>
            <form method="post" action="">
                <input type='hidden' name='bfm_permissions_nonce' value='<?php echo wp_create_nonce("bfm_permissions_nonce"); ?>'>

                <label for='do_not_use_for_admin-id'>Do not use this settings for administrator </label>
                <input type='checkbox' name='do_not_use_for_admin' id='do_not_use_for_admin-id' value='do_not_use_for_admin' <?php if ($permissionSettings->isEnabledForAdmin()) echo "checked"; ?>>

                <h3>Allowed MIME types and size</h3>
                <?php foreach ($file_types as $file_type) : ?>
                    <input type='checkbox' name="file_type[]" value="<?php echo $file_type; ?>" id='<?php echo $file_type . "_id"; ?>' <?php echo in_array($file_type, $permissionSettings->getEnabledFileType()) ? "checked" : ""; ?> />
                    <label for="<?php echo $file_type . "_id"; ?>"><?php echo $FMP->__p($file_type); ?></label>
                <?php endforeach; ?>
                <small><a href="http://www.iana.org/assignments/media-types/media-types.xhtml">What is MIME types?</a></small>
                </br>
                </br>
                <label for="file_size_id">Maximum File Size</label>
                <input type='number' name="file_size" id='file_size_id' value="<?php echo $permissionSettings->getMaximumUploadSize(); ?>" />
                <small>MB(mega byte).</small><small> 0 for unlimited size</small>

                </br>
                </br>
                <h3>Root Folder</h3>
                <label for="root_folder_id">Root Folder Path</label>
                <input style="width:500px;" type='text' placeholder='<?php echo $permissionSettings->getDefaultPublicRootPath(); ?>' name="root_folder" id='root_folder_id' value="<?php echo $permissionSettings->getPublicRootPath(); ?>" />
                <small>default is <b><?php echo $permissionSettings->getDefaultPublicRootPath(); ?></b></small>

                </br>
                </br>
                <label for="root_folder_url_id">Root Folder URL</label>
                <input style="width:500px;" type='text' placeholder='<?php echo $permissionSettings->getDefaultPublicRootURL(); ?>' name="root_folder_url" id='root_folder_url_id' value="<?php echo $permissionSettings->getPublicRootURL(); ?>" />
                <small>default is <b><?php echo $permissionSettings->getDefaultPublicRootURL(); ?></b></small>

                <br />
                <br />

                <h3>Folder Options</h3>
                <input type='radio' name="folder_options" id='folder_options_single_id' value="common" <?php echo $permissionSettings->getFolderOption() === "common" ? "checked" : ""; ?> />
                <label for="folder_options_single_id">Enable a common folder for everyone</label>
                <br>
                <br>
                <input type='radio' name="folder_options" id='folder_options_separate_id' value="user" <?php echo $permissionSettings->getFolderOption() === "user" ? "checked" : ""; ?> />
                <label for="folder_options_separate_id">Enable separate folders for each user</label>

                <br>
                <br>
                <input type='radio' name="folder_options" id='folder_options_userrole_id' value="role" <?php echo $permissionSettings->getFolderOption() === "role" ? "checked" : ""; ?> />
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
                                        <input type='checkbox' name='<?php echo $role; ?>[]' onClick='FMP.banned_notification("<?php echo $role . '_' . $operation; ?>", "<?php echo $role; ?>");' id='<?php echo $role . '_' . $operation; ?>' class='<?php echo $operation; ?>' value='<?php echo $operation; ?>' <?php if (isset($previous_settings[$role]) && is_array($previous_settings[$role]) && in_array($operation, $previous_settings[$role])) echo "checked"; ?> />
                                    <?php } else { ?>
                                        <input type='checkbox' name='<?php echo $role; ?>[]' id='<?php echo $role . '_' . $operation; ?>' class='<?php echo $operation; ?>' value='<?php echo $operation; ?>' <?php if (isset($previous_settings[$role]) && is_array($previous_settings[$role]) && in_array($operation, $previous_settings[$role])) echo "checked"; ?> />
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
                                    <input type='checkbox' name='<?php echo $FMP->zip($user['user_login']); ?>[]' value="<?php echo $operation; ?>" <?php if (isset($previous_settings[$FMP->zip($user['user_login'])]) && is_array($previous_settings[$FMP->zip($user['user_login'])]) && in_array($operation, $previous_settings[$FMP->zip($user['user_login'])])) echo "checked"; ?> />
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