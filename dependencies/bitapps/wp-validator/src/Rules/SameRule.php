<?php
/**
 * @license MIT
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BitApps\FM\Dependencies\BitApps\WPValidator\Rules;

use BitApps\FM\Dependencies\BitApps\WPValidator\Rule;

class SameRule extends Rule
{
    private $message = "The :attribute and :other must match";

    protected $requireParameters = ['other'];

    public function validate($value)
    {
        $this->checkRequiredParameter($this->requireParameters);

        $otherValue = $this->getInputDataContainer()->getAttributeValue($this->getParameter('other'));

        return $value === $otherValue;
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
