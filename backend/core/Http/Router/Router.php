<?php

namespace BitApps\FM\Core\Http\Router;

final class Router
{
    private $_routes;

    private $_registeredRoutes;

    private $_middlewares;

    private $_registeredMiddlewares;

    private $_namespace;

    private $_version;

    private $_requestType;

    private static $_instance;

    public function __construct($type, $namespace, $version)
    {
        $this->_routes      = [];
        $this->_namespace   = $namespace;
        $this->_version     = $version;
        $this->_requestType = $type;
        self::$_instance    = $this;
    }

    public function getRequestType()
    {
        return $this->_requestType;
    }

    public function getVersion()
    {
        return empty($this->_version) ? '' : $this->_version . '/';
    }

    public function getNamespace()
    {
        return $this->_namespace;
    }

    public function getAjaxPrefix()
    {
        return $this->getNamespace() . (empty($this->_version) ? '' : '/' . $this->_version);
    }

    public function getRoutes()
    {
        return $this->_routes;
    }

    public function getRoute($routeIndex)
    {
        return isset($this->_routes[$routeIndex]) ? $this->_routes[$routeIndex] : null;
    }

    public function addRoute(RouteRegister $route)
    {
        $this->_routes[] = $route;
    }

    public function addRegisteredRoute($name, RouteRegister $route)
    {
        $this->_registeredRoutes[$name] = $route;
    }

    public function getRegisteredRoute($routeName)
    {
        return isset($this->_registeredRoutes[$routeName]) ? $this->_registeredRoutes[$routeName] : null;
    }

    public function getRegisteredRoutes()
    {
        return $this->_registeredRoutes;
    }

    public static function instance($type = 'ajax', $namespace = null, $version = null)
    {
        if (\is_null(self::$_instance)) {
            self::$_instance = new self($type, $namespace, $version);
        }

        return self::$_instance;
    }

    public function register()
    {
        if ($this->getRequestType() === 'ajax') {
            $ajaxRouter = new AjaxRouter($this);
            $ajaxRouter->registerRoutes();
        } elseif ($this->getRequestType() === 'api') {
            $ajaxRouter = new APIRouter($this);
            $ajaxRouter->registerRoutes();
        }
    }

    public function setMiddlewares($middlewares)
    {
        $this->_middlewares = $middlewares;
    }

    public function getRegisteredMiddleware($name)
    {
        if (!isset($this->_registeredMiddlewares[$name])) {
            $this->_registeredMiddlewares[$name] = isset($this->_middlewares[$name])
                && class_exists($this->_middlewares[$name])
                && method_exists($this->_middlewares[$name], 'handle') ? new $this->_middlewares[$name]() : null;
        }

        return $this->_registeredMiddlewares[$name];
    }
}
