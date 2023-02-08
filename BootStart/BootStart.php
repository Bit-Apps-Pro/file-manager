<?php

// Security Check
defined('ABSPATH') or die();

/**
 * The starter file that holds everything together.
 *
 * @package BootStart_1_0_0
 *
 * @since version 0.1.1
 **/

/**
 * Holds almost all the functionality that this nano framework supports.
 *
 * We will eventually add more detailed description later.
 * */
abstract class FM_BootStart
{

    /**
     *
     * @var string $name name of the plugin
     *
     * */
    public $name;

    /**
     *
     * @var string $prefix Plugin wide prefix that will be used to differentiate from other plugin / or system vars
     *
     * */
    public $prefix;

    /**
     *
     * @var string $path Absolute path of the plugin.
     *
     * */
    protected $path;

    /**
     *
     * @var array $SCD Short Code Data
     *
     * */
    protected $SCD;

    /**
     *
     * @var object $options The object of the options class
     *
     * */
    public $options;

    /**
     *
     * @var string $upload_path :: This variable holds the path of the default upload folder
     *
     * */
    public $upload_path;

    /**
     *
     * @var string $upload_url :: This variable holds the url of the default upload folder
     *
     * */
    public $upload_url;

    /**
     *
     * @var array $menu :: Defines how the menu would be
     *
     * */
    protected $menu_data;

    public $devEnv;

    public $STD;

    /**
     *
     * Constructor function
     *
     *
     * This function does the works that every plugin must do like checking ABSPATH,
     * triggering activation and deactivation hooks etc.
     *
     * @todo Add an uninstall function
     *
     * */
    function __construct($name)
    {

        // Assigning name
        $this->name = __(trim($name), 'file-manager');

        // Assigning prefix
        $this->prefix = str_replace(' ', '-', strtolower(trim($this->name)));

        // Assigning path
        $this->path = __FILE__;

        // Assigning DevEnv
        $this->devEnv = false;

        // Upload folder path
        $upload = wp_upload_dir();
        $this->upload_path = $upload['basedir'] . DS . $this->prefix;

        // Upload folder url
        $upload = wp_upload_dir();
        $this->upload_url = $upload['baseurl'] . '/' . $this->prefix;

        // Setting php.ini variables
        $this->php_ini_settings();

        // Options

        // Default options
        $default_options = [
            'file_manager_settings' => array(
                'show_url_path' => 'show',
                'language' => array(
                    'code' => 'en',
                    'name' => 'Default',
                    'file-url' => plugins_url('libs/elFinder/js/i18n/elfinder.en.js', BFM_FINDER_DIR),
                ),
                'size' => array(
                    'width' => 'auto',
                    'height' => '500'
                ),
                'fm_default_view_type' => 'icons',
                'fm_display_ui_options' => ['toolbar', 'places', 'tree', 'path', 'stat']
            ),
        ];

        $this->options = get_option($this->prefix);
        if (empty($this->options)) {
            $this->options = $default_options;
        } else {
            $this->options = array_merge($default_options, $this->options);
        }

        register_shutdown_function(array(&$this, 'save_options'));

        // Creating upload folder.
        $this->upload_folder();

        // Frontend asset loading
        add_action('wp_enqueue_scripts', array(&$this, 'assets'));

        // Dashboard asset loading
        add_action('admin_enqueue_scripts', array(&$this, 'admin_assets'));

        // Adding a menu at admin area
        add_action('admin_menu', array(&$this, 'menu'));

        // Shortcode hook
        add_action('init', array(&$this, 'shortcode'));
    }

    /**
     *
     * Set the all necessary variables of php.ini file.
     *
     * @todo Add some php.ini variables.
     *
     * */
    protected function php_ini_settings()
    {

        // This should have a standard variable list.
        /**
         *
         * ## Increase file upload limit
         * ## Turn on error if of php if debugging variable is defined and set to true.
         *
         * */
        ini_set('post_max_size', '128M');
        ini_set('upload_max_filesize', '128M');
    }

    /**
     *
     * Loads frontend assets
     *
     * */
    public function assets()
    {

        $this->elfinder_assets(); // Loads all the assets necessary for elFinder

        // Including front-style.css
        wp_register_style('fm-front-style', $this->url('css/front-style.css'), false);

        // Including front-script.js
        wp_register_script('fm-front-script', $this->url('js/front-script.js'), array(), '1.0.0', true);
    }

