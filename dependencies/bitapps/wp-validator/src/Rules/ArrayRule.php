<?php
/**
 * @license MIT
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BitApps\FM\Dependencies\BitApps\WPValidator\Rules;

use BitApps\FM\Dependencies\BitApps\WPValidator\Rule;

class ArrayRule extends Rule
{
    private $message = "The :attribute must be array";

    public function validate($value)
    {
        return is_array($value);
    }

    public function message()
    {
        return $this->message;
    }
}
