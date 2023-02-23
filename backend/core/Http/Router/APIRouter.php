<?php

namespace BitApps\FM\Core\Http\Router;

use WP_REST_Controller;
use WP_REST_Server;

final class APIRouter extends WP_REST_Controller
{
    const READABLE = WP_REST_Server::READABLE;

    const CREATABLE = WP_REST_Server::CREATABLE;

    const EDITABLE = WP_REST_Server::EDITABLE;

    const DELETABLE = WP_REST_Server::DELETABLE;

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

    /**
     * Registers api route.
     *
     * @param RouteRegister $route api route
     */
    public function addRoute(RouteRegister $route)
    {
        $args = [];
        foreach ($route->getMethods() as $method) {
            $args[] = [
                'methods'             => $this->getMethod($method),
                'callback'            => [$route, 'handleRequest'],
                'permission_callback' => '__return_true',
            ];
        }

        $path   = $route->hasRegex() ? $route->regex() : $route->getPath();
        $prefix = $route->getRoutePrefix();
        if ($prefix) {
            if (substr($prefix, -1) !== '/') {
                $path = $prefix . '/' . $path;
            } else {
                $path = $prefix . $path;
            }
        }
        register_rest_route(
            $this->_router->getNamespace(),
            $this->_router->getVersion() . $path,
            $args
        );
    }

    public function getMethod($method)
    {
        switch (strtolower($method)) {
            case 'get':
                return self::READABLE;

            case 'post':
                return self::CREATABLE;

            case 'put':
                return self::EDITABLE;

            case 'delete':
                return self::DELETABLE;

            default:
                return self::READABLE;
        }
    }
}
