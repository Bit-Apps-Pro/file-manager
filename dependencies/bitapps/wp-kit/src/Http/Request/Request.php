<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Dependencies\BitApps\WPKit\Http\Request;

use ArrayAccess;
use BitApps\FM\Dependencies\BitApps\WPKit\Configs\JsonConfig;
use BitApps\FM\Dependencies\BitApps\WPKit\Helpers\Arr;
use BitApps\FM\Dependencies\BitApps\WPKit\Helpers\JSON;
use BitApps\FM\Dependencies\BitApps\WPKit\Http\IpTool;
use BitApps\FM\Dependencies\BitApps\WPKit\Http\Router\RouteRegister;
use BitApps\FM\Dependencies\BitApps\WPValidator\Validator;
use JsonSerializable;
use ReturnTypeWillChange;
use RuntimeException;
use WP_REST_Request;

class Request extends Validator implements ArrayAccess, JsonSerializable
{
    use IpTool;

    protected $route;

    protected $rest;

    protected $attributes = [];

    protected $queryParams = [];

    protected $routeParams = [];

    protected $body = [];

    /**
     * Undocumented function.
     */
    public function __construct(RouteRegister $route = null)
    {
        $this->route = $route;
        $this->setBody();
        $this->setQueryParams();
        $this->setRouteParams();
        $this->attributes = (array) $this->queryParams + (array) $this->body + (array) $this->routeParams;
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
        if (isset($this->rest) && method_exists($this->rest, $method)) {
            return \call_user_func_array([$this->rest, $method], $parameters);
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
        return $this->queryParams;
    }

    /**
     * Returns Route sets for current request.
     *
     * @return RouteRegister
     */
    public function getRoute()
    {
        return $this->route;
    }

    public function body()
    {
        return $this->body;
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
        $this->attributes = (array) $request->get_params();
        $this->rest       = $request;
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
        return $this->attributes;
    }

    public function get($offset, $default = null)
    {
        return Arr::get($this->attributes, $offset, $default);
    }

    public function input($offset, $default = null)
    {
        $this->get($offset, $default);
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
        return Arr::has($this->attributes, $offset);

        // return isset($this->attributes[$offset]);
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
        return $this->attributes;
    }

    public function validate($rules, $messages = null, $attributeLabels = null)
    {
        $validator = $this->make($this->attributes, $rules, $messages, $attributeLabels);

        if ($validator->fails()) {
            $response = [
                'status' => 'error',
                'code'   => 'VALIDATION',
                'data'   => $validator->errors(),
            ];

            wp_send_json($response, 422);
        }

        return $validator->validated();
    }

    protected function setQueryParams($params = [])
    {
        if (!empty($params)) {
            $this->queryParams = $params;
        } elseif (isset($_GET)) {
            $this->queryParams = $_GET;
        }
    }

    protected function setRouteParams($params = [])
    {
        if (!empty($params)) {
            $this->routeParams = $params;
            foreach ($params as $name => $value) {
                $this->route->setRouteParamValue($name, $value);
            }
        } elseif (isset($this->route) && !\is_null($this->route)) {
            $this->routeParams = (array) $this->route->getRouteParamValues();
        }
    }

    protected function setBody($body = [])
    {
        if (!empty($body)) {
            $this->body = $body;
        } else {
            if (
                strpos($this->contentType(), 'form-data')                === false
                && strpos($this->contentType(), 'x-www-form-urlencoded') === false
            ) {
                $this->body = JSON::maybeDecode(file_get_contents('php://input'), JsonConfig::decodeAsArray());
            }

            if (!empty($_POST)) {
                $this->body = (array) $this->body + (array) $_POST;
            }
        }
    }

    protected function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    protected function unsetAttribute($key)
    {
        unset($this->attributes[$key]);
    }
}
