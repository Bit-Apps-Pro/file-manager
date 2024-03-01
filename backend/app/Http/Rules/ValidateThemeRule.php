<?php

namespace BitApps\FM\Http\Rules;

use BitApps\FM\Dependencies\BitApps\WPValidator\Rule;
use BitApps\FM\Plugin;

class ValidateThemeRule extends Rule
{
    public function validate($value)
    {
        $themes = Plugin::instance()->preferences()->themes();

        return isset($themes[$value]) ? true : false;
    }

    public function message()
    {
        return __('Theme variant is not valid', 'file-manager');
    }
}
