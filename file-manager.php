<?php

/**
 * Plugin Name: Bit File Manager
 * Plugin URI: https://bitapps.pro/
 * Author:     Bit Apps
 * Author URI:  https://bitapps.pro
 * Version: 5.2.8
 * PHP version: 5.6
 * Text domain: file-manager
 * License: GPLv2
 * Description: File Manager let you manage your file the way you like. You can upload, delete, copy, move, rename, compress, extract files. You don't need to worry about ftp. It is realy simple and easy to use.
 * */

// Including elFinder class
require_once 'BootStart' . DIRECTORY_SEPARATOR . 'Constants.php';
define('BFM_BASEDIR', plugin_dir_path(__FILE__));
define('BFM_BASEURL', plugin_dir_url(__FILE__));

// define('BFM_ROOT_DIR', dirname(BFM_BASEDIR) . DIRECTORY_SEPARATOR);
// define('BFM_ROOT_URL', plugin_dir_url(BFM_BASEDIR));

define('BFM_ROOT_DIR', BFM_BASEDIR);
define('BFM_ROOT_URL', BFM_BASEURL);
define('BFM_ASSET_URL', BFM_BASEURL . 'assets/');

define('BFM_FINDER_DIR', BFM_ROOT_DIR . 'libs' . DIRECTORY_SEPARATOR . 'elFinder' . DIRECTORY_SEPARATOR);
define('BFM_FINDER_URL', plugin_dir_url(BFM_FINDER_DIR . 'php'));

// Including elFinder class
require_once BFM_FINDER_DIR . 'php' . DS . 'autoload.php';

// Including Boot Starter
require_once 'BootStart' . DS . 'BootStart.php';

// Including other necessary files
require_once 'inc/__init__.php';

// After library Loaded
do_action('file_manager_init');
class FM extends FM_BootStart
{

