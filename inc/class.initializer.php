<?php
// Security Check
defined('ABSPATH') || die();

if(!class_exists('SettingsInitializer')):
class SettingsInitializer{

    public static $settings = array();

    static function init_settings(){

        // Loading the variables here.
        SettingsInitializer::$settings['do-not-use-for-admin'] = 'do-not-use-for-admin';
        SettingsInitializer::$settings['file_type'] = array('text', 'image', 'application', 'video', 'audio');
        SettingsInitializer::$settings['file-size'] = 2;
        SettingsInitializer::$settings['single-folder'] = 'folder_options_single';
        SettingsInitializer::$settings['folder_options-separate'] = 'separate-folder';
        SettingsInitializer::$settings['folder_options-userrole'] = 'userrole-folder';
        SettingsInitializer::$settings['administrator'] = array('download', 'upload', 'cut', 'copy', 'duplicate', 'paste', 'rm', 'mkdir', 'mkfile', 'edit', 'rename', 'archive', 'extract', 'path' => '');

        update_option('fmp_permission_system', SettingsInitializer::$settings);
    }

}
endif;
