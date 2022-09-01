<?php

/**
 * 
 * Defines the file manager widget
 * 
 * */

// Security check
defined('ABSPATH') or die();
if (!class_exists('FileManagerWidget')) :
    class FileManagerWidget extends WP_Widget
    {

        public function __construct()
        {

            $options = array(
                'classname' => 'FileManagerWidget',
                'description' => 'Add file manager as a widget where you want.'
            );

            parent::__construct('FimeManagerWidget', 'File Manager Widget', $options);
        }

        public function widget($args, $instance)
        {
            file_manager_permission_system_frontend();
            // Outputs the content of the widget

        }

        public function form($instance)
        {

            // Outputs the options form on admin

        }

        public function update($new_instance, $old_instance)
        {

            // Processes widget options to be saved

        }
    }
endif;
