<?php
// Security check 
defined('ABSPATH') || die();

if(!class_exists('FMPHPSyntaxChecker')):

class FMPHPSyntaxChecker{

	public function checkSyntax($cmd, &$args, $elfinder, $volume){
		$args['content'] = stripcslashes($args['content']); // Default wordpress slashing removed.

		// Checking syntax for PHP file.
		if(strpos($args['content'], '<?php') !== false){
			$temp_file_path = FM_UPLOAD_BASE_DIR . 'temp.php';
			$fp = fopen($temp_file_path, "w+");
			fwrite($fp, $args['content']);
			fclose($fp);
			exec("php -l " . $temp_file_path , $output, $return);

			
			$error_message = [];
			foreach($output as $result){
				if(strpos($result , 'No syntax errors detected') !== false){
					continue;
				}elseif($result == ''){
					continue;
				}

				if(strpos($result, 'Errors parsing' ) !== false){
				}else{
					$error_message[] = $result;
					error_log(print_r($result, true));
				}
			}

			unlink($temp_file_path);

			if($return !== 0) return array(
				'preventexec' => true,
				'results' => array(
					'error' => array($error_message),
				),
			);
		}
		
		return true;
	}

}

endif;