<?php

namespace BitApps\FM\Http\Rules;

use BitApps\WPValidator\Rule;
use BitApps\FM\Plugin;

class ValidateLangRule extends Rule
{
    public function validate($value)
    {
        $languages = Plugin::instance()->preferences()->availableLanguages();

        return isset($languages[$value]) ? true : false;
    }

    public function message()
    {
        return __('Language code is not valid', 'file-manager');
    }
}
