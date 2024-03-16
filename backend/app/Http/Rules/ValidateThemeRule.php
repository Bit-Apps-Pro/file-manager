<?php

namespace BitApps\FM\Http\Rules;

use BitApps\WPValidator\Rule;
use BitApps\FM\Plugin;

class ValidateThemeRule extends Rule
{
    public function validate($value)
    {
        $themes            = Plugin::instance()->preferences()->themes();
        $themes['default'] = 'default';

        return isset($themes[$value]) ? true : false;
    }

    public function message()
    {
        return __('Theme variant is not valid', 'file-manager');
    }
}
