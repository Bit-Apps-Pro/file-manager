<?php

use BitApps\FM\Plugin;

use function BitApps\FM\Functions\validatePath;

if (!\defined('ABSPATH')) {
    exit();
}

// Settings processing
$preferenceProvider = Plugin::instance()->preferences();

if (isset($_POST) && !empty($_POST)) {
    if (
        !wp_verify_nonce(
            sanitize_text_field($_POST['file-manager-settings-security-token']),
            'file-manager-settings-security-token'
        )
         || !current_user_can('install_plugins')
         ) {
        wp_die();
    }

    $rootPath = isset($_POST['root_folder_path'])
    ? sanitize_text_field($_POST['root_folder_path']) : '';
    $rootPath = validatePath($rootPath);


    $_POST['show_url_path'] = sanitize_text_field($_POST['show_url_path']);
    if (isset($_POST['show_url_path']) && ($_POST['show_url_path'] == 'show' || $_POST['show_url_path'] == 'hide')) {
        $preferenceProvider->setUrlPathView($_POST['show_url_path']);
    }

    $preferenceProvider->setRootPath(
        $rootPath
    );

    $preferenceProvider->setRootUrl(
        isset($_POST['root_folder_url'])
        ? esc_url_raw($_POST['root_folder_url']) : ''
    );

    $preferenceProvider->setLang(sanitize_text_field($_POST['language']));
    $preferenceProvider->setTheme(sanitize_text_field($_POST['theme']));
    $preferenceProvider->setWidth(isset($_POST['width']) ? filter_var($_POST['width'], FILTER_VALIDATE_INT) : 'auto');
    $preferenceProvider->setHeight(isset($_POST['height']) ? filter_var($_POST['height'], FILTER_VALIDATE_INT) : '500');
    $preferenceProvider->setVisibilityOfHiddenFile(
        !empty($_POST['fm-show-hidden-files'])
         ? sanitize_text_field($_POST['fm-show-hidden-files']) : ''
    );
    $preferenceProvider->setPermissionForHiddenFolderCreation(
        !empty($_POST['fm-create-hidden-files-folders'])
         ? sanitize_text_field($_POST['fm-create-hidden-files-folders']) : ''
    );
    $preferenceProvider->setPermissionForTrashCreation(
        !empty($_POST['fm-create-trash-files-folders'])
        ? sanitize_text_field($_POST['fm-create-trash-files-folders']) : ''
    );
    $preferenceProvider->setRootVolumeName(
        !empty($_POST['fm_root_folder_name'])
         ? sanitize_text_field($_POST['fm_root_folder_name']) : 'WP Root'
    );
    $preferenceProvider->setViewType(
        !empty($_POST['fm_default_view_type'])
         ? sanitize_text_field($_POST['fm_default_view_type']) : 'icons'
    );

    $preferenceProvider->setRememberLastDir(
        !empty($_POST['fm-remember-last-dir'])
         ? sanitize_text_field($_POST['fm-remember-last-dir']) : ''
    );

    $preferenceProvider->setClearHistoryOnReload(
        isset($_POST['fm-clear-history-on-reload'])
        && !empty($_POST['fm-clear-history-on-reload'])
            ? sanitize_text_field($_POST['fm-clear-history-on-reload']) : ''
    );
    $preferenceProvider->setUiOptions(
        isset($_POST['fm_display_ui_options']) && !empty($_POST['fm_display_ui_options'])
         ? filter_var_array($_POST['fm_display_ui_options']) : ['toolbar', 'places', 'tree', 'path', 'stat']
    );

    $preferenceProvider->saveOptions();
}

$themes = [
    'default'          => 'Default',
    'material-default' => 'Material Default',
    'material-gray'    => 'Material Gray',
    'material-light'   => 'Material Light',
    'bootstrap'        => 'Bootstrap',
];
$selectedTheme = $preferenceProvider->getTheme();

?>
<?php require_once 'header.php'; ?>
<div class='fm-container'>

    <div class='col-main'>

        <div class='gb-fm-row fmp-settings'>

            <h2><?php _e('Settings', 'file-manager'); ?></h2>

            <form action='' method='post' class='fmp-settings-form'>
                <input type='hidden' name='file-manager-settings-security-token' value='<?php echo esc_attr(wp_create_nonce('file-manager-settings-security-token')); ?>'>
                <table>
                    <tr>
                        <td rowspan="2">
                            <h4><?php esc_html_e('URL and Path', 'file-manager'); ?></h4>
                        </td>
                        <td>
                            <label for='show_url_path_id'> <?php esc_html_e('Show', 'file-manager'); ?> </label>
                            <input type='radio' name='show_url_path' id='show_url_path_id' value='show' <?php if ($preferenceProvider->getUrlPathView() == 'show') {
                                echo 'checked';
                            }
?> />

                            <label for='hide_url_path_id'> Hide </label>
                            <input type='radio' name='show_url_path' id='hide_url_path_id' value='hide' <?php if ($preferenceProvider->getUrlPathView() == 'hide') {
                                echo 'checked';
                            }
