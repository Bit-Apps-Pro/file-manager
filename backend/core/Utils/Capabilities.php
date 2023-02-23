<?php

namespace BitApps\FM\Core\Utils;

use BitApps\FM\Core\Hooks\Hooks;

final class Capabilities
{
    public static function check($cap, ...$args)
    {
        return current_user_can($cap, ...$args);
    }

    public static function filter($cap, $default = 'manage_options')
    {
        return static::check(Hooks::apply($cap, $default));
    }
}
