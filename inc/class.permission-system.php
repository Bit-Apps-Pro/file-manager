<?php

use BitApps\FM\Providers\AccessControlProvider;
use BitApps\FM\Providers\PermissionsProvider;

defined('ABSPATH') or die();



class FileManagerPermission
{
    /**
     * Version Number
     *
     * @var $version Version number of the plugin
     * */
    public $version;

    /**
     * Root path of the plugin
     *
     * @var string $path
     * */
    public $path;

    public $url;

    /**
     * Stores the shortcode output
     *
     * @var string $shortcode_output
     * */
    public $shortcode_output;

    /**
     * Works on the update notification
     *
     * @var object $fmp_updater
     * */
    public $fmp_updater;

    /**
     * Holds the list of operations
     *
     * @var array $list_of_operations
     * */
    public $list_of_operations;

    /**
     * Defines if the current user is banned
     *
     * @var boolean $current_user_banned
     * */
    public $current_user_banned;

    /**
     * Permissions settings helper
     *
     * @var PermissionsProvider $permissionSettings
     * */
    public $permissionSettings;

    /**
     * Constructor function
     *
     * Does all the initialization part
     * */
    public function __construct()
    {

        $this->list_of_operations = array('download', 'upload', 'cut', 'copy', 'duplicate', 'paste', 'rm', 'mkdir', 'mkfile', 'edit', 'rename', 'archive', 'extract', 'ban');

        $this->current_user_banned = 'not-banned';

        $this->permissionSettings = new PermissionsProvider();

        // Changing footer
        add_filter('fm_footer', [$this, 'footer']);

        // Admin Menu for all user roles
        add_filter('fm_capabilities', [$this, 'filterAdminMenuCapabilities']);

        // Adding general shortcode
        // add_shortcode('file_manager', [$this, 'file_manager_view']);

        // Managing Admin backend options
        add_filter('fm_options_filter', [$this, 'admin_options']);

        // Adding public shortcode
        add_shortcode('file_manager_public', [$this, 'file_manager_public_view']);

        // Adding Ajax for private folder
        // $this->add_ajax('bfm_permissions_system_connector');

        // Adding Ajax
        add_action('wp_ajax_bfm_permissions_system_connector', [$this, 'handlePermissionSystemAjax']); // Logged in users
        add_action('wp_ajax_nopriv_bfm_permissions_system_connector', [$this, 'handlePermissionSystemAjax']); // Guest in users


        // Checking for update
        // $fmp_updater = new FMUpdater($this->version);
    }

    /**
     *
     * @function menu Menu invoker for permission system
     *
     * */
    public function menu()
    {
    }

    /**
     *
     * @function admin_menu_file_manager
     * @description All the other user who have access to the admin dashboard can see the file manager
     *
     * */
    public function filterAdminMenuCapabilities($capabilities)
    {
        if ($this->permissionSettings->isCurrentUserHasPermission()) {
            return $this->permissionSettings->currentUserRole();
        }

        return $capabilities;
    }

    /**
     *
     * View for file manager
     *
     * */
    public function file_manager_view($data)
    {

        include BFM_ROOT_DIR . 'views/shortcode/file_manager_view.php';
        return $this->shortcode_output;
    }

    /**
     *
     * View public file manager without login.
     *
     * */
    public function file_manager_public_view($data)
    {

        include BFM_ROOT_DIR . 'views/shortcode/file_manager_public_view.php';
    }

    /**
     *
     * File Manager Pro Connector function new
     *
     * */
    public function handlePermissionSystemAjax()
    {

        $settings = get_option('file_manager_permissions', []);

        /**
         *
         * ### Algorithm ###
         * 1. Checking if the user is logged in or not
         *  a. If logged in then a the process will go to a different function
         *  b. else the user is a guest then it will call guest processing function.
         * */

        $current_user = wp_get_current_user();
        if (empty($current_user->roles)) {
            $opts = $this->guest_processor($settings);
        } else {
            $opts = $this->user_processor($settings);
        }

        // var_dump($current_user->roles);
        // exit;

        $opts = apply_filters('fmp_options_filter', $opts);
        $elFinder = new elFinderConnector(new elFinder($opts));
        $elFinder->run();

        //		$elFinder = new FM_EL_Finder();
        //		$elFinder = $elFinder->connect($opts);
        //		$elFinder->run();

        wp_die();
    }

