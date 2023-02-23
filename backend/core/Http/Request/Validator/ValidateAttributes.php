<?php

namespace BitApps\FM\Core\Http\Request\Validator;

use Exception;

trait ValidateAttributes
{
    public function validateRequired($attribute)
    {
        return $this->has($attribute);
    }

    public function validateNumeric($attribute, $value)
    {
        return is_numeric($value);
    }

    /**
     * Validate that an attribute is an integer.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function validateInteger($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validate that an attribute is a valid IP.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function validateIp($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Validate that an attribute is a valid IPv4.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function validateIpv4($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    /**
     * Validate that an attribute is a valid IPv6.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function validateIpv6($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    /**
     * Validate that an attribute is a valid MAC address.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function validateMacAddress($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_MAC) !== false;
    }

    /**
     * Validate the attribute is a valid JSON string.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function validateJson($attribute, $value)
    {
        if (\is_array($value)) {
            return false;
        }

        if (!\is_scalar($value) && !\is_null($value) && !method_exists($value, '__toString')) {
            return false;
        }

        json_decode($value);

        return json_last_error() === JSON_ERROR_NONE;
    }

    public function validateEmail($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public function validateUrl($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_URL);
    }

    public function validateString($attribute, $value)
    {
        return \is_string($value);
    }

    public function validateDate($attribute, $value)
    {
        if ($value instanceof DateTimeInterface) {
            return true;
        }

        try {
            if ((!\is_string($value) && !is_numeric($value)) || strtotime($value) === false) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        $date = date_parse($value);

        return checkdate($date['month'], $date['day'], $date['year']);
    }

    public function validateMin($attribute, $value, $valueToCompare)
    {
        return is_numeric($value) && $value >= $valueToCompare;
    }

    public function validateMax($attribute, $value, $valueToCompare)
    {
        return is_numeric($value) && $value <= $valueToCompare;
    }
}
