<?php

namespace BitApps\FM\Http\Rules;

use BitApps\WPValidator\Rule;
use BitApps\FM\Plugin;

class ValidateCommandsRule extends Rule
{
    public function validate($value)
    {
        $commands = Plugin::instance()->permissions()->allCommands();

        if (!\is_array($value)) {
            return false;
        }

        $visited = [];
        foreach ($commands as $command) {
            $visited[$command] = 1;
        }

        foreach ($value as $key => $command) {
            if (!isset($visited[$command])) {
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return __('Folder Path Must be within WordPress root directory', 'file-manager');
    }
}