    /**
     *
     * @var $version Wordpress file manager plugin version
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

    public function __construct($name)
    {

        $this->version = '5.2.8';
        $this->version_no = 528;
        $this->site = 'https://bitapps.pro';
        $this->support_page = 'https://www.bitapps.pro/contact';
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
        $this->add_ajax('fm_site_backup'); // Site backup function 

        parent::__construct($name);

        // Adding plugins page links
        add_filter('plugin_action_links', array(&$this, 'plugin_page_links'), 10, 2);

        // Admin Notices
        add_action('admin_notices', array(&$this, 'admin_notice'));
    }

    /**
     *
     * File Manager connector function
     *
     *
     * @throws Exception
     */
    public function connector()
    {

        // Allowed mime types
        $mime = new FMMIME(BFM_FINDER_DIR . 'php/mime.types');
        $fmAccessControll = new FMAccessControl();

        $opts = array(
            // 'bind' => array(
            //     'put.pre' => array(new FMPHPSyntaxChecker, 'checkSyntax'), // Syntax Checking.
            //     'archive.pre back.pre chmod.pre colwidth.pre copy.pre cut.pre duplicate.pre editor.pre put.pre extract.pre forward.pre fullscreen.pre getfile.pre help.pre home.pre info.pre mkdir.pre mkfile.pre netmount.pre netunmount.pre open.pre opendir.pre paste.pre places.pre quicklook.pre reload.pre rename.pre resize.pre restore.pre rm.pre search.pre sort.pre up.pre upload.pre view.pre zipdl.pre tree.pre parents.pre ls.pre tmb.pre size.pre dim.pre' => array(&$this, 'security_check'),
            //     //				 'archive.pre back.pre chmod.pre colwidth.pre copy.pre cut.pre duplicate.pre editor.pre put.pre extract.pre forward.pre fullscreen.pre getfile.pre help.pre home.pre info.pre mkdir.pre mkfile.pre netmount.pre netunmount.pre open.pre opendir.pre paste.pre places.pre quicklook.pre reload.pre rename.pre resize.pre restore.pre rm.pre search.pre sort.pre up.pre upload.pre view.pre zipdl.pre file.pre tree.pre parents.pre ls.pre tmb.pre size.pre dim.pre get.pre' => array(&$this, 'security_check'),
            //     'upload' => array(new FMMediaSync(), 'onFileUpload'),
            //     '*' => 'fm_logger',
            // ),
            // 'debug' => WP_DEBUG,
            'roots' => array(
                array(
                    'alias'         => isset($this->options['file_manager_settings']['fm_root_folder_name']) && !empty($this->options['file_manager_settings']['fm_root_folder_name']) ? $this->options['file_manager_settings']['fm_root_folder_name'] : "WP Root",
                    'driver'        => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
                    'path'          => isset($this->options['file_manager_settings']['root_folder_path']) && !empty($this->options['file_manager_settings']['root_folder_path']) ? $this->options['file_manager_settings']['root_folder_path'] : ABSPATH,                     // path to files (REQUIRED)
                    'URL'           => isset($this->options['file_manager_settings']['root_folder_url']) && !empty($this->options['file_manager_settings']['root_folder_url']) ? $this->options['file_manager_settings']['root_folder_url'] : site_url(),                  // URL to files (REQUIRED)
                    'uploadDeny'    => array(),                // All Mimetypes not allowed to upload
                    'uploadAllow'   => $mime->get_types(), // All MIME types is allowed
                    'uploadOrder'   => array('order', 'allow', 'deny'),      // allowed Mimetype `image` and `text/plain` only
                    'accessControl' => array($fmAccessControll, 'control'),
                    'acceptedName' =>  array($fmAccessControll, 'accepted__name'), // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#acceptedName
                    'disabled'      => array(),    // List of disabled operations
                    'dispInlineRegex' => '^(?:image|application/(?:vnd\.)?(?:ms(?:-office|word|-excel|-powerpoint)|openxmlformats-officedocument)|text/plain$)', // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#dispInlineRegex
                    'trashHash'     =>   isset($this->options['file_manager_settings']['fm-create-trash-files-folders']) && !empty($this->options['file_manager_settings']['fm-create-trash-files-folders']) ? 't1_Lw' : '',                     // elFinder's hash of trash folder
                    'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too						
                    // 'defaults'   => array('read' => true, 'write' => true,'locked'=>true),
                    'allowChmodReadOnly' => true, // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#allowChmodReadOnly
                    'statOwner' => true, // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#statOwner

                    'attributes' => array( // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#attributes
                        array( // hide specipic folder.
                            'pattern' => '!^/img!',
                            'hidden' => false,
                            'read'   => true,
                            'write'  => true,
                            'locked' => false,
                        ),
                        array( // hide specipic folder.
                            'pattern' => '!^/inc!',
                            'hidden' => false,
                            'read'   => true,
                            'write'  => true,
                            'locked' => false,
                        ),
                        // array( // hide specipic file type.
                        // 	'pattern' => '!.gitignore!',
                        // 	'hidden' => false,
                        // 	'read'   => true,
                        // 	'write'  => true,
                        // 	'locked' => false,
                        // ),
                        // array( // hide all files type.
                        // 	'pattern' => '/^.+/',
                        // 	'hidden' => false,
                        // 	'read'   => true,
                        // 	'write'  => true,
                        // 	'locked' => false,
                        // ),

                    ),
                    // 'copyTo' => true, //https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#copyTo
                    'uploadMaxSize' => 0, // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#uploadMaxSize
                    // 'archiveMimes' => array(), // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#archiveMimes
                    //'dirMode'        => 0755,            // new dirs mode (default 0755)
                    //'fileMode'       => 0644,            // new files mode (default 0644)
                    // 'maxTargets'=> 0,
                ),
                array(
                    'alias'        => 'Media',
                    'driver'        => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
                    'path'          => FM_MEDIA_BASE_DIR_PATH,                     // path to files (REQUIRED)
                    'URL'           => FM_MEDIA_BASE_DIR_URL,                  // URL to files (REQUIRED)
                    'uploadDeny'    => array(),                // All Mimetypes not allowed to upload
                    'uploadAllow'   => $mime->get_types(), // All MIME types is allowed
                    'uploadOrder'   => array('allow', 'deny'),      // allowed Mimetype `image` and `text/plain` only
                    'accessControl' => array(new FMAccessControl(), 'control'),
                    'disabled'      => array(),    // List of disabled operations
                ),
            )
        );
        /**
         * Enable/Disable trash directory.
         */
        if (isset($this->options['file_manager_settings']['fm-create-trash-files-folders']) && $this->options['file_manager_settings']['fm-create-trash-files-folders']  && is_writable(FM_WP_UPLOAD_DIR['basedir'])) {
            $opts['roots'][] = array(
                'id'            => '1',
                'driver'        => 'Trash',
                'path'          => FM_TRASH_DIR_PATH,  // path to files (REQUIRED)
                'tmbURL'        => FM_TRASH_TMB_DIR_URL, // path to files (REQUIRED),
                'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
                'uploadDeny'    => array(),                // Recomend the same settings as the original volume that uses the trash
                'uploadAllow'   => $mime->get_types(), // Same as above
                'uploadOrder'   => array('deny', 'allow'),      // Same as above
                'accessControl' => array(new FMAccessControl(), 'control'),
                'acceptedName' =>  array($fmAccessControll, 'accepted__name'),              // Same as above

            );
        }

        /**
         *
         * @filter fm_options :: Options filter
         * Implementation Example: add_filter('fm_options', array($this, 'fm_options_test'), 10, 1);
         *
         * */
        $filetered_opts = apply_filters('fm_options_filter', $opts);
        if (!empty($filetered_opts['roots'])) {
            $opts = $filetered_opts;
        }
        $elFinder = new elFinderConnector(new elFinder($opts));
        $elFinder->run();
        wp_die();
    }

