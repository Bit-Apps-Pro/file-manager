<?php
/**
 * @license MIT
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BitApps\FM\Http\Rules;

use BitApps\FM\Dependencies\BitApps\WPValidator\Rule;
use BitApps\FM\Plugin;

class ValidPathRule extends Rule
{

    public function validate($value)
    {
        $path = Plugin::instance()->preferences()->realPath($value);

        return strpos($path, trim(ABSPATH, '/\\')) !== false;
    }

    public function message()
    {
        return __('Folder Path Must be within WordPress root directory', 'file-manager');
    }
}
