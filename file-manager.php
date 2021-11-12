<?php
/**
 *
 * Plugin Name: Library File Manager
 * Author: Aftabul Islam
 * Author URI: https://wpjos.com
 * Version: 5.2.2
 * Author Email: toaihimel@gmail.com
 * PHP version: 5.6
 * Text domain: file-manager
 * License: GPLv2
 * Description: File Manage let you manage your file the way you like. You can upload, delete, copy, move, rename, compress, extract files. You don't need to worry about ftp. It is realy simple and easy to use.
 *
 * */

/*
 * @since v5.2.0
 *
 * Constant Definition
 *
 * */

// Directory Seperator
if( !defined( 'DS' ) ) define("DS", DIRECTORY_SEPARATOR);

$upload_dir = wp_upload_dir();

defined( 'FM_UPLOAD_BASE_DIR' ) || define( 'FM_UPLOAD_BASE_DIR', $upload_dir['basedir'] . DS . 'file-manager' . DS );

// Including elFinder class
require_once('elFinder' . DS . 'elFinder.php');

// Including bootstarter
require_once('BootStart' . DS . 'BootStart.php');

// Including other necessary files
require_once('inc/__init__.php');

// After library Loaded
do_action('file_manager_init');

class FM extends FM_BootStart {

	/**
	 *
	 * @var $version Wordpress library file manager plugin version
	 *
	 * */
	public $version;

	public $version_no;

	/**
	 *
	 * @var $site Site url
	 *
	 * */
	public $site;

	/**
	 *
	 * @var $giribaz_landing_page Landing page for giribaz
	 *
	 * */
	public $giribaz_landing_page;

	/**
	 *
	 * @var $support_page Support ticket page
	 *
	 * */
	public $support_page;

	/**
	 *
	 * @var $feedback_page Feedback page
	 *
	 * */
	public $feedback_page;

	/**
	 *
	 * @var $file_manager_view_path View path of library file manager
	 *
	 * */
	public $file_manager_view_path;

	public function __construct($name){

		$this->version = '5.2.2';
		$this->version_no = 522;
		$this->site = 'https://wpjos.com';
		$this->giribaz_landing_page = 'https://wpjos.com/library-file-manager-plugin';
		$this->support_page = 'https://wpjos.com/support/';
		$this->feedback_page = 'https://wordpress.org/support/plugin/file-manager/reviews/';
		$this->file_manager_view_path = plugin_dir_path(__FILE__);

		// Checking for migration
		new FMMigrate($this->version_no);

		// Adding Menu
		$this->menu_data = array(
			'type' => 'menu',
		);

		// Adding Ajax
		$this->add_ajax('connector'); // elFinder ajax call
		$this->add_ajax('fm_site_backup'); // Site backup function invoked

		parent::__construct($name);

		// Adding plugins page links
		add_filter('plugin_action_links', array(&$this, 'plugin_page_links'), 10, 2);

		// Admin Notices
		add_action('admin_notices', array(&$this, 'admin_notice'));
	}