    /**
     *
     * @function user_processor
     * @description Checks the settings of that particular user and generates an options configuration
     * @param array $settings Settings data from the wordpress options
     * @return array $options Options variable for the elfinder
     *
     * */
    public function user_processor($settings)
    {

        global $FileManager;

        $current_user = wp_get_current_user();
        $user_role = $current_user->roles[0];
        $user_login = $current_user->data->user_login;
        $user_id = $current_user->ID;

        // File manager and File Manager pro security check synchronization
        if (!isset($settings['do_not_use_for_admin']) || empty($settings['do_not_use_for_admin']) || $user_role != 'administrator') $security_check_callback = array(&$FileManager, 'security_check');
        else $security_check_callback = array(&$this, 'security_check');

        // Returnable $options variable
        $options = array(
            'bind' => array(
                //auto::
                'ls.pre tree.pre parents.pre tmb.pre zipdl.pre size.pre mkdir.pre mkfile.pre rm.pre rename.pre duplicate.pre paste.pre upload.pre get.pre put.pre archive.pre extract.pre search.pre info.pre dim.pre resize.pre netmount.pre url.pre callback.pre chmod.pre' => $security_check_callback,
            ),
            'debug' => true,
            'roots' => array(),
        );

        // Folder list for the current user.
        $folder_list = array();
        // Loading permissions
        $permission_list = $this->processPermissionList($settings, $current_user);
        //auto::  $permission_list = in_array('ban', $permission_list) ? array() && $this->current_user_banned = 'ban' : $permission_list; // If the user is banned then their will be no permissions
        if (in_array('ban', $permission_list)) {
            $permission_list = array();
            $this->current_user_banned = 'ban';
        }
        $operation_list = $this->list_of_operations;
        $disabled_permissions = array();
        foreach ($operation_list as $operation) if (!in_array($operation, $permission_list)) $disabled_permissions[] = $operation;

        // Root Folder
        $settings['root_folder'] = isset($settings['root_folder']) && !empty($settings['root_folder']) ? trailingslashit($settings['root_folder']) : trailingslashit(FM_UPLOAD_BASE_DIR);
        $settings['root_folder_url'] = isset($settings['root_folder_url']) && !empty($settings['root_folder_url']) ? trailingslashit($settings['root_folder_url']) : trailingslashit(FM_UPLOAD_BASE_URL);
        if (!is_dir($settings['root_folder'])) {
            mkdir($settings['root_folder'], 0755, true);
        } // Creating root folder if it doesn't exists.

        // Personal Folder
        if (isset($settings['folder_options_separate']) && !empty($settings['folder_options_separate'])) {
            $folder_list[] = array(
                'path' => $settings['root_folder'] . $user_login,
                'url' => $settings['root_folder_url'] . $user_login,
            );
        }

        // Public Folder
        if (isset($settings['folder_options_single']) && !empty($settings['folder_options_single'])) {
            $folder_list[] = array(
                'path' => $settings['public_folder_path'],
                'url' => '',
            );
            if ($FileManager->options['file_manager_settings']['show_url_path'] == 'show') $folder_list['url'] = $settings['public_folder_url'];
        }

        // Extra User personal Folder from the input field
        if (isset($settings[$user_login]['path']) && !empty($settings[$user_login]['path'])) {
            $user_personal_folder_list = explode(',', $settings[$user_login]['path']);
            foreach ($user_personal_folder_list as $user_personal_folder) {
                $user_personal_folder = trim($user_personal_folder);
                if ($user_personal_folder[0] == DS) { // If the realpath is included then avoid the root path
                    $folder_list[] = array(
                        'path' => $user_personal_folder,
                        'url' => $user_personal_folder,
                    );
                } else {
                    $folder_list[] = array(
                        'path' => $settings['root_folder'] . $user_personal_folder,
                        'url' => $settings['root_folder_url'] . $user_personal_folder,
                    );
                }
            }
        }

        // User Role Folder
        if (isset($settings['folder_options_userrole']) && !empty($settings['folder_options_userrole'])) {
            $folder_list[] = array(
                'path' => $settings['root_folder'] . $user_role,
                'url' => $settings['root_folder_url'] . $user_role,
            );
        }

        // Extra User Role Folder from the input field
        if (isset($settings[$user_role]['path']) && !empty($settings[$user_role]['path'])) {
            $user_role_folder_list = explode(',', $settings[$user_role]['path']);
            foreach ($user_role_folder_list as $user_role_folder) {
                $user_role_folder = trim($user_role_folder);

                if ($user_role_folder[0] == DS) { // If the realpath is included then avoid the root path
                    $folder_list[] = array(
                        'path' => $user_role_folder,
                        'url' => $user_role_folder,
                    );
                } else {
                    $folder_list[] = array(
                        'path' => $settings['root_folder'] . $user_role_folder,
                        'url' => $settings['root_folder_url'] . $user_role_folder,
                    );
                }
            }
        }

        $fm_access_control = new AccessControlProvider();
        foreach ($folder_list as $folder) {
            if (!is_array($folder)) {
                continue;
            }
            $options['roots'][] = array(
                'driver'        => 'LocalFileSystem',      // driver for accessing file system (REQUIRED)
                'path'          => $folder['path'],        // path to files (REQUIRED)
                'URL'           => $folder['url'],            // URL to files (REQUIRED)
                'uploadDeny'    => array(),                // All Mimetypes not allowed to upload
                'uploadAllow'   => $settings['file_type'], // Mimetype `image` and `text/plain` allowed to upload
                'uploadOrder'   => array('allow', 'deny'), // allowed Mimetype `image` and `text/plain` only
                'disabled' => $disabled_permissions,
                'acceptedName' => ['bfm_file_name_validator', 'validateName'],
                'defaults'   => array('read' => true, 'write' => true, 'hidden' => false, 'locked' => false),
                'uploadMaxSize' => $settings['file_size'] . "M", // Maximum file upload size
                'accessControl' => array(&$fm_access_control, 'control'),
            );
            if ((isset($folder['path']) && !empty($folder['path'])) && !is_dir($folder['path'])) {
                mkdir($folder['path'], 0755);
            }
        }

        return $options;
    }

