<?php
// Security check
defined('ABSPATH') || die();

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

function gb_fm_activate(){

  // Initilizing the option to store logging
  if( !get_option('fm_log', false) ){
    add_option('fm_log', '');
  }

  // Creating necessary folders for file manager
  $upload_dir = wp_upload_dir();
  $index = $upload_dir['basedir'] . DS . 'file-manager' . DS . 'index.html';
  wp_mkdir_p(dirname($index));

  // Creating indexfile
  if(!file_exists($index)){
    $fp = fopen($index, 'a');
    fwrite($fp, " ");
    fclose($fp);
  }

  /* ------------------------------ Initilizing Statistical Data ------------------------------ */
  $statistics = array(
    'start-time' => time(),
    'review' => array(
      'initial-popup' => time() + 7 * 24 * 60 * 60,
      'popup-interval' => 2 * 24 * 60 * 60,
      'most-recent-popup' => 0, // Last when the popup was triggered.
      'current-status' => 0, // 1 = initial-popup, 2 = remind-me-later, 3 = already-provided-feedback, 4 = don't show this message
    ),
  );
  /* ------------------------------ Initilizing Statistical Data ENDS ------------------------- */


    // Logger table
    global $wpdb;
    $table_prefix = $wpdb->prefix;
    $SQL = "CREATE TABLE {$table_prefix}fm_log (id int(11) NOT NULL, user_id int(11) NOT NULL, operation_id varchar(32) NOT NULL, file_path varchar(1024) NOT NULL, time datetime NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;ALTER TABLE {$table_prefix}fm_log ADD PRIMARY KEY (id);ALTER TABLE {$table_prefix}fm_log MODIFY id int(11) NOT NULL AUTO_INCREMENT;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $SQL );
}
