<?php
/**
 * @license MIT
 *
 * Modified using Strauss.
 *
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Http\Rules;

use BitApps\FM\Dependencies\BitApps\WPValidator\Rule;
use BitApps\FM\Plugin;

class ValidateRolesRule extends Rule
{
    public function validate($value)
    {
        $roles = Plugin::instance()->permissions()->allRoles();

        if (!\is_array($value)) {
            return false;
        }

        $visited = [];
        foreach ($roles as $role) {
            $visited[$role] = 1;
        }

        foreach ($value as $roleId => $permissions) {
            if (!isset($visited[$roleId])) {
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
