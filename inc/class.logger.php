<?php
// Handles all the logging
if(!class_exists('FMLogger')):
class FMLogger{
	private $table_name;

	function __construct(){
		global $wpdb;
		$this->table_name = $wpdb->table_prefix . "fm_log"; 
	}

	function insert($operation, $file_path){
		global $wpdb;
		$wpdb->insert($this->table_name, array(
			'user_id' => get_current_user_id(),
			'operation_id' => $operation,
			'file_path' => $file_path,
			'time' => current_time('mysql'),
		));
	}
}
endif;