<?php
// Security check
defined('ABSPATH') || die();

// This class migrates from the older version to the newer version
class FMMigrate{

  private $version;

  public function __construct($current_version){

    // Checkks the current version
    // update_option('fm_current_version', 501);
    $this->version = get_option('fm_current_version', 501);
    if((int)($this->version) < (int)($current_version)){
      for($I = (int)($this->version) + 1; $I <= $current_version; $I++ ){
        $function = 'migrate_to_' . $I;
        if( method_exists($this, $function) ) $this->$function();
        update_option('fm_current_version', $I);
      }
    }

  }

    protected function migrate_to_502(){
        $upload_dir = wp_upload_dir();
        $log_file = $upload_dir['basedir'] . DS . 'file-manager' . DS . 'log.txt';
        if(file_exists($log_file)) unlink($log_file);
    }

    protected function migrate_to_510(){

    }

}