?> />
                        </td>
                    </tr>
                    <tr>

                        <td>
                            <label for='root_folder_path_id'> <?php esc_html_e('Root Folder Path', 'file-manager'); ?> </label>
                            <input type='text' name='root_folder_path' onkeyup="pathVlidation()" id='root_folder_path_id' value='<?php echo esc_attr($preferenceProvider->getRootPath());?>' /></br>
                            <span id="fm_path_err"></span>
                            <script>
                                function pathVlidation() {
                                    let path = document.getElementById('root_folder_path_id').value;
                                    let isWinPath = path.match(/^[a-zA-Z]:\\([a-zA-Z0-9.\-*.+]+([ ][a-zA-Z0-9.\-*.+]+)*\\)*([a-zA-Z0-9.\-*.+]+([ ][a-zA-Z0-9.\-*.+]+)*)*$/gi);
                                    let isNixPath = path.match(/^\/([A-z0-9-_+]+\/?)*$/gm);

                                    let span = document.getElementById('fm_path_err');
                                    if (!isWinPath && !isNixPath) {
                                        span.innerHTML = "Path is wrong! Please enter a valid Path.";
                                        span.style.color = 'red';
                                        span.style.visibility = 'visible'
                                    } else if (isNixPath.length || isWinPath.length) {
                                        span.style.visibility = 'hidden'
                                    }
                                }
                            </script>
                            <br>
                            <small><?php esc_html_e('Default Path:', 'file-manager'); ?> <b><?php echo esc_html(ABSPATH); ?></b></small>
                            <br><br>
                            <label for='root_folder_url_id'> <?php esc_html_e('Root Folder URL', 'file-manager'); ?> </label>
                            &nbsp;
                            <input type='text' name='root_folder_url' onkeyup="validURL()" id='root_folder_url_id' 
                            value='<?php echo esc_attr($preferenceProvider->getRootUrl());?>' 
                            />
                            <br />
                            <span id="url_error"></span>
                            <br>
                            <small><?php esc_html_e('Default URL:', 'file-manager'); ?> <b><?php echo esc_html(site_url()); ?></b></small>
                            <script>
                                function validURL() {
                                    string = document.getElementById('root_folder_url_id').value;
                                    if (string) {
                                        var res = string.match(/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/g);
                                        var span = document.getElementById('url_error')

                                        if (res == null) {
                                            span.innerHTML = "URL is wrong! Please enter a valid URL.";
                                            span.style.color = 'red';
                                            span.style.visibility = 'visible'
                                        } else if (res.length) {
                                            span.style.visibility = 'hidden'
                                        }
                                    }
                                }
                            </script>
                        </td>
                    </tr>
                    <!-- <tr>
                            <td></td>
                            <td><small><?php esc_html_e('Default Path:', 'file-manager'); ?> <b><?php echo esc_html(ABSPATH); ?></b></small></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><small><?php esc_html_e('Default URL:', 'file-manager'); ?> <b><?php echo esc_html(site_url()); ?></b></small></td>
                        </tr> -->
                    <tr>
                        <td></td>
                        <td style="text-align: center;"><small><?php esc_html_e("Root folder path and URL must be correct, otherwise it won't work.", 'file-manager'); ?></small></td>
                    </tr>
                    <tr>
                        <td>
                            <h4><?php esc_html_e('Select Language', 'file-manager'); ?></h4>
                        </td>
                        <td>
                          <select name='language'>
                                <?php
                                $selectedCode = $preferenceProvider->getLangCode();
