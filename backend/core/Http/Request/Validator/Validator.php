<?php

namespace BitApps\FM\Core\Http\Request\Validator;

trait Validator
{
    use ValidateAttributes;

    private $_errors = [];

    public function validate($rulesForAllAttribute = null, $messages = null)
    {
        if ($rulesForAllAttribute === null && method_exists($this, 'rules')) {
            $rulesForAllAttribute = $this->rules();
        }

        if ($rulesForAllAttribute === null || empty($rulesForAllAttribute)) {
            return true;
        }

        if ($messages === null && method_exists($this, 'messages')) {
            $messages = $this->messages();
        }

        foreach ($rulesForAllAttribute as $attribute => $rules) {
            $this->parseAllRuleForAttribute($attribute, $rules, $messages);
        }

        return $this->errors();
    }

    public function parseAllRuleForAttribute($attribute, $rules, $messages)
    {
        foreach ($rules as $id => $ruleStr) {
            $ruleParams = [];
            $attrErrId  = isset($this->_errors[$attribute])
                && is_countable($this->_errors[$attribute]) ? \count($this->_errors[$attribute]) : 0;
            if (\is_string($ruleStr)) {
                $ruleArr = explode(':', $ruleStr);
                $rule    = $ruleArr[0];
                if (\count($ruleArr) > 1) {
                    $ruleParams = explode(',', $ruleArr[1]);
                }
            } else {
                $rule = $ruleStr;
            }

            if (\is_string($rule)) {
                $ruleName = str_replace('_', '', ucwords($rule, '_'));
                if (
                    method_exists($this, 'validate' . ucfirst($ruleName))
                    && !($message
                        = $this->{'validate' . ucfirst($ruleName)}($attribute, $this->get($attribute), ...$ruleParams)
                    )
                ) {
                    $this->_errors[$attribute][$attrErrId] = $message;
                }
            } elseif (is_subclass_of($rule, Rule::class)) {
                $ruleObj = \is_object($rule) ? $rule : new $rule();
                $rule    = \get_class($rule);
                if (!$ruleObj->passes($attribute, $this->{$attribute})) {
                    $this->_errors[$attribute][$attrErrId] = $ruleObj->message();
                }
            }

            if (isset($this->_errors[$attribute][$attrErrId])) {
                $errorMessage = $this->_errors[$attribute][$attrErrId];
                if (isset($messages[$attribute]) && \is_string($messages[$attribute])) {
                    $errorMessage = $messages[$attribute];
                } elseif (isset($messages[$attribute][$rule])) {
                    $errorMessage = $messages[$attribute][$rule];
                } elseif (\is_bool($this->_errors[$attribute][$attrErrId])) {
                    $errorMessage = sprintf('%s must be validated with %s rule', $attribute, (string) $rule);
                }

                $this->_errors[$attribute][$attrErrId] = str_replace(':attribute', $attribute, $errorMessage);
            }
        }
    }

    public function errors()
    {
        return $this->_errors;
    }

    public function validated()
    {
        return empty($this->_errors) ? true : false;
    }
}
