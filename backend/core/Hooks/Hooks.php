<?php

namespace BitApps\FM\Core\Hooks;

use RuntimeException;

/**
 * A forwarder class for actions and filters.
 *
 * @method static HooksWrapper doAction(string $tag, ...$arg)
 * @method static HooksWrapper addAction(string $tag,callable $functionToAdd,int $priority = 10, int $acceptedArgs = 1)
 * @method static HooksWrapper removeAction(string $tag,callable $functionToRemove,int $priority = 10)
 * @method static HooksWrapper addFilter(string $tag,callable $functionToAdd, int $priority = 10, int $acceptedArgs = 1)
 * @method static HooksWrapper applyFilter(string $tag, $value, ...$args)
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
