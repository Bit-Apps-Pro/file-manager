<?php
/**
 * @license MIT
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BitApps\FM\Dependencies\BitApps\WPValidator\Rules;

use BitApps\FM\Dependencies\BitApps\WPValidator\Rule;

class UppercaseRule extends Rule
{
    private $message = "The :attribute must be in uppercase";

    public function validate($value)
    {
        return $value === strtoupper($value);
    }

    public function message()
    {
        return $this->message;
    }
}
