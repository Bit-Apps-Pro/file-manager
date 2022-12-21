<?php

namespace BitApps\FM\Core\Http\Router;

use WP_REST_Controller;


final class Router
{
    private $_routes;

    private $_namespace;

    private $_version;

    private static $_instance = null;


    public function __construct($namespace, $version)
    {
        $this->_routes = [];
        $this->_namespace = $namespace;
        $this->_version = $version;
        self::$_instance = $this;
    }

    public function getBasePrefix()
    {
        return empty($this->_version) ? '' : '/' . $this->_version;
    }

    public function getNamespace()
    {
        return $this->_namespace;
    }

    public function getRoutes()
    {
        return $this->_routes;
    }


    public function getRoute($route)
    {
        return isset($this->_routes[$route]) ? $this->_routes[$route] : null;
    }


    public function addRoute($route)
    {
        $this->_routes[] = $route;
    }

    public static function instance($namespace = null, $version = null)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($namespace, $version);
        }
        return self::$_instance;
    }

    public function register()
    {
        foreach ($this->_routes as $route) {
            $route->register();
        }
    }
}
