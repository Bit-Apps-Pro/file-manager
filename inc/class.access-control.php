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
		        ? ( '' == $this->settings['fm-create-hidden-files-folders']) ? !($attr == 'read' || $attr == 'write') :  ($attr == 'read' || $attr == 'write')
		        :  null;                                    
	}

	/**
	 * Create or upload .( Dot) started files or folder based on settings.
	 * @param $name file.
	 * @return boolean
	 * @reference : https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#acceptedName
	 */
	function accepted__name($name){

		if( 'fm-create-hidden-files-folders' == $this->settings['fm-create-hidden-files-folders'] ){
			return true;
		}

		return strpos($name, '.') !== 0;
	}
	
}

endif;