    /**
     *
     * @function guest_processor
     * @description Checks the settings of guest user and generates an options configuration
     * @param array $settings Settings data from the wordpress options
     * @return array $options Options variable for the elfinder
     *
     * */
    public function guest_processor($settings)
    {

        global $FileManager;

        // Returnable $options variable
        $options = array(
            'bind' => array(
                'ls.pre tree.pre parents.pre tmb.pre zipdl.pre size.pre mkdir.pre mkfile.pre rm.pre rename.pre duplicate.pre paste.pre upload.pre get.pre put.pre archive.pre extract.pre search.pre info.pre dim.pre resize.pre netmount.pre url.pre callback.pre chmod.pre' => array(&$this, 'security_check'),
            ),
            'debug' => true,
            'roots' => array(),
        );

        $folder_list = array();

        // Root Folder
        $settings['root_folder'] = isset($settings['root_folder']) && !empty($settings['root_folder']) ? trailingslashit($settings['root_folder']) : trailingslashit(FM_UPLOAD_BASE_DIR);
        $settings['root_folder_url'] = isset($settings['root_folder_url']) && !empty($settings['root_folder_url']) ? trailingslashit($settings['root_folder_url']) : trailingslashit(FM_UPLOAD_BASE_URL);
        if (!is_dir($settings['root_folder'])) mkdir($settings['root_folder'], 0755); // Creating root folder if it doesn't exists.

        // Public Folder
        if (isset($settings['fmp_guest']['path']) && !empty($settings['fmp_guest']['path'])) {
            $user_role_folder_list = explode(',', $settings['fmp_guest']['path']);
            foreach ($user_role_folder_list as $user_role_folder) {
                $user_role_folder = trim($user_role_folder);

                if ($user_role_folder[0] == DS) { // If the realpath is included then avoid the root path
                    $folder_list[] = array(
                        'path' => $user_role_folder,
                        'url' => $user_role_folder,
                    );
                } else {
                    $folder_list[] = array(
                        'path' => $settings['root_folder'] . $user_role_folder,
                        'url' => $settings['root_folder_url'] . $user_role_folder,
                    );
                }
            }
        }

        $disabled_permissions = $this->list_of_operations;
        if (count($settings['fmp_guest']) <= 1) $disabled_permissions[] = 'download';

        foreach ($folder_list as $folder) {
            $options['roots'][] = array(
                'driver'        => 'LocalFileSystem',      // driver for accessing file system (REQUIRED)
                'path'          => $folder['path'],        // path to files (REQUIRED)
                'URL'           => $folder['url'],            // URL to files (REQUIRED)
                'uploadDeny'    => array(),                // All Mimetypes not allowed to upload
                'uploadAllow'   => $settings['file_type'], // Mimetype `image` and `text/plain` allowed to upload
                'uploadOrder'   => array('allow', 'deny'), // allowed Mimetype `image` and `text/plain` only
                'disabled' => $disabled_permissions,
                'acceptedName' => 'bfm_file_name_validator',
                'defaults'   => array('read' => true, 'write' => true, 'hidden' => false, 'locked' => false),
                'attributes' => array(
                    array( // Hiding all hidden files
                        'pattern' => '/.tmb/',
                        'read' => false,
                        'write' => false,
                        'hidden' => true,
                        'locked' => true,
                    ),
                ),
            );
            if (!is_dir($folder['path'])) mkdir($folder['path'], 0755);
        }

        return $options;
    }

