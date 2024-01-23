<?php
/**
 * @license MIT
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Dependencies\BitApps\WPValidator\Rules;

use BitApps\FM\Dependencies\BitApps\WPValidator\Rule;

class AcceptedRule extends Rule
{
    private $message = "The :attribute must be accepted";

    public function validate($value)
    {
        $accepted = ['yes', 'on', '1', 1, true, 'true'];
        return in_array($value, $accepted, true);
    }

    public function message()
    {
        return $this->message;
    }
}
