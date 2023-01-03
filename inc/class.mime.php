<?php 
/**
 * 
 * @file class.mime.php
 * @since 5.1.4
 * @description Manages the MIME types.
 * 
 * */

// Security Check 
defined('ABSPATH') || die();

if(!class_exists('FMMIME')):

class FMMIME{

	private $mime_file_path;

	function __construct($mime_file_path = null){
		
		if($mime_file_path) $this->mime_file_path = $mime_file_path;
			else $this->mime_file_path = plugin_dir_path(__FILE__) . '../elFinder/php/mime.types';

	}


	// Get MIME types
	function get_types(){

		$mime_list = array();
		// echo $this->mime_file_path;
		$fp = fopen($this->mime_file_path, 'r');
		if($fp){
			while(($line = fgets($fp)) !== false){
				if(strpos($line, '#') === 0) continue;
				$single_mime = explode('/', $line);
				$mime_type = trim($single_mime[0]);
				if(!in_array($mime_type, $mime_list)) $mime_list[] = $mime_type;
			}
		}
		
		return($mime_list);
	}
}

endif;