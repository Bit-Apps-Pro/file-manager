<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Dependencies\BitApps\WPKit\Configs;

final class JsonConfig
{
    protected static $decodeAsArray = true;

    public static function setDecodeAsArray($value)
    {
        static::$decodeAsArray = $value;
    }

    public static function decodeAsArray()
    {
        return static::$decodeAsArray;
    }
}
