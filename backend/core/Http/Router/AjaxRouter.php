<?php

namespace BitApps\FM\Core\Http\Router;

use BitApps\FM\Core\Hooks\Hooks;

/**
 * Base Router.
 *
 * @var Router $_route
 */
final class AjaxRouter
{
    private $_router;

    public function __construct(Router $router)
    {
        $this->_router = $router;
    }

    public function registerRoutes()
    {
        foreach ($this->_router->getRoutes() as $route) {
            $this->addRoute($route);
        }
    }

    public function addRoute(RouteRegister $route)
    {
        if (
            !isset($_REQUEST['action'])
            ||  strpos($_REQUEST['action'], $this->_router->getAjaxPrefix()) === false
            || !\in_array(strtoupper($_SERVER['REQUEST_METHOD']), $route->getMethods())
        ) {
            return;
        }

        $requestPath = str_replace($this->_router->getAjaxPrefix(), '', $_REQUEST['action']);
        if (!$this->isRouteMatched($route, $requestPath)) {
            return;
        }

        Hooks::addAction('wp_ajax_' . $_REQUEST['action'], [$route, 'handleRequest']);
        if ($route->isNoAuth()) {
            Hooks::addAction('wp_ajax_nopriv_' . $_REQUEST['action'], [$route, 'handleRequest']);
        }

        $this->_router->addRegisteredRoute($this->currentRouteName(), $route);
    }

    public function currentRouteName()
    {
        return $_SERVER['REQUEST_METHOD'] . $_REQUEST['action'];
    }

    /**
     * Returns current registered route.
     *
     * @return RouteRegister
     */
    public function currentRoute()
    {
        return $this->_router->getRegisteredRoute($this->currentRouteName());
    }

    private function isRouteMatched(RouteRegister $route, $requestPath)
    {
        if ($route->getRoutePrefix() . $route->getPath() === $requestPath) {
            return true;
        }

        if (
            !$route->hasRegex()
            || preg_match('~^(?|' . $route->regex() . ')$~x', $requestPath, $matchedRoutes) === false
            || empty($matchedRoutes)
        ) {
            return false;
        }

        foreach ($route->getRouteParams() as $param => $attribute) {
            if (isset($matchedRoutes[$param])) {
                $route->setRouteParamValue($param, $matchedRoutes[$param]);
            }
        }

        return $matchedRoutes[0] === $requestPath;
    }
}
