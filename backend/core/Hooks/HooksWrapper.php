<?php

namespace BitApps\FM\Core\Hooks;

/**
 * A wrapper class for actions and filters.
 */
final class HooksWrapper
{
    /**
     * A wrapper for do_action().
     *
     * @param string $tag the name of the action to be executed
     * @param mixed  $arg Optional. Additional arguments which
     *                    are passed on to the functions hooked to the action. Default empty.
     *
     * @return mixed
     */
    public function doAction($tag, ...$arg)
    {
        return do_action($tag, ...$arg);
    }

    /**
     * A wrapper for add_action().
     *
     * @param string   $tag           the name of the action to which the $functionToAdd is hooked
     * @param callable $functionToAdd the name of the function you wish to be called
     * @param int      $priority      Optional. Used to specify the order in which the functions
     *                                associated with a particular action are executed.
     *                                Default 10. Lower numbers correspond with earlier execution,
     *                                and functions with the same priority
     *                                are executed in the order in which they were added to the action.
     * @param int      $acceptedArgs  Optional. The number of arguments the function accepts. Default 1.
     *
     * @return true — Will always return true
     */
    public function addAction($tag, $functionToAdd, $priority = 10, $acceptedArgs = 1)
    {
        return add_action(
            $tag,
            $functionToAdd,
            $priority,
            $acceptedArgs
        );
    }

    /**
     * A wrapper for remove_action().
     *
     * @param string   $tag              the action hook to which the function to be removed is hooked
     * @param callable $functionToRemove the name of the function which should be removed
     * @param int      $priority         Optional. The priority of the function. Default 10.
     *
     * @return bool — Whether the function is removed
     */
    public function removeAction($tag, $functionToRemove, $priority = 10)
    {
        return remove_action($tag, $functionToRemove, $priority);
    }

    /**
     * A wrapper for add_filter().
     *
     * @param string   $tag           the name of the filter to hook the $functionToAdd callback to
     * @param callable $functionToAdd the callback to be run when the filter is applied
     * @param int      $priority      Optional. Used to specify the order in which the functions
     *                                associated with a particular action are executed.
     *                                Lower numbers correspond with earlier execution,
     *                                and functions with the same priority are executed
     *                                in the order in which they were added to the action. Default 10.
     * @param int      $acceptedArgs  Optional. The number of arguments the function accepts. Default 1.
     *
     * @return true
     */
    public function addFilter($tag, $functionToAdd, $priority = 10, $acceptedArgs = 1)
    {
        return add_filter($tag, $functionToAdd, $priority, $acceptedArgs);
    }

    /**
     * A wrapper for apply_filters().
     *
     * @param string $tag     the name of the filter hook
     * @param mixed  $value   the default value to filter
     * @param mixed  ...$args Additional parameters to pass to the callback functions.
     *
     * @return mixed the filtered value after all hooked functions are applied to it
     */
    public function applyFilter($tag, $value, ...$args)
    {
        return apply_filters($tag, $value, ...$args);
    }
}
