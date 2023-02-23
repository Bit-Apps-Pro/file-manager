<?php

namespace BitApps\FM\Core\Shortcode;

use RuntimeException;

/**
 * A forwarder class for actions and filters.
 *
 * @method static void doShortcode( $content, $ignoreHtml = false )
 * @method static void addShortcode($tag, $callback)
 * @method static void removeShortcode($tag)
 * @method static bool shortcodeExists($tag)
 * @method static bool hasShortcode($content, $tag)
 */
final class Shortcode
{
    private static $_wrapper;

    public function __construct()
    {
        if (!isset(self::$_wrapper)) {
            self::$_wrapper = new ShortcodeWrapper();
        }
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->getInstance(), $method)) {
            return \call_user_func_array([$this->getInstance(), $method], $parameters);
        }

        throw new RuntimeException('Undefined method [' . $method . '] called on ' . __CLASS__ . ' class.');
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static())->{$method}(...$parameters);
    }

    public function getInstance()
    {
        return self::$_wrapper;
    }
}
