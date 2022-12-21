<?php

if (!function_exists('bfm_file_name_validator')) {
    function bfm_file_name_validator($name)
    {
        return true;
    }
}

// Including the widget class
require_once BFM_ROOT_DIR . 'views/widgets/file-manager-widget.php';

if (!function_exists('pr')) :
    /**
     * Debugging function
     * 
     * @param mixed $obj Data to print for debug
     * 
     * @return void
     */
    function pr($obj)
    {
        if (!defined('GB_DEBUG')) return;
        echo "<pre>";
        print_r($obj);
        echo "</pre>";
    }
endif;

if (!function_exists('access_control')) :
    /**
     * Simple function to demonstrate how to control file access using "accessControl" callback.
     * This method will disable accessing files/folders starting from '.' (dot)
     *
     * @param string $attr   attribute name (read|write|locked|hidden)
     * @param string $path   file path relative to volume root directory started with directory separator
     * @param string $data   unknown
     * @param string $volume unknown
     * 
     * @return bool|null
     **/
    function access_control($attr, $path, $data, $volume)
    {
        return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
            ? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
            :  null;                                    // else elFinder decide it itself
    }
endif;


// Simple function API to invoke the file manager about anywhere
if (!function_exists('file_manager_permission_system_frontend')) :

    function file_manager_permission_system_frontend()
    {

        global $FMP;

        if (!is_user_logged_in()) return;

        // Adding necessary Scripts these will be added to the footer section.
        // Jquery UI CSS
        wp_enqueue_style('fmp_jquery-ui-css', $FMP->url('jquery-ui-1.11.4/jquery-ui.min.css'));

        // Jquery UI theme
        wp_enqueue_style('fmp_jquery-ui-css-theme', $FMP->url('jquery-ui-1.11.4/jquery-ui.theme.css'));

        // elFinder CSS
        wp_enqueue_style('fmp_elfinder-css', $FMP->url('elFinder/css/elfinder.min.css'));

        // elFinder theme CSS
        wp_enqueue_style('fmp_elfinder-theme-css', $FMP->url('elFinder/css/theme.css'));

        // elFinder Scripts depends on jQuery UI core, selectable, draggable, droppable, resizable, dialog and slider.
        wp_enqueue_script('fmp_elfinder-script', $FMP->url('elFinder/js/elfinder.full.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-selectable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-resizable', 'jquery-ui-dialog', 'jquery-ui-slider',));

        $ajax_url = site_url() . "/wp-admin/admin-ajax.php";

?>

        <div id='file-manager-pro-wrapper'></div>

        <script>
            PLUGINS_URL = '<?php echo plugins_url(); ?>';
            ajaxurl = '<?php echo $ajax_url; ?>';

            jQuery(document).ready(function() {

                jQuery('#file-manager-pro-wrapper').elfinder({
                    url: ajaxurl,
                    debug: ['error', 'warning', 'event-destroy'],
                    customData: {
                        action: 'connector_pro'
                    },
                });
            });
        </script>

        <style>
            div.ui-widget-content:nth-child(11)>div:nth-child(1) {
                display: none !important;
            }
        </style>


<?php
    }

endif;