    /**
     * Process permission list from permission settings
     * 
     * @param array $settings
     * 
     * @return array
     */
    public function processPermissionList($settings, $current_user)
    {
        if (empty($settings)) {
            return [];
        }
        $permissionList = [];
        if (isset($settings[$current_user->data->user_login]) && count($settings[$current_user->data->user_login]) > 1) {
            $permissionList = $settings[$current_user->data->user_login];
        } elseif (isset($settings[$current_user->roles[0]])) {
            $permissionList = $settings[$current_user->roles[0]];
        }
        return $permissionList;
    }
    public function security_check()
    {
        // Checks if the current user have enough authorization to operate.
        if (!wp_verify_nonce($_POST['bfm_nonce'], 'bfm_nonce')) wp_die();
        check_ajax_referer('bfm_nonce', 'bfm_nonce');
    }

    /**
     *
     * @function is_banned
     * @description Checks if the current user is banned.
     * @return boolean
     *
     * */
    public function is_bannned()
    {
        if (!is_user_logged_in()) {
            return false;
        }
        $settings = get_option('file_manager_permissions', []);
        if (empty($settings)) {
            return false;
        }

        $current_user = wp_get_current_user();
        $user_role = $current_user->roles[0];
        $user_login = $current_user->data->user_login;
        $user_id = $current_user->ID;
        $permission_list = $this->processPermissionList($settings, $current_user);
        if (in_array('ban', $permission_list)) {
            $permission_list = array();
            $this->current_user_banned = 'ban';
        }
        if ($this->current_user_banned == 'ban') return true;
        else return false;
    }

    /**
     *
     * @function no_permission
     * @description Checkes if the user has no permission set. If true then the user will be able to see the file but would not do anything.
     * @return boolean
     *
     * */
    public function no_permission()
    {
        if (!is_user_logged_in()) {
            return false;
        }
        $settings = get_option('file_manager_permissions', []);
        if (empty($settings)) {
            return false;
        }
        $current_user = wp_get_current_user();
        $permission_list = $this->processPermissionList($settings, $current_user);

        if (count($permission_list) > 1) return false;
        else return true;
    }

    //
    public function admin_options($options)
    {

        $current_user = wp_get_current_user();
        $settings = get_option('file_manager_permissions', []);

        if (
            isset($settings['do_not_use_for_admin']) && $settings['do_not_use_for_admin'] == 'do_not_use_for_admin'
            && $current_user->roles[0] == 'administrator'
        ) {
            return $options;
        }

        /**
         *
         * ### Algorithm ###
         * 1. Checking if the user is logged in or not
         *  a. If logged in then a the process will go to a different function
         *  b. else the user is a guest then it will call guest processing function.
         * */

        if (!empty($current_user->roles) && !empty($settings)) {
            $options = $this->user_processor($settings);
        }
        return $options;
    }

    /**
     *
     * Changing footer text for premium plugin
     *
     * */

    public function footer($footer_text)
    {
        return "<span class='fmp_footer'>File Manager Premium</span>";
    }

    /**
     *
     * Adds ajax hooks and functions automatically
     *
     *
     * @param string $name Name of the function
     *
     * @param bool $guest Should the function work for guests *Default: false*
     *
     * */
    public function add_ajax($name, $guest = false)
    {

        // Adds admin ajax
        $hook = 'wp_ajax_' . $name;
        add_action($hook, array(&$this, $name));

        // Allow guests
        if (!$guest) return;

        $hook = 'wp_ajax_nopriv_' . $name;
        add_action($hook, array(&$this, $name));
    }

    /**
     *
     * Absolute URL finder
     *
     * @param string $string the relative url
     *
     * */
    public function url($string)
    {
        return BFM_BASEURL . $string;
        // return plugins_url('/' . $this->prefix . '/' . $string);
    }

    /**
     *
     * For presentable version of slugs
     *
     * */
    public function __p($string)
    {
        $string = str_replace('_', ' ', $string);
        $string = str_replace('-', ' ', $string);
        $string[0] = strtoupper($string[0]);
        return $string;
    }

    /**
     *
     * string compression function
     *
     * */
    public function zip($string)
    {

        $string = trim($string);
        $string = str_replace(' ', '-', $string);
        $string = strtolower($string);
        return $string;
    }
}
