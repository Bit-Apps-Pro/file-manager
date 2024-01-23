<?php
/**
 * @license MIT
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Dependencies\BitApps\WPValidator;

use stdClass;

trait Helpers
{
    protected function isEmpty($val)
    {
        if (empty($val) && !in_array($val, ['0', 0, 0.0, false], true)) {
            return true;
        }

        return false;
    }

    protected function getValueLength($value)
    {
        if (is_int($value) || is_float($value)) {
            $value = $value;
        } elseif (is_string($value)) {
            $value = mb_strlen($value, 'UTF-8');
        } elseif (is_array($value)) {
            $value = count($value);
        } else {
            return false;
        }

        return $value;

    }

    // public function setNestedElement($data, $keys, $value)
    // {
    //     $reference = &$data;
    //     foreach ($keys as $key) {
    //         if (is_object($reference) && property_exists($reference, $key)) {
    //             $reference->$key = [];
    //             $reference = &$reference->{$key};
    //         } else {
    //             if (is_array($reference) && !array_key_exists($key, $reference)) {
    //                 $reference[$key] = [];
    //             }
    //             $reference = &$reference[$key];
    //         }

    //     }

    //     $reference = $value;
    //     unset($reference);

    //     return $data;
    // }

    public function setNestedElement(&$data, $keys, $value)
    {
        $current = &$data;

        foreach ($keys as $key) {
            if (is_array($current) && !isset($current[$key])) {
                $current[$key] = [];
            } elseif (is_object($current) && !isset($current->$key)) {
                $current->$key = new stdClass();
            }

            $current = &$current[$key] ?? $current->$key;
        }

        $current = $value;
        return $current;
    }

    public function getValueFromPath($keys, $data)
    {
        $counter = 0;
        while (count($keys) > $counter) {
            $path = $keys[$counter];
            if (is_object($data)) {
                $data = (array) $data;
            }
            if (isset($data[$path])) {
                $data = $data[$path];
            } else {
                return null;
            }
            $counter++;
        }
        return $data;
    }

    public function nestedArrayKeyExists($array, $keys)
    {

        foreach ($keys as $key) {
            if (\is_array($array) && \array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return false;
            }
        }

        return true;
    }
}
