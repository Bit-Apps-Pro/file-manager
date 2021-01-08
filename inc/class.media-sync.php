<?php 
/**
 * 
 * Synchronizing media file when a media file is uploaded. Media Files: Image, Audio, Video, Archive
 * @since 5.1
 * @package FileManager
 * 
 * */
// Security check
defined('ABSPATH') || die();

if( !class_exists('FMMediaSync') ):

// Sync current media library uploads to media library.
class FMMediaSync {

	public $wp_upload_directory;

	function __construct(){

		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$this->wp_upload_directory = wp_upload_dir();
		$this->wp_upload_directory = $this->wp_upload_directory['path'];

	}

	// Triggers when a file is uploaded and initiates the uploading process for single or batch files.
	public function onFileUpload($cmd, &$result, $args, $elfinder, $volume){
		$images = array();
		$I = 0;
		$fp = fopen('fmm.txt', 'w+');
		
		$upload_target_path = $volume->getPath($args['target']);

		if( strpos( $upload_target_path, $this->wp_upload_directory ) !== false ){
			
			$images = array();

			for($I = 0; $I < count($args['FILES']['upload']['name']); $I++){
				
				$images[] = array(
					'name' => $args['FILES']['upload']['name'][$I],
					'type' => wp_check_filetype( $args['FILES']['upload']['name'][$I], null ),
					'path' => trailingslashit($upload_target_path) . $args['FILES']['upload']['name'][$I],
					'url' => $this->abs_path_to_url( trailingslashit($upload_target_path) . $args['FILES']['upload']['name'][$I] ),
				);

			}

			$this->add_media($images);

		}
		
	}
	
	private function add_media($images){
		foreach ($images as $image){
			$attachment = array(
				'post_mime_type' => $image['type']['type'],
				'post_title' => sanitize_file_name( $image['name'] ),
			);
			$attachment_id = wp_insert_attachment( $attachment, $image['path'] );
			$attach_data = wp_generate_attachment_metadata( $attachment_id, $image['path'] );
			wp_update_attachment_metadata( $attachment_id, $attach_data );
		}
	}

	private function abs_path_to_url( $path = '' ) {
	    $url = str_replace(
	        wp_normalize_path( untrailingslashit( ABSPATH ) ),
	        site_url(),
	        wp_normalize_path( $path )
	    );
	    return esc_url_raw( $url );
	}

}
endif;