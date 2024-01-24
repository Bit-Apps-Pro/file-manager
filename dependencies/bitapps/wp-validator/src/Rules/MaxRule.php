<?php
/**
 * @license MIT
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BitApps\FM\Dependencies\BitApps\WPValidator\Rules;

use BitApps\FM\Dependencies\BitApps\WPValidator\Helpers;
use BitApps\FM\Dependencies\BitApps\WPValidator\Rule;

class MaxRule extends Rule
{
    use Helpers;

    protected $message = "The :attribute may not be greater than :max characters";

    protected $requireParameters = ['max'];

    public function validate($value)
    {
        $this->checkRequiredParameter($this->requireParameters);

        $max = (int) $this->getParameter('max');

        $length = $this->getValueLength($value);

        if ($length) {
            return $length <= $max;
        }

        return false;

    }

    public function getParamKeys()
    {
        return $this->requireParameters;
    }

    public function message()
    {
        return $this->message;
    }
}
