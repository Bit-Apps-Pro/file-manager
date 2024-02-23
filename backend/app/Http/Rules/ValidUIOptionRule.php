<?php
/**
 * @license MIT
 *
 * Modified using Strauss.
 *
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Http\Rules;

use BitApps\FM\Dependencies\BitApps\WPValidator\Rule;

class ValidUIOptionRule extends Rule
{
    public function validate($value)
    {
        foreach ($value as $option) {
            if (! \in_array($option, ['toolbar','places','tree','path','stat'])) {
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return __('UI option must be valid', 'file-manager');
    }
}
