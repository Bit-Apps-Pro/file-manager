<?php
// Security check 
defined('ABSPATH') || die();

if(!class_exists('FMPHPSyntaxChecker')):

class FMPHPSyntaxChecker{

	public function checkSyntax($cmd, &$args, $elfinder, $volume){
		$args['content'] = stripcslashes($args['content']); // Default wordpress slashing removed.

		// Checking syntax for PHP file.
		if(strpos($args, '<?php') !== false){
			$temp_file_path = __DIR__ . DIRECTORY_SEPARATOR . 'temp.php';
			$fp = fopen($temp_file_path, "w+");
			fwrite($fp, $args['content']);
			fclose($fp);
			exec("php -l " . $temp_file_path , $output, $return);
			if(strpos($output[0], 'Errors parsing' ) !== false) $error_message = __("Syntax Error found. Please check your code for syntax error.",'file-manager');
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