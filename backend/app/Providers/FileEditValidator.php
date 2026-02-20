<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Exception\PreCommandException;
use BitApps\FM\Plugin;
use BitApps\FM\Vendor\BitApps\WPKit\Utils\Capabilities;
use ParseError;

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
            (Capabilities::check('install_plugins'))
            || (\defined('BFM_DISABLE_SYNTAX_CHECK') && BFM_DISABLE_SYNTAX_CHECK)
        ) {
            return;
        }

        try {
            token_get_all($content);
        } catch (ParseError $e) {
            $error = wp_sprintf(
                // translators: 1: file name, 2: PHP parse error message
                __('Syntax error in file: %1$s. Error: %2$s', 'file-manager'),
                $fileName,
                $e->getMessage()
            );
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
