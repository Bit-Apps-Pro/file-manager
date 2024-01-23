<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Dependencies\BitApps\WPKit\Hooks;

use RuntimeException;

/**
 * A forwarder class for actions and filters.
 *
 * @method static HooksWrapper doAction($tag, ...$arg)
 * @method static HooksWrapper addAction($tag,callable $functionToAdd, $priority = 10, $acceptedArgs = 1)
 * @method static HooksWrapper removeAction($tag,callable $functionToRemove, $priority = 10)
 * @method static HooksWrapper addFilter($tag,callable $functionToAdd, $priority = 10, $acceptedArgs = 1)
 * @method static HooksWrapper applyFilter($tag, $value, ...$args)
 */
final class Hooks
{
    private static $_hook;

    public function __construct()
    {
        if (!isset(self::$_hook)) {
            self::$_hook = new HooksWrapper();
        }
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->getInstance(), $method)) {
            return \call_user_func_array([$this->getInstance(), $method], $parameters);
        }

        throw new RuntimeException('Undefined method [' . $method . '] called on Model class.');
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static())->{$method}(...$parameters);
    }

    public function getInstance()
    {
        return self::$_hook;
    }
}
