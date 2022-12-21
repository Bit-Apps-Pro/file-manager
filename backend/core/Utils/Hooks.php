<?php

namespace BitApps\FM\Core\Utils;

/**
 * A wrapper class for actions and filters.
 */
final class Hooks
{
    /**
     * A wrapper for do_action()
     *
     * @param string $tag    The name of the action to be executed.
     * @param mixed  ...$arg Optional. Additional arguments which are passed on to the functions hooked to the action. Default empty.
     *
     * @return mixed
     */
    public static function do($tag, ...$arg)
    {
        return do_action($tag, ...$arg);
    }

    /**
     * A wrapper for add_action()
     *
     * @param string   $tag             The name of the action to which the $function_to_add is hooked.
     * @param callable $function_to_add The name of the function you wish to be called.
     * @param int      $priority        Optional. Used to specify the order in which the functions associated with a particular action are executed. Default 10. Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
     * @param int      $accepted_args   Optional. The number of arguments the function accepts. Default 1.
     * 
     * @return true — Will always return true.
     */
    public static function add($tag, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        return add_action(
            $tag,
            $function_to_add,
            $priority,
            $accepted_args
        );
    }

    /**
     * A wrapper for remove_action()
     *
     * @param string   $tag                The action hook to which the function to be removed is hooked.
     * @param callable $function_to_remove The name of the function which should be removed.
     * @param int      $priority           Optional. The priority of the function. Default 10.
     * 
     * @return bool — Whether the function is removed.
     */
    public static function remove($tag, $function_to_remove, $priority = 10)
    {
        return remove_action($tag, $function_to_remove, $priority);
    }

    /**
     * A wrapper for add_filter()
     *
     * @param string   $tag             The name of the filter to hook the $function_to_add callback to.
     * @param callable $function_to_add The callback to be run when the filter is applied.
     * @param int      $priority        Optional. Used to specify the order in which the functions associated with a particular action are executed. Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action. Default 10.
     * @param int      $accepted_args   Optional. The number of arguments the function accepts. Default 1.
     * 
     * @return true
     */
    public static function filter($tag, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        return add_filter($tag, $function_to_add, $priority, $accepted_args);
    }

    /**
     * A wrapper for apply_filters()
     *
     * @param string $tag     The name of the filter hook.
     * @param mixed  $value   The default value to filter.
     * @param mixed  ...$args Additional parameters to pass to the callback functions.
     * 
     * @return mixed  The filtered value after all hooked functions are applied to it.
     */
    public static function apply($tag, $value, ...$args)
    {
        return apply_filters($tag, $value, ...$args);
    }
}
