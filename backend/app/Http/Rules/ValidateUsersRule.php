<?php

namespace BitApps\FM\Http\Rules;

use BitApps\WPValidator\Rule;
use BitApps\FM\Plugin;

class ValidateUsersRule extends Rule
{
    public function validate($value)
    {
        $users = Plugin::instance()->permissions()->allUsers();

        if (!\is_array($value)) {
            return false;
        }

        foreach ($value as $usrId => $permissions) {
            if (!isset($users[$usrId])) {
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
