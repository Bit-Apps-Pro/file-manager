<?php
// Access controll of files.

// Security Check
defined('ABSPATH') || die();

if(!class_exists('FMAccessControl')):

class FMAccessControl{

	public $settings;

	function __construct(){
		global $FileManager;
		$this->settings = $FileManager->options['file_manager_settings'];
	}
	
	function control($attr, $path, $data, $volume) {
    	if(!isset($this->settings['fm-show-hidden-files']) || empty($this->settings['fm-show-hidden-files']))
    		return strpos(basename($path), '.') === 0    
		        ? !($attr == 'read' || $attr == 'write')  
		        :  null;                                    
	}
	
}

endif;