<?php
/**
 * @license MIT
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Dependencies\BitApps\WPValidator;

use InvalidArgumentException;

abstract class Rule
{
    private $inputDataContainer;

    private $params = [];

    private $paramKeys = [];

    private $roleName;

    abstract public function validate($value);

    abstract public function message();

    public function getInputDataContainer()
    {
        return $this->inputDataContainer;
    }

    public function setInputDataContainer($inputDataContainer)
    {
        $this->inputDataContainer = $inputDataContainer;
    }

    protected function checkRequiredParameter($params)
    {
        foreach ($params as $param) {
            if (!isset($this->params[$param])) {
                $ruleName = $this->getRuleName();
                $parameterCount = count($params);
                throw new InvalidArgumentException("Validation rule $ruleName requires at least $parameterCount parameters");
            }
        }
    }

    public function getParamKeys()
    {
        return $this->paramKeys;
    }

    public function setParameterValues($paramKeys, $paramValues)
    {
        if (count($paramKeys) === count($paramValues)) {
            $this->params = array_combine($paramKeys, $paramValues);
        }
    }

    public function getParameter($key)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }
        return null;
    }

    public function setRuleName($ruleName)
    {
        $this->roleName = $ruleName;
    }

    public function getRuleName()
    {
        return $this->roleName;
    }

    public function getParamValues()
    {
        return $this->params;
    }

}
