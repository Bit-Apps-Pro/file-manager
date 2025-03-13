<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Exception\PreCommandException;
use BitApps\FM\Plugin;
use BitApps\FM\Vendor\BitApps\WPKit\Utils\Capabilities;

\defined('ABSPATH') or exit();
class FileEditValidator
{
    public function validate($cmd, &$args, $elfinder, $volume)
    {
        try {
            $this->checkPermission();
            Plugin::instance()->accessControl()->scanFile($cmd, $args);
        } catch (PreCommandException $th) {
            return $th->getError();
        }

        $args['content'] = stripcslashes($args['content']); // Default wordpress slashing removed.

        // Checking syntax for PHP file.
        if (strpos($args['content'], '<?php') !== false) {
            try {
                $this->checkSyntax($args['content'], wp_basename($volume->getPath($args['target'])));
            } catch (PreCommandException $th) {
                return $th->getError();
            }
        }
    }

    public function checkSyntax($content, $fileName)
    {
        $error = '';

        if (
            (!\function_exists('exec')  && Capabilities::check('install_plugins'))
            || (\defined('BFM_DISABLE_SYNTAX_CHECK') && BFM_DISABLE_SYNTAX_CHECK)
            ) {
            return;
        } else if (!\function_exists('exec')) {
            $error = __('exec() is required for php syntax check');
        } else {
            $fp           = tmpfile();
            $metaData     = stream_get_meta_data($fp);
            $tempFilePath = $metaData['uri'];
            fwrite($fp, $content);
            exec('php -l ' . escapeshellarg($tempFilePath), $output, $return);
            fclose($fp);
            
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
                        $fileName
                    );
                } else {
                    $errorMessages[] = $result;
                }
            }

            if ($return !== 0 && !empty($errorMessages)) {
                $error = !\is_string($errorMessages[0]) ? json_encode($errorMessages[0]) : $errorMessages[0];
            }
        }

        if (!empty($error)) {
            throw new PreCommandException(esc_html($error));
        }
    }

    private function checkPermission()
    {
        $error = '';
        if (\defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) {
            $error = __('File edit is disabled. To allow edit, please set DISALLOW_FILE_EDIT to false in wp-config file', 'file-manager');
        }

        if (empty($error) && !Plugin::instance()->permissions()->currentUserCanRun('edit')) {
            $error = __('Not Authorized to edit file', 'file-manager');
        }

        if (!empty($error)) {
            throw new PreCommandException(esc_html($error));
        }
    }
}