    /*
     *
     * Loads the backend / admin assets
     *
     * */
    public function admin_assets()
    {

        $this->elfinder_assets(); // Loads all the assets necessary for elFinder

        wp_register_style('fmp_permission-system-tippy-css', $this->url('libs/js/tippy-v0.2.8/tippy.css'));

        // Admin scripts
        wp_register_script('fmp_permission-system-tippy-script', $this->url('libs/js/tippy-v0.2.8/tippy.js'), array('jquery'));
        wp_register_script('fmp_permission-system-admin-script', $this->url('assets/js/admin-script.js'), array('fmp_permission-system-tippy-script'));

        // Including admin-style.css
        wp_register_style('fmp-admin-style', $this->url('assets/css/style.min.css'));

        // Including admin-script.js
        wp_register_script('fmp-admin-script', $this->url('assets/js/admin-script.js'), array('jquery'));
    }

    /**
     *
     * @function elfinder_assets
     * @description Registers all the elfinder assets
     *
     * */
    public function elfinder_assets()
    {
        $jquery_ui_url = BFM_ROOT_URL . 'libs/js/jquery-ui/jquery-ui.min.css';
        $jquery_ui_url = apply_filters('fm_jquery_ui_theme_hook', $jquery_ui_url);

        // Jquery UI CSS
        wp_register_style('fmp-jquery-ui-css',  $jquery_ui_url);

        $elfinder_style = $this->is_minified_file_load('fmp-elfinder-css');
        // elFinder CSS
        wp_register_style($elfinder_style['handle'], BFM_FINDER_URL . 'css/elfinder' . $elfinder_style['file_type'] . 'css');
        // wp_register_style($elfinder_style['handle'], BFM_FINDER_URL . 'css/elfinder' . $elfinder_style['file_type'] . 'css', array('fmp-jquery-ui-css'));
        wp_register_style('fmp-elfinder-theme-css', BFM_ROOT_URL . 'libs/js/jquery-ui/jquery-ui.theme.min.css');

        // elFinder Scripts depends on jQuery UI core, selectable, draggable, droppable, resizable, dialog and slider.
        $elfinder_script = $this->is_minified_file_load('fmp-elfinder-script');
        wp_register_script($elfinder_script['handle'], BFM_FINDER_URL . 'js/elfinder' . $elfinder_script['file_type'] . 'js', array('jquery', 'jquery-ui-core', 'jquery-ui-selectable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-resizable', 'jquery-ui-dialog', 'jquery-ui-slider', 'jquery-ui-tabs'));
        $editor_script = $this->is_minified_file_load('fmp-elfinder-editor-script');
        wp_register_script($editor_script['handle'], BFM_FINDER_URL . 'js/extras/editors.default' . $editor_script['file_type'] . 'js', array($elfinder_script['handle']));

        wp_localize_script(
            $elfinder_script['handle'],
            "fm",
            array(
                'ajax_url'         => admin_url('admin-ajax.php'),
                'nonce'         => wp_create_nonce('fm_nonce'),
                'plugin_dir'    => BFM_ROOT_DIR,
                'plugin_url'     => BFM_ROOT_URL,
                'js_url'         => BFM_FINDER_URL . "js/",
                'elfinder'         => BFM_FINDER_URL,
                'themes'       => $this->themes(),
                "theme" => $this->selectedTheme()
            )
        );
    }

    /**
     * Returns all available themes
     *
     * @return array
     */
    public function themes()
    {
        $themeBase = BFM_ROOT_DIR . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'themes';
        $themeDirs = scandir($themeBase);
        $themes = [];
        foreach ($themeDirs as $theme) {
            if ($theme === '.' || $theme === '..') {
                continue;
            }
            $variants = scandir($themeBase . DIRECTORY_SEPARATOR . $theme);
            foreach ($variants as $variant) {
                if ($variant === '.' || $variant === '..') {
                    continue;
                }
                if (is_readable($themeBase . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . $variant . DIRECTORY_SEPARATOR . $variant . '.json')) {
                    $themes[$variant] = BFM_ASSET_URL . "themes/$theme/$variant/$variant.json";
                }
            }
        }
        return $themes;
    }

    /**
     * Returns selected theme from settings
     *
     * @return array
     */
    public function selectedTheme()
    {
        $theme = 'default';
        if (isset($this->options['file_manager_settings']['theme'])) {
            $theme = $this->options['file_manager_settings']['theme'];
        }
        return $theme;
    }

    /**
     * Load minified files if WP_DEBUG || WP_DEBUG_LOG true
     */
    public function is_minified_file_load($handle_name)
    {

        if (WP_DEBUG) {
            return [
                'handle' => $handle_name,
                'file_type' => ('fmp-elfinder-script' === $handle_name || 'fmp-elfinder-css' === $handle_name) ? '.full.' :  '.'
            ];
        }

        return [
            'handle' => $handle_name . '-min',
            'file_type' => '.min.'
        ];
    }

