<?php
/**
 * @license MIT
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BitApps\FM\Dependencies\BitApps\WPValidator\Rules;

use BitApps\FM\Dependencies\BitApps\WPValidator\Rule;

class LowercaseRule extends Rule
{
    private $message = "The :attribute must be in lowercase";

    public function validate($value)
    {
        return $value === strtolower($value);
    }

    public function message()
    {
        return $this->message;
    }
}
