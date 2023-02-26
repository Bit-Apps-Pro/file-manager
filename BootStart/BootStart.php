<?php

// Security Check
\defined('ABSPATH') or exit();

/**
 * The starter file that holds everything together.
 *
 * @since version 0.1.1
 */

/**
 * Holds almost all the functionality that this nano framework supports.
 *
 * We will eventually add more detailed description later.
 * */
abstract class FM_BootStart
{
    /**
     * @var string $name name of the plugin
     *
     * */
    public $name;

    /**
     * @var string $prefix Plugin wide prefix that will be used to differentiate from other plugin / or system vars
     *
     * */
    public $prefix;

    /**
     * @var object $options The object of the options class
     *
     * */
    public $options;

    /**
     * @var string $upload_path :: This variable holds the path of the default upload folder
     *
     * */
    public $upload_path;

    /**
     * @var string $upload_url :: This variable holds the url of the default upload folder
     *
     * */
    public $upload_url;

    public $devEnv;

    public $STD;

    /**
     * @var string $path Absolute path of the plugin.
     *
     * */
    protected $path;

    /**
     * @var array $SCD Short Code Data
     *
     * */
    protected $SCD;

    /**
     * @var array $menu :: Defines how the menu would be
     *
     * */
    protected $menu_data;

    /**
     * Constructor function
     *
     * This function does the works that every plugin must do like checking ABSPATH,
     * triggering activation and deactivation hooks etc.
     *
     * @todo Add an uninstall function
     *
     * @param mixed $name
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

        // Options

        // Default options
        $default_options = [
            'file_manager_settings' => [
                'show_url_path' => 'show',
                'language'      => [
                    'code'     => 'en',
                    'name'     => 'Default',
                    'file-url' => plugins_url('libs/elFinder/js/i18n/elfinder.en.js', BFM_FINDER_DIR),
                ],
                'size' => [
                    'width'  => 'auto',
                    'height' => '500'
                ],
                'fm_default_view_type'  => 'icons',
                'fm_display_ui_options' => ['toolbar', 'places', 'tree', 'path', 'stat']
            ],
        ];

        $this->options = get_option($this->prefix);
        if (empty($this->options)) {
            $this->options = $default_options;
        } else {
            $this->options = array_merge($default_options, $this->options);
        }

        register_shutdown_function([&$this, 'save_options']);

        // Creating upload folder.
        // $this->upload_folder();

        // Frontend asset loading
        // add_action('wp_enqueue_scripts', [$this, 'assets']);

        // Dashboard asset loading
        // add_action('admin_enqueue_scripts', array(&$this, 'admin_assets'));

        // Adding a menu at admin area
        // add_action('admin_menu', array(&$this, 'menu'));

        // Shortcode hook
        // add_action('init', [&$this, 'shortcode']);
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
        $themes    = [];
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
                    $themes[$variant] = BFM_ASSET_URL . "themes/{$theme}/{$variant}/{$variant}.json";
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
            // $theme = $this->options['file_manager_settings']['theme'];
        }

        return $theme;
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

        return strtolower($string);
    }

    /**
     * @function save_options
     *
     * */
    public function save_options()
    {
        update_option($this->prefix, $this->options);
    }
}
