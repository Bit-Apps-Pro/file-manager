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

class BetweenRule extends Rule
{
    use Helpers;

    protected $message = "The :attribute must be between :min and :max";

    protected $requireParameters = ['min', 'max'];

    public function validate($value)
    {
        $this->checkRequiredParameter($this->requireParameters);

        $min = (int) $this->getParameter('min');
        $max = (int) $this->getParameter('max');

        $length = $this->getValueLength($value);

        if ($length) {
            return $length >= $min && $length <= $max;
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
