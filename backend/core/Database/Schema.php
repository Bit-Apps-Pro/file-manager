<?php

namespace BitApps\FM\Core\Database;

use Closure;
use ErrorException;
use RuntimeException;

class Schema
{
    public static function createBlueprint($schema, $method, Closure $callback = null)
    {
        return new Blueprint(
            $schema,
            $method,
            Connection::getPrefix(),
            $callback
        );
    }

    public static function build(Blueprint $blueprint)
    {
        return $blueprint->build();
    }

    public static function __callStatic($method, $parameters)
    {
        if (! method_exists(Blueprint::class, $method)) {
            throw new RuntimeException('Undefined method ['.$method.'] called on Schema class.');
        }
        if (is_null($parameters)) {
            throw new ErrorException('Expected at least 1 parameter, 0 given.');
        }
        if (count($parameters) > 1 && $parameters[1] instanceof Closure) {
            $blueprint = self::createBlueprint($parameters[0], $method, $parameters[1]);
            unset($parameters[0], $parameters[1]);
        } else {
            $blueprint = self::createBlueprint($parameters[0], $method);
            unset($parameters[0]);
        }
        call_user_func_array([$blueprint, $method], $parameters);

        return self::build($blueprint);
    }
}
