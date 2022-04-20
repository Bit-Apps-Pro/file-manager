<?php
if (!defined('ABSPATH')) {
    die();
}
global $FileManager;
// echo '<pre>'; print_r( _get_cron_array() ); echo '</pre>';

$admin_page_url = admin_url() . "admin.php?page={$FileManager->prefix}";

// Enqueing admin assets
$FileManager->admin_assets();
$logs = get_option('lfm_log', array());
// Language
include 'language-code.php';
global $fm_languages;
?>
<?php require_once 'header.php';?>
<div class='fm-container'>
	<div class='col-main' >
		<div class='gb-fm-row fmp-settings'>
        <h2><?php _e("File Manger Log", 'file-manager');?></h2>

                <?php echo '<link rel="stylesheet" href="'.FILE_MANAGER_URL.'lib/datatable/jquery.dataTables.min.css" media="all">';?>
                <table id="lfm_log_data_table" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Sr</th>
                                <th>Date</th>
                                <th>Command</th>
                                <th>Key</th>
                                <th>Error</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($logs as $key => $log) {?>
                    <tr>
                        <td><?php echo $key+1; ?></td>
                        <td><?php echo $log['date']; ?></td>
                        <td><?php echo $log['cmd']; ?></td>
                        <td><?php echo (isset($log['key']) && !empty($log['key'])) ? $log['key'] : ""; ?></td>
                        <td><?php print_r((isset($log['err']) && !empty($log['err'])) ? $log['err'] : "");?></td>
                    </tr>
                <?php }?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Sr</th>
                                <th>Date</th>
                                <th>Command</th>
                                <th>Key</th>
                                <th>Error</th>
                            </tr>
                        </tfoot>
                    <table>
                <?php
                echo '<script src="'.FILE_MANAGER_URL.'lib/datatable/jquery.dataTables.min.js" ></script>';
                ?>
                <script>
                    jQuery(document).ready(function() {
                        var table = jQuery('#lfm_log_data_table').DataTable();

                        jQuery('#lfm_log_data_table tbody').on('click', 'tr', function () {
                            var data = table.row( this ).data();
                            // alert( 'You clicked on '+data[0]+'\'s row' );
                        } );
                    } );
                </script>

        </div>
    </div>
</div>