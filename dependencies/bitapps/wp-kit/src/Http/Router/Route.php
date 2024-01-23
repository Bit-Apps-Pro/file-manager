<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Dependencies\BitApps\WPKit\Http\Router;

/**
 * A forwarder class for RouteBase.
 *
 * @method static RouteBase     middleware()
 * @method static RouteBase     prefix($prefix)
 * @method static RouteBase     noAuth()
 * @method static RouteBase     isNoAuth()
 * @method static RouteBase     isTokenIgnored()
 * @method static RouteBase     ignoreToken()
 * @method static RouteBase     getMiddleware()
 * @method static RouteBase     getRoutePrefix()
 * @method static RouteBase     group(Closure $callback)
 * @method static RouteBase     getRouter()
 * @method static RouteRegister match($methods, $path, callable $action)
 * @method static RouteRegister get($path, callable $action)
 * @method static RouteRegister post($path, callable $action)
 * @method static RouteRegister getMethods()
 * @method static RouteRegister action(callable $action)
 * @method static RouteRegister getAction()
 * @method static RouteRegister path($path)
 * @method static RouteRegister getPath()
 * @method static RouteRegister name($name)
 * @method static RouteRegister getName()
 * @method static RouteRegister isNoAuth()
 * @method static RouteRegister isTokenIgnored()
 * @method static RouteRegister regex()
 * @method static RouteRegister hasRegex()
 * @method static RouteRegister handleMiddleware()
 * @method static RouteRegister getRoutePrefix()
 * @method static RouteRegister getRouteParam($name)
 * @method static RouteRegister getRouteParams()
 * @method static RouteRegister setRouteParamValue($name, $value)
 * @method static RouteRegister getRouteParamValue($name)
 * @method static RouteRegister getRouteParamValues()
 * @method static RouteRegister setRequest(Request $request)
 * @method static RouteRegister getRequest()
 */
final class Route
{
    /**
     * Handle static call to route.
     *
     * @param string $method     Name of method from RouteBase
     * @param mixed  $parameters Params to pass
     *
     * @return RouteBase
     */
    public function __call($method, $parameters)
    {
        return \call_user_func_array([new RouteBase(), $method], $parameters);
    }

    /**
     * Handle static call to route.
     *
     * @param string $method     Name of method from RouteBase
     * @param mixed  $parameters Params to pass
     *
     * @return RouteBase
     */
    public static function __callStatic($method, $parameters)
    {
        return \call_user_func_array([new RouteBase(), $method], $parameters);
    }
}