    public function security_check()
    {
        // Checks if the current user have enough authorization to operate.
        if (!wp_verify_nonce($_POST['file_manager_security_token'], 'fm_nonce') || !current_user_can('manage_options')) wp_die();
        check_ajax_referer('fm_nonce', 'file_manager_security_token');
    }

    /**
     *
     * Adds plugin page links,
     *
     * */
    public function plugin_page_links($links, $file)
    {

        static $this_plugin;

        if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

        if ($file == $this_plugin) {
            array_unshift($links, '<a target=\'blank\' href="https://www.bitapps.pro">' . "Support" . '</a>');

            array_unshift($links, '<a href="admin.php?page=bit-file-manager-settings">' . "Settings" . '</a>');

            array_unshift($links, '<a href="' . admin_url('admin.php?page=file-manager') . '">' . "Home" . '</a>');
        }

        return $links;
    }

    /**
     *
     * @function admin_notice
     * 
     * @description Adds admin notices to the admin page
     * 
     * @param void
     * @return void
     *
     * */
    public function admin_notice()
    {

        // DISALLOW_FILE_EDIT Macro checking
        if (defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) :
?>
            <div class='update-nag fm-error'><b>DISALLOW_FILE_EDIT</b> <?php _e("is set to", 'file-manager'); ?> <b>TRUE</b>. <?php _e("You will not be able to edit files with", 'file-manager'); ?> <a href='admin.php?page=file-manager-settings'>Library File Manager</a>. <?php _e("Please set", 'file-manager'); ?> <b>DISALLOW_FILE_EDIT</b> <?php _e("to", 'file-manager'); ?> <b>FALSE</b></div>
            <style>
                .fm-error {
                    border-left: 4px solid red;
                    display: block;
                }
            </style>
<?php
        endif;
    }
}

// Activation Deactivation hook
register_activation_hook(__FILE__, 'bfm_activate');

global $FileManager, $FMP;
$FileManager = new FM('File Manager');
$FMP = new FileManagerPermission();
