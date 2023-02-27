<?php
// Simple function API to invoke the file manager about anywhere
if (!function_exists('file_manager_permission_system_frontend')) :

    function file_manager_permission_system_frontend()
    {

        global $FMP;

        if (!is_user_logged_in()) return;

        // Adding necessary Scripts these will be added to the footer section.
        // Jquery UI CSS
        wp_enqueue_style('bfm_jquery-ui-css', $FMP->url('libs/js/jquery-ui/jquery-ui.min.css'));

        // Jquery UI theme
        wp_enqueue_style('bfm_jquery-ui-css-theme', $FMP->url('libs/js/jquery-ui/jquery-ui.theme.css'));

        // elFinder CSS
        wp_enqueue_style('bfm_elfinder-css', $FMP->url('elFinder/css/elfinder.min.css'));

        // elFinder theme CSS
        wp_enqueue_style('bfm_elfinder-theme-css', $FMP->url('elFinder/css/theme.css'));

        // elFinder Scripts depends on jQuery UI core, selectable, draggable, droppable, resizable, dialog and slider.
        wp_enqueue_script('bfm_elfinder-script', $FMP->url('elFinder/js/elfinder.full.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-selectable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-resizable', 'jquery-ui-dialog', 'jquery-ui-slider',));

        $ajax_url = site_url() . "/wp-admin/admin-ajax.php";

?>

        <div id='file-manager-frontend'></div>

        <script>
            PLUGINS_URL = '<?php echo plugins_url(); ?>';
            ajaxurl = '<?php echo $ajax_url; ?>';

            jQuery(document).ready(function() {

                jQuery('#file-manager-frontend').elfinder({
                    url: ajaxurl,
                    debug: ['error', 'warning', 'event-destroy'],
                    customData: {
                        action: 'bfm_permissions_system_connector'
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
