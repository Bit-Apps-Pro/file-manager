<?php

// Security Check

use BitApps\FM\Config;
use BitApps\FM\Core\Utils\Capabilities;
use BitApps\FM\Plugin;

\defined('ABSPATH') or exit();

// Processing Post data
if (!empty($_POST)) {
    // Checks if the current user have enough authorization to operate.
    if (
        !wp_verify_nonce($_POST['bfm_permissions_nonce'], 'bfm_permissions_nonce')
        || !Capabilities::filter(Config::VAR_PREFIX . 'user_can_set_permission')
    ) {
        wp_die();
    }

    // var_dump($_POST); die;
    check_ajax_referer('bfm_permissions_nonce', 'bfm_permissions_nonce');
    Config::updateOption('permissions', $_POST, 'yes');
}

$permissionSettings = Plugin::instance()->permissions();

$operations = $permissionSettings->allCommands();

// File Types
$fileTypes = ['text', 'image', 'application', 'video', 'audio'];
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
                    <td class='file-manager-shortcode-table-shortcode'>[file-manager]</td>
                </tr>
                <!-- <tr>
                    <td>Copy and paste this php function anywhere in you template to show file manager.</td>
                    <td class='file-manager-shortcode-table-shortcode'>file_manager_permission_system_frontend()</td>
                </tr> -->
            </table>
        </div>

        <div class='gb-fm-row'>
            <form method="post" action="">
                <input type='hidden' name='bfm_permissions_nonce' value='<?php echo wp_create_nonce('bfm_permissions_nonce'); ?>'>

                <label for='do_not_use_for_admin-id'>Do not use this settings for administrator </label>
                <input type='checkbox' name='do_not_use_for_admin' id='do_not_use_for_admin-id' value='do_not_use_for_admin' <?php if ($permissionSettings->isDisabledForAdmin()) {
                    echo 'checked';
                } ?>>

                <h3>Allowed MIME types and size</h3>
                <?php foreach ($fileTypes as $fileType) { ?>
                    <input type='checkbox' name="fileType[]" value="<?php echo $fileType; ?>" id='<?php echo $fileType . '_id'; ?>' <?php echo \in_array($fileType, $permissionSettings->getEnabledFileType()) ? 'checked' : ''; ?> />
                    <label for="<?php echo $fileType . '_id'; ?>"><?php echo $fileType; ?></label>
                <?php } ?>
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
                <input type='radio' name="folder_options" id='folder_options_single_id' value="common" <?php echo $permissionSettings->getFolderOption() === 'common' ? 'checked' : ''; ?> />
                <label for="folder_options_single_id">Enable a common folder for everyone</label>
                <br>
                <br>
                <input type='radio' name="folder_options" id='folder_options_separate_id' value="user" <?php echo $permissionSettings->getFolderOption() === 'user' ? 'checked' : ''; ?> />
                <label for="folder_options_separate_id">Enable separate folders for each user</label>

                <br>
                <br>
                <input type='radio' name="folder_options" id='folder_options_userrole_id' value="role" <?php echo $permissionSettings->getFolderOption() === 'role' ? 'checked' : ''; ?> />
                <label for="folder_options_userrole_id">Enable folders for each user role</label>

                <h3>Roles Permission</h3>

                <table>

                    <tr>
                        <th>Role </th>


                        <?php foreach ($operations as $operation) { ?>

                            <th><?php echo $operation; ?></th>

                        <?php } ?>

                        <th>Path<small>(relative to root folder or absolute path)</small></th>

                    </tr>
                    <?php foreach ($permissionSettings->allRoles() as $role) { ?>
                        <tr>

                            <td><?php echo $role; ?></td>

                            <?php
                            $settingsForRole = $permissionSettings->getByRole($role);
                        foreach ($operations as $operation) {
                            ?>
                                <td>
                                    <input type='checkbox' name='by_role[<?php echo $role; ?>][commands][]' id='<?php echo $role . '_' . $operation; ?>' class='<?php echo $operation; ?>' value='<?php echo $operation; ?>' <?php echo \in_array($operation, $settingsForRole['commands']) ? 'checked' : ''; ?> />
                            <?php } ?>
                            <td>
                                <input type='text' name='by_role[<?php echo $role; ?>][path]' value='<?php echo $settingsForRole['path']; ?>' />

                            </td>

                        </tr>

                    <?php } ?>

                </table>

                <h3>Guest User Settings</h3>
                <table>
                    <tr>
                        <th>Guests can Download file</th>
                        <td><input type='checkbox' name='guest[commands][]' value='download' <?php echo \in_array('download', $permissionSettings->getGuestPermissions()['commands']) ? 'checked' : '' ?>> <i class="fa fa-question-circle tippy" aria-hidden="true" title='Hello Help'></i> </td>

                        <th>Guests File Path</th>
                        <td><input type='text' name='guest[path]' value='<?php echo $permissionSettings->getGuestPermissions()['path']; ?>'></td>
                    </tr>
                </table>

                <!--
                User permission table
        -->
                <h3>User Permission</h3>
                <table>

                    <tr>
                        <th>User login</th>
                        <th>&nbsp;&nbsp;</th>


                        <?php foreach ($operations as $operation) { ?>

                            <th><?php echo $operation; ?></th>

                        <?php } ?>

                        <th>Path <small>(relative to root folder or absolute path)</small></th>

                    </tr>
                    <?php foreach ($permissionSettings->allUsers() as $user) {
                        $userID          = $user->id;
                        $settingsForRole = $permissionSettings->getByUser($userID);
                        ?>
                        <tr>

                            <td><?php echo $user->display_name; ?></td>
                            <td>&nbsp;&nbsp;</td>

                            <?php foreach ($operations as $operation) { ?>
                                <td>
                                    <input type='checkbox' name='by_user[<?php echo $userID; ?>][commands][]' value="<?php echo $operation; ?>" <?php echo \in_array($operation, $settingsForRole['commands']) ? 'checked' : ''; ?> />
                                </td>
                            <?php } ?>
                            <td>

                                <input type='text' name='by_user[<?php echo $userID; ?>][path]' value='<?php echo $settingsForRole['path']; ?>' />

                            </td>

                        </tr>

                    <?php } ?>

                </table>

                <input class='fmp-save' type='submit' value='Save' />

            </form>
        </div>
    </div>
    <?php require_once 'sidebar.php'; ?>
</div>

<?php require_once 'footer.php'; ?>

<style>
    .bootstart-admin-content {
        float: unset !important;
    }
</style>