    /**
     *
     * Adds a sidebar/sub/top menu
     *
     * */
    public function menu()
    {

        if (empty($this->menu_data)) return;

        if ($this->menu_data['type'] == 'menu') {

            $capabilities = 'administrator';
            $capabilities = apply_filters('fm_capabilities', $capabilities);

            // Main Menu
            add_menu_page('Bit File Manager | Dashboard', 'Bit File Manager', $capabilities, $this->prefix, array(&$this, 'admin_panel'), $this->url('assets/img/icon-24x24.png'), 2);
            // Settings Page
            add_submenu_page($this->prefix, 'Bit File Manager | Dashboard', 'Home', 'manage_options', $this->prefix, array(&$this, 'admin_panel'));
            add_submenu_page($this->prefix, 'Bit File Manager Settings', 'Settings', 'manage_options', $this->zip('Bit File Manager Settings'), array(&$this, 'settings'));

            add_submenu_page(
                'file-manager', // Parent Slug
                'Bit File Manager | Sets permission for specific user or by user role', // Page title
                'Permissions', // Menu title
                'manage_options', // User capabilities
                'file-manager-permission-system', // Menu Slug
                function () {
                    include_once BFM_ROOT_DIR . 'views' . DS . 'admin' . DS . 'permission_system.php';
                }
            );
            // System Page
            add_submenu_page($this->prefix, 'System Information', 'System Info', 'manage_options', $this->zip('System Information'), array(&$this, 'systems'), 3);
        }
    }

    /**
     *
     * Adds an admin page to the backend.
     *
     * */
    public function admin_panel()
    {

        $this->render('', 'admin' . DS . 'index');
    }

    /**
     * Adds a settings page
     *
     * */
    public function settings()
    {

        if (!current_user_can('manage_options')) die($this->render('', 'access-denied'));

        $this->render('', 'admin' . DS . 'settings');
    }

    /**
     * Adds a System page
     *
     * */
    public function systems()
    {

        if (!current_user_can('manage_options')) die($this->render('', 'access-denied'));

        $this->render('', 'admin' . DS . 'utility');
    }

    /**
     * Absolute URL plugin root
     *
     * @param string $string the relative url
     *
     * */
    public function finderUrl($string)
    {

        return BFM_FINDER_URL . $string;
    }

    /**
     * Absolute URL finder
     *
     * @param string $string the relative url
     *
     * */
    public function url($string)
    {

        return BFM_ROOT_URL . $string;
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
        add_action($hook, array($this, $name));

        // Allow guests
        if (!$guest) return;

        $hook = 'wp_ajax_nopriv_' . $name;
        add_action($hook, array($this, $name));
    }

    /**
     *
     * Get the script for ajax request
     *
     *
     * @param string $name Name of the ajax request fuction.
     *
     * @param array $data Post data to send
     *
     * @return string $script A jQuery.post() request function to show on the the main page.
     *
     * */
    public function get_ajax_script($name, $data)
    {

        $data['action'] = $name;

?>

        jQuery.post(
        '<?php echo admin_url('admin-ajax.php'); ?>',
        <?php echo json_encode($data); ?>
        '<?php echo $name; ?>'
        );

<?php

    }

    /**
     *
     * Adds Shortcodes
     *
     * */
    public function shortcode()
    {

        if (empty($this->STD)) return;

        foreach ($this->STD as $std) {

            $ret = add_shortcode($std, array($this, $std . '_view'));
        }
    }

    /**
     *
     * Includes a view file form the view folder which matches the called functions name
     *
     * @param string $view_file Name of the view file.
     *
     * */
    protected function render($data = null, $view_file = null)
    {

        if ($view_file == null) {

            // Generates file name from function name
            $trace = debug_backtrace();
            $view_file = $trace[1]['function'] . '.php';
        } else {

            $view_file .= '.php';
        }

        include BFM_ROOT_DIR . DS . "views" . DS . $view_file;
    }

    /**
     *
     * @function upload_folder Checks if the upload folder is present. If not creates a upload folder.
     *
     * */
    protected function upload_folder()
    {

        // Creats upload directory for this specific plugin
        if (!is_dir($this->upload_path)) mkdir($this->upload_path, 0777);
    }

    /**
     * String compression function
     *
     * @param string $string String to compress
     *
     * @return string
     * */
    public function zip($string)
    {

        $string = trim($string);
        $string = str_replace(' ', '-', $string);
        $string = strtolower($string);
        return $string;
    }

    /**
     *
     * @function save_options
     *
     * */
    public function save_options()
    {
        update_option($this->prefix, $this->options);
    }
}
