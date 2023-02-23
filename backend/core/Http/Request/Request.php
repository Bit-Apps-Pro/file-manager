<?php

namespace BitApps\FM\Core\Http\Request;

use ArrayAccess;
use BitApps\FM\Core\Helpers\Arr;
use BitApps\FM\Core\Helpers\JSON;
use BitApps\FM\Core\Http\IpTool;
use BitApps\FM\Core\Http\Request\Validator\Validator;
use BitApps\FM\Core\Http\Router\RouteRegister;
use JsonSerializable;
use ReturnTypeWillChange;
use RuntimeException;
use WP_REST_Request;

class Request implements ArrayAccess, JsonSerializable
{
    use IpTool, Validator;

    private $_route;

    private $_rest;

    private $_attributes = [];

    private $_queryParams = [];

    private $_routeParams = [];

    private $_body = [];

    /**
     * Undocumented function.
     */
    public function __construct(RouteRegister $route = null)
    {
        $this->_route = $route;
        $this->setBody();
        $this->setQueryParams();
        $this->setRouteParams();
        $this->_attributes = (array) $this->_queryParams + (array) $this->_body + (array) $this->_routeParams;
    }

    public function __isset($offset)
    {
        return $this->has($offset);
    }

    public function __get($offset)
    {
        return $this->get($offset);
    }

    public function __set($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    public function __unset($offset)
    {
        $this->unsetAttribute($offset);
    }

    public function __call($method, $parameters)
    {
        if (isset($this->_rest) && method_exists($this->_rest, $method)) {
            return \call_user_func_array([$this->_rest, $method], $parameters);
        }

        throw new RuntimeException('Undefined method [' . (string) $method . '] called on ' . __CLASS__ . 'class.');
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static())->{$method}(...$parameters);
    }

    public function __clone()
    {
        // do nothing
    }

    public function queryParams()
    {
        return $this->_queryParams;
    }

    /**
     * Returns Route sets for current request.
     *
     * @return RouteRegister
     */
    public function getRoute()
    {
        return $this->_route;
    }

    public function body()
    {
        return $this->_body;
    }

    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function contentType()
    {
        return isset($_SERVER['CONTENT_TYPE']) ? ltrim($_SERVER['CONTENT_TYPE'], 'application/') : '';
    }

    public function setApiRequest(WP_REST_Request $request)
    {
        $this->setBody((array) $request->get_body_params() + (array) $request->get_json_params());
        $this->setQueryParams((array) $request->get_query_params());
        $this->setRouteParams((array) $request->get_url_params());
        $this->_attributes = (array) $request->get_params();
        $this->_rest       = $request;
    }

    /**
     * Provides all files in a request if exist otherwise returns null.
     *
     * @return null|array
     */
    public function files()
    {
        return isset($_FILES) ? $_FILES : null;
    }

    public function all()
    {
        return $this->_attributes;
    }

    public function get($offset)
    {
        return Arr::get($this->_attributes, $offset);
    }

    public function input($offset)
    {
        $this->get($offset);
    }

    public function except()
    {
        $paramToIgnore = \func_get_args();
        $all           = $this->all();
        foreach ($paramToIgnore as $key) {
            unset($all[$key]);
        }

        return $all;
    }

    public function has($offset)
    {
        return Arr::has($this->_attributes, $offset);

        return isset($this->_attributes[$offset]);
    }

    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->unsetAttribute($offset);
    }

    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->_attributes;
    }

    private function setQueryParams($params = [])
    {
        if (!empty($params)) {
            $this->_queryParams = $params;
        } elseif (isset($_GET)) {
            $this->_queryParams = $_GET;
        }
    }

    private function setRouteParams($params = [])
    {
        if (!empty($params)) {
            $this->_routeParams = $params;
            foreach ($params as $name => $value) {
                $this->_route->setRouteParamValue($name, $value);
            }
        } elseif (isset($this->_route) && !\is_null($this->_route)) {
            $this->_routeParams = (array) $this->_route->getRouteParamValues();
        }
    }

    private function setBody($body = [])
    {
        if (!empty($body)) {
            $this->_body = $body;
        } else {
            if (
                strpos($this->contentType(), 'form-data')                === false
                && strpos($this->contentType(), 'x-www-form-urlencoded') === false
            ) {
                $this->_body = JSON::maybeDecode(file_get_contents('php://input'), true);
            }

            $this->_body = (array) $this->_body + (array) $_POST;
        }
    }

    private function setAttribute($key, $value)
    {
        $this->_attributes[$key] = $value;
    }

    private function unsetAttribute($key)
    {
        unset($this->_attributes[$key]);
    }
}
