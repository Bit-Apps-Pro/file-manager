<?php

namespace BitApps\FM\Core\Http\Request\Validator;

interface Rule
{
    /**
     * Checks if value passes the rule
     *
     * @param string $attribute
     * @param string $value
     *
     * @return bool
     */
    public function passes($attribute, $value);

    /**
     * Returns message when validation failed for the rule
     *
     * @return string
     */
    public function message();
}
