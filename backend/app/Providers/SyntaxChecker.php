<?php

namespace BitApps\FM\Providers;

\defined('ABSPATH') or exit();
class SyntaxChecker
{
    public function checkSyntax($cmd, &$args, $elfinder, $volume)
    {
        $args['content'] = stripcslashes($args['content']); // Default wordpress slashing removed.

        // Checking syntax for PHP file.
        if (strpos($args['content'], '<?php') !== false) {
            $tempFilePath   = FM_UPLOAD_BASE_DIR . 'temp.php';
            $fp             = fopen($tempFilePath, 'w+');
            fwrite($fp, $args['content']);
            fclose($fp);
            exec('php -l ' . $tempFilePath, $output, $return);

            $errorMessage = [];
            foreach ($output as $result) {
                if (strpos($result, 'No syntax errors detected') !== false) {
                    continue;
                } elseif ($result == '') {
                    continue;
                }

                if (strpos($result, 'Errors parsing') !== false) {
                } else {
                    $errorMessage[] = $result;
                }
            }

            unlink($tempFilePath);

            if ($return !== 0) {
                return [
                    'preventexec' => true,
                    'results'     => [
                        'error' => [$errorMessage],
                    ],
                ];
            }
        }

        return true;
    }
}
