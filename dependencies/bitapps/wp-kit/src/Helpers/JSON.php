<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Dependencies\BitApps\WPKit\Helpers;

final class JSON
{
    public static function encode($data, $options = 0, $depth = 512)
    {
        return wp_json_encode($data, $options, $depth);
    }

    public static function maybeEncode($data, $options = 0, $depth = 512)
    {
        if (\is_array($data) || \is_object($data)) {
            return self::encode($data, $options, $depth);
        }

        return $data;
    }

    /**
     * Decodes json string
     *
     * @param string    $json        the json string being decoded
     * @param null|bool $associative [optional] When TRUE, returned objects will be converted into associative arrays
     * @param int       $depth       [optional] User specified recursion depth
     * @param int       $flags       [optional] Bitmask of JSON decode options:
     *                               {@see JSON_BIGINT_AS_STRING} decodes large integers as their original string value.
     *                               {@see JSON_INVALID_UTF8_IGNORE} ignores invalid UTF-8 characters,
     *                               {@see JSON_INVALID_UTF8_SUBSTITUTE} converts invalid UTF-8 characters to \0xfffd,
     *                               {@see JSON_OBJECT_AS_ARRAY} decodes JSON objects as PHP array,
     *                               since 7.2.0 used by default if $assoc parameter is null,
     *                               {@see JSON_THROW_ON_ERROR} when passed this flag,
     *                               the error behaviour of these functions is changed.
     *                               The global error state is left untouched, and if an
     *                               error occurs that would otherwise set it, these functions instead throw a JsonException
     *
     * @return mixed the value encoded in json in appropriate PHP type. Values
     *               true, false and null (case-insensitive) are returned as TRUE, FALSE
     *               and NULL respectively. NULL is returned if the json cannot be decoded
     *               or if the encoded data is deeper than the recursion limit.
     */
    public static function decode($json, $associative = false, $depth = 512, $flags = 0)
    {
        return json_decode($json, $associative, $depth, $flags);
    }

    /**
     * If provided parma is string then will decode.
     *
     * @param string    $json        the json string being decoded
     * @param null|bool $associative [optional] When TRUE, returned objects will be converted into associative arrays
     * @param int       $depth       [optional] User specified recursion depth
     * @param int       $flags       [optional] Bitmask of JSON decode options:
     *                               {@see JSON_BIGINT_AS_STRING} decodes large integers as their original string value.
     *                               {@see JSON_INVALID_UTF8_IGNORE} ignores invalid UTF-8 characters,
     *                               {@see JSON_INVALID_UTF8_SUBSTITUTE} converts invalid UTF-8 characters to \0xfffd,
     *                               {@see JSON_OBJECT_AS_ARRAY} decodes JSON objects as PHP array,
     *                               since 7.2.0 used by default if $assoc parameter is null,
     *                               {@see JSON_THROW_ON_ERROR} when passed this flag,
     *                               the error behaviour of these functions is changed.
     *                               The global error state is left untouched,
     *                               and if an error occurs that would otherwise set it,
     *                               these functions instead throw a JsonException
     *
     * @return mixed the value encoded in json in appropriate PHP type.
     *               Values true, false and null (case-insensitive) are
     *               returned as TRUE, FALSE and NULL respectively.
     *               NULL is returned if the json cannot be decoded
     *               or if the encoded data is deeper than the recursion limit.
     */
    public static function maybeDecode($json, $associative = false, $depth = 512, $flags = 0)
    {
        if (!\is_string($json)) {
            return $json;
        }

        return self::decode($json, $associative, $depth, $flags);
    }

    public static function is($jsonString, $associative = false, $depth = 512, $flags = 0)
    {
        $decoded = json_decode($jsonString, $associative, $depth, $flags);

        if ($decoded !== null && json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return false;
    }
}