foreach ($preferenceProvider->availableLanguages() as $code => $name) {
    ?>
                                    <option <?php selected($code, $selectedCode); ?> value='<?php echo esc_attr($code); ?>'><?php echo esc_html($name); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4><?php esc_html_e('Select Theme', 'file-manager'); ?></h4>
                        </td>
                        <td>
                            <select name='theme'>
                                <?php foreach ($themes as $themeID => $theme) { ?>
                                    <option 
                                    <?php selected($themeID, $selectedTheme); ?> value='<?php echo esc_attr($themeID); ?>'><?php echo esc_html($theme); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4><?php esc_html_e('Size', 'file-manager'); ?></h4>
                        </td>
                        <td>
                            <label for='fm-width-id'><?php esc_html_e('Width', 'file-manager'); ?></label>
                            &nbsp;&nbsp;&nbsp;
                            <input
                                id='fm-width-id'
                                type='text'
                                name='width'
                                value='<?php echo esc_attr($preferenceProvider->getWidth());?>'
                            >
                            <br />
                            <label for='fm-height-id'><?php esc_html_e('Height', 'file-manager'); ?></label>
                            &nbsp;&nbsp;
                            <input
                            id='fm-height-id'
                            type='text'
                            name='height'
                            value='<?php echo esc_attr($preferenceProvider->getHeight())?>'>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4><label for='fm-width-id'><?php esc_html_e('Show Hidden Files', 'file-manager'); ?></label></h4>
                        </td>
                        <td>
                            <input 
                            id='fm-media-sync-id'
                            type='checkbox'
                            name='fm-show-hidden-files'
                            <?php
                           if ($preferenceProvider->getVisibilityOfHiddenFile()) {
                               echo 'checked';
                           }?>
                             value="fm-show-hidden-files"
                            >
                            <small><?php esc_html_e('When checked hidden files and folders will be shown to the users.', 'file-manager'); ?></small>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4><label for='fm-width-id'><?php esc_html_e('Allow Create/Upload Hidden Files/Folders', 'file-manager'); ?></label></h4>
                        </td>
                        <td>
                            <input
                             id='fm-hidden-file-id'
                            type='checkbox'
                             name='fm-create-hidden-files-folders'
                              <?php if ($preferenceProvider->isHiddenFolderAllowed()) {
                                  echo 'checked';
                              }
?>
                            value="fm-create-hidden-files-folders"
                            >
                            <small><?php esc_html_e('When checked hidden files and folders will be create by the users.', 'file-manager'); ?></small>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4><label for='fm-width-id'><?php esc_html_e('Allow Trash', 'file-manager'); ?></label></h4>
                        </td>
                        <td>
                            <input
                            id='fm-trash-id'
                            type='checkbox'
                            name='fm-create-trash-files-folders'
                            <?php
                             if ($preferenceProvider->isTrashAllowed()) {
                                 echo 'checked';
                             }
?> value="fm-create-trash-files-folders">
                            <small><?php esc_html_e('When checked deleted files and folder will save here.', 'file-manager'); ?></small>
                            <br />
                            <small><?php esc_html_e('Default Path:', 'file-manager'); ?> <b><?php echo esc_html(FM_TRASH_DIR_PATH); ?></b></small>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4><?php esc_html_e('Root Folder Name', 'file-manager'); ?></h4>
                        </td>
                        <td>
                            <label for='fm-root-folder-name-id'></label>
                            <input
                                id='fm-root-folder-name-id'
                                type='text'
                                name='fm_root_folder_name'
                                value='<?php echo esc_attr($preferenceProvider->getRootVolumeName());?>'
                            >
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4><?php esc_html_e('Default View Type', 'file-manager'); ?></h4>
                        </td>
                        <td>
                            <?php
                            $defaultViewType = $preferenceProvider->getViewType();
?>
                            <label for='fm-root-folder-name-id'></label>
                            <select id="fm_default_view_type" name="fm_default_view_type">
                                <option disabled>Select Defualt View Type</option>
                                <option <?php selected('icons', $defaultViewType); ?> value='icons'>Icons</option>
                                <option <?php selected('list', $defaultViewType); ?> value='list'>List</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4><label for='fm-remember-last-dir'><?php esc_html_e('Remember Last Directory', 'file-manager'); ?></label></h4>
                        </td>
                        <td>
                            <input
                                id='fm-remember-last-dir'
                                type='checkbox'
                                name='fm-remember-last-dir'
                                 <?php
                                  if ($preferenceProvider->getRememberLastDir()) {
                                      echo 'checked';
                                  }
?> 
                                value="fm-remember-last-dir"
                            >
                            <small><?php esc_html_e('Remeber last opened dir to open it after reload.', 'file-manager'); ?></small>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4><label for='fm-clear-history-on-reload'><?php esc_html_e('Clear History On Reload', 'file-manager'); ?></label></h4>
                        </td>
                        <td>
                            <input
                                id='fm-clear-history-on-reload'
                                type='checkbox'
                                name='fm-clear-history-on-reload'
                            <?php
                            if ($preferenceProvider->getClearHistoryOnReload()) {
                                echo 'checked';
                            }
?>
                            value="fm-clear-history-on-reload">
                            <small><?php esc_html_e('Clear historys(elFinder) on reload(not browser).', 'file-manager'); ?></small>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4><?php esc_html_e('Default View Type', 'file-manager'); ?></h4>
                        </td>
                        <td>
                            <label for='fm-root-folder-name-id'></label>
                            <select id="fm_display_ui_options" name="fm_display_ui_options[]" multiple>
                                <option disabled>Select Default View Type</option>
<?php
$uioptions = ['toolbar', 'places', 'tree', 'path', 'stat'];
foreach ($uioptions as $place) { ?>
                                    <option <?php if (\in_array($place, $preferenceProvider->getUiOptions())) {
                                        echo 'selected';
                                    } ?> value='<?php echo esc_attr($place); ?>'><?php echo esc_html($place); ?></option>

                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type='submit' value='<?php esc_html_e('Save', 'file-manager'); ?>' />
                        </td>
                    </tr>
                </table>

            </form>

        </div>


    </div>

    <?php require_once 'sidebar.php'; ?>

</div>

<?php require_once 'footer.php'; ?>
<!--

-->