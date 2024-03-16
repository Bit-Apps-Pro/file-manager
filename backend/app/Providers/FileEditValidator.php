<?php

namespace BitApps\FM\Providers;

use BitApps\WPKit\Utils\Capabilities;
use BitApps\FM\Exception\PreCommandException;
use BitApps\FM\Plugin;

\defined('ABSPATH') or exit();
class FileEditValidator
{
    public function validate($cmd, &$args, $elfinder, $volume)
    {
        try {
            $this->checkPermission();
        } catch (PreCommandException $th) {
            return $th->getError();
        }

        $args['content'] = stripcslashes($args['content']); // Default wordpress slashing removed.

        // Checking syntax for PHP file.
        if (strpos($args['content'], '<?php') !== false) {
            try {
                $this->checkSyntax($args['content']);
            } catch (PreCommandException $th) {
                return $th->getError();
            }
        }
    }

    public function checkSyntax($content)
    {
        $error = '';

        if (!\function_exists('exec')) {
            $error = __('exec() is required for php syntax check');
        } else {
            $tempFilePath   = FM_UPLOAD_BASE_DIR . 'temp.php';
            $fp             = fopen($tempFilePath, 'w+');
            fwrite($fp, $content);
            fclose($fp);
            exec('php -l ' . escapeshellarg($tempFilePath), $output, $return);

            $errorMessages = [];
            foreach ($output as $result) {
                if (
                    strpos($result, 'No syntax errors detected') !== false
                || $result == ''
                ) {
                    continue;
                }

                if (strpos($result, 'Errors parsing') !== false) {
                    $error = wp_sprintf(
                        // translators: 1: Temporary file path
                        __('Errors parsing the file [ %s ] as php script', 'file-manager'),
                        str_replace('temp', '', $tempFilePath)
                    );
                } else {
                    $errorMessages[] = $result;
                }
            }

            unlink($tempFilePath);

            if ($return !== 0 && !empty($errorMessages)) {
                $error = !\is_string($errorMessages[0]) ? json_encode($errorMessages[0]) : $errorMessages[0];
            }
        }

        if (\defined('BFM_DISABLE_SYNTAX_CHECK') && BFM_DISABLE_SYNTAX_CHECK) {
            return;
        }

        if (!empty($error) && !Capabilities::check('install_plugins')) {
            throw new PreCommandException(esc_html($error));
        }
    }

    private function checkPermission()
    {
        $error = '';
        if (\defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) {
            $error = __('File edit is disabled. To allow edit, please set DISALLOW_FILE_EDIT to false in wp-config file', 'file-manager');
        }

        if (\is_null($error) && !Plugin::instance()->permissions()->currentUserCanRun('edit')) {
            $error = __('Not Authorized to edit file', 'file-manager');
        }

        if (!empty($error)) {
            throw new PreCommandException(esc_html($error));
        }
    }
}
