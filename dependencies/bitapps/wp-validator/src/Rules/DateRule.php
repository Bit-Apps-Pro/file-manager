<?php
/**
 * @license MIT
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BitApps\FM\Dependencies\BitApps\WPValidator\Rules;

use BitApps\FM\Dependencies\BitApps\WPValidator\Rule;

class DateRule extends Rule
{
    private $message = "The :attribute is not a valid date";

    public function validate($value)
    {
        return strtotime($value) !== false;
    }

    public function message()
    {
        return $this->message;
    }
}
