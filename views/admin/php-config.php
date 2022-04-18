<?php
/**
 *
 * @file php-config.php Utility information about the plugin
 *
 * */

// Security Check
if (!defined('ABSPATH')) {
    die();
}

global $FileManager;

wp_enqueue_style('fmp-admin-style');
?>
<div class='fm-container'>
<div class='col-main'>
    <div class='gb-fm-row fmp-settings'>
        <form action='' method='post' class='fmp-settings-form'>
					<input type='hidden' name='file-manager-settings-security-token' value='<?php echo wp_create_nonce('file-manager-settings-security-token'); ?>'>
					<table>
                        <!-- <tr>
							<td><h4><?php _e("Root Folder Name", 'file-manager');?></h4></td>
							<td>
								<label for='fm-root-folder-name-id'></label>
								<input id='fm-root-folder-name-id' type='text' name='fm_root_folder_name' value=''>
							</td>
						</tr> -->
        <?php
        
            $options = ini_get_all();
            // print_r($options);
            foreach($options as $key => $value){ 
                if( '' === $value['local_value'] || '1' === $value['local_value'] || '0' === $value['local_value'] ){?>
                        <tr>
							<td><h4><?php _e($key, 'file-manager');?></h4></td>
							<td>
								<label for='<?php echo $key?>'></label>
								<input id='<?php echo $key?>' type='checkbox' name='<?php echo $key?>'  <?php if($value['local_value']) echo "checked"?>   value='<?php echo $value['local_value']?>'>
							</td>
						</tr>
                <?php }else{ ?>
                    <tr>
                        <td><h4><?php _e($key, 'file-manager');?></h4></td>
                        <td>
                            <label for='<?php echo $key?>'></label>
                            <input id='<?php echo $key?>' type='text' name='<?php echo $key?>' value='<?php echo $value['local_value']?>'>
                        </td>
                    </tr>
                <?php
                    }
            } ?>
                </table>
            </form>
        </div>
    </div>
</div>
