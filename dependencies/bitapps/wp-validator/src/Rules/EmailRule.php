<?php
/**
 * @license MIT
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BitApps\FM\Dependencies\BitApps\WPValidator\Rules;

use BitApps\FM\Dependencies\BitApps\WPValidator\Rule;

class EmailRule extends Rule
{
    private $message = "The :attribute must be a valid email address";

    public function validate($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function message()
    {
        return $this->message;
    }
}