	/**
	 *
	 * Library File Manager connector function
	 *
	 * */
	public function connector(){

		// Allowed mime types
		$mime = new FMMIME( plugin_dir_path(__FILE__) . 'elFinder/php/mime.types' );
		$wp_upload_dir = wp_upload_dir();
		
		$opts = array(
			'bind' => array(
				 'put.pre' => array(new FMPHPSyntaxChecker, 'checkSyntax'), // Syntax Checking.
				// 'archive.pre back.pre chmod.pre colwidth.pre copy.pre cut.pre duplicate.pre editor.pre put.pre extract.pre forward.pre fullscreen.pre getfile.pre help.pre home.pre info.pre mkdir.pre mkfile.pre netmount.pre netunmount.pre open.pre opendir.pre paste.pre places.pre quicklook.pre reload.pre rename.pre resize.pre restore.pre rm.pre search.pre sort.pre up.pre upload.pre view.pre zipdl.pre file.pre tree.pre parents.pre ls.pre tmb.pre size.pre dim.pre get.pre' => array(&$this, 'security_check'),
				// 'upload' => array(new FMMediaSync(), 'onFileUpload'),
				'*' => 'fm_logger',
			),
			'debug' => true,
			'roots' => array(
				array(
					'alias'         => 'WP Root',
					'driver'        => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
					'path'          => isset($this->options['file_manager_settings']['root_folder_path']) && !empty($this->options['file_manager_settings']['root_folder_path']) ? $this->options['file_manager_settings']['root_folder_path'] : ABSPATH,                     // path to files (REQUIRED)
					'URL'           => isset($this->options['file_manager_settings']['root_folder_url']) && !empty($this->options['file_manager_settings']['root_folder_url']) ? $this->options['file_manager_settings']['root_folder_url'] :site_url(),                  // URL to files (REQUIRED)
					'uploadDeny'    => array(),                // All Mimetypes not allowed to upload
					'uploadAllow'   => $mime->get_types(), // All MIME types is allowed
					'uploadOrder'   => array('allow', 'deny'),      // allowed Mimetype `image` and `text/plain` only
					'accessControl' => array(new FMAccessControl(), 'control'),
					'disabled'      => array(),    // List of disabled operations
				),
				array(
					'alias'        => 'Media',
					'driver'        => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
					'path'          => $wp_upload_dir['path'],                     // path to files (REQUIRED)
					'URL'           => $wp_upload_dir['url'],                  // URL to files (REQUIRED)
					'uploadDeny'    => array(),                // All Mimetypes not allowed to upload
					'uploadAllow'   => $mime->get_types(), // All MIME types is allowed
					'uploadOrder'   => array('allow', 'deny'),      // allowed Mimetype `image` and `text/plain` only
					'accessControl' => array(new FMAccessControl(), 'control'),
					'disabled'      => array(),    // List of disabled operations
				),
			)
		);

		/**
		 *
		 * @filter fm_options :: Options filter
		 * Implementation Example: add_filter('fm_options', array($this, 'fm_options_test'), 10, 1);
		 *
		 * */
		$opts = apply_filters('fm_options_filter', $opts);
		$elFinder = new FM_EL_Finder();
		$elFinder = $elFinder->connect($opts);
		$elFinder->run();

		wp_die();
	}

	public function security_check(){
		// Checks if the current user have enough authorization to operate.
		if( ! wp_verify_nonce( $_POST['file_manager_security_token'] ,'file-manager-security-token') || !current_user_can( 'manage_options' ) ) wp_die();
		check_ajax_referer('file-manager-security-token', 'file_manager_security_token');
	}

	/**
	 *
	 * Adds plugin page links,
	 *
	 * */
	public function plugin_page_links($links, $file){

		static $this_plugin;

		if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

		if ($file == $this_plugin){
			array_unshift( $links, '<a target=\'blank\' href="https://wpjos.com/support/">'. "Support" .'</a>');

			array_unshift( $links, '<a href="admin.php?page=file-manager-settings">'. "Library File Manager" .'</a>');

			if( !defined('FILE_MANAGER_PREMIUM') && !defined('FILE_MANAGER_BACKEND') )
				array_unshift( $links, '<a target=\'blank\' class="file-manager-admin-panel-pro" href="https://wpjos.com/library-file-manager-plugin/" style="color: white; font-weight: bold; background-color: red; padding-right: 5px; padding-left: 5px; border-radius: 40%;">'. "Pro" .'</a>');

		}

		return $links;
	}

	/**
	 *
	 * @function admin_notice
	 * @description Adds admin notices to the admin page
	 * @param void
	 * @return void
	 *
	 * */
	public function admin_notice(){

		// DISALLOW_FILE_EDIT Macro checking
		if(defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT):
		?>
		<div class='update-nag fm-error'><b>DISALLOW_FILE_EDIT</b> <?php _e("is set to", 'file-manager'); ?> <b>TRUE</b>. <?php _e("You will not be able to edit files with", 'file-manager'); ?> <a href='admin.php?page=file-manager-settings'>Library File Manager</a>. <?php _e("Please set", 'file-manager'); ?> <b>DISALLOW_FILE_EDIT</b> <?php _e("to", 'file-manager'); ?> <b>FALSE</b></div>
		<style>
			.fm-error{
				border-left: 4px solid red;
				display: block;
			}
		</style>
		<?php
		endif;
	}

}

// Activation Deactivation hook
register_activation_hook( __FILE__, 'gb_fm_activate' );

global $FileManager;
$FileManager = new FM('File Manager');

if(!function_exists('pr')):
function pr($obj){
	if (!defined('GB_DEBUG')) return;
	echo "<pre>";
	print_r($obj);
	echo "</pre>";
}
endif;
