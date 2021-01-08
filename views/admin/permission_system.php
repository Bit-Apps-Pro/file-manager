<?php
/**
 *
 * @file index.php The manin admin view file that will show the actual file manager
 *
 * */

// Security check
if( !defined('ABSPATH') ) die();
global $FileManager;
?>
<?php
// Loading admin assets
global $FileManager;
$FileManager->admin_assets();
?>

<?php require_once( 'header.php' ); ?>

<div class='fm-container sudo-permission-system'>

	<div class='col-main'>
		<div class='fmp-demo-notice'>
			<?php _e("This is a demo of", 'file-manager'); ?><a href='http://giribaz.com/wordpress-file-manager-plugin/'><?php _e("File Manager Permission System(Pro)", 'file-manager'); ?></a> <?php _e("Extension.", 'file-manager'); ?>
			<button onClick="window.location = 'http://giribaz.com/wordpress-file-manager-plugin/'"><?php _e("Get It Now!", 'file-manager'); ?></button>
		</div>
		<div class='gb-fm-row'>

		<img src='<?php echo plugin_dir_url(__FILE__) . '../../img/File Manager Permission System(Pro).png'?>'>

		</div>

		<!--<div class='gb-fm-row fm-data'>
			<?php require_once('utility.php'); ?>
		</div>-->

	</div>

	<?php //require_once('sidebar.php'); ?>

</div>

<?php require_once('footer.php'); ?>
