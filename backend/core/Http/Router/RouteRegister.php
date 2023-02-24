<?php

namespace BitApps\FM\Core\Http\Router;

use BitApps\FM\Core\Http\Request\Request;
use BitApps\FM\Core\Http\RequestType;
use BitApps\FM\Core\Http\Response;

use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use WP_REST_Request;
use WP_REST_Response;

final class RouteRegister
{
    private $_name;

    private $_methods = [];

    private $_action;

    private $_path;

    private $_routeBase;

    private $_routeParams;

    private $_routeParamValues;

    private $_regex;

    private $_regexMatched;

    /**
     * Instance of rest request
     *
     * @var WP_REST_Request
     */
    private $_restRequest;

    /**
     * Instance of Request
     *
     * @var Request
     */
    private $_request;

    private $_response = [];

    public function __construct(RouteBase $routeBase)
    {
        $this->_routeBase = $routeBase;
    }

    public function match($methods, $path, $action)
    {
        if (\is_string($methods)) {
            $methods = explode(',', $methods);
        }

        foreach ($methods as $method) {
            $this->register($method, $path, $action);
        }

        return $this;
    }

    public function get($path, $action)
    {
        return $this->register('GET', $path, $action);
    }

    public function post($path, $action)
    {
        return $this->register('POST', $path, $action);
    }

    public function getMethods()
    {
        return $this->_methods;
    }

    public function action($action)
    {
        $this->_action = $action;

        return $this;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function path($path)
    {
        $this->_path = $path;

        return $this;
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function name($name)
    {
        $this->_name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function isNoAuth()
    {
        return $this->_routeBase->isNoAuth();
    }

    public function isTokenIgnored()
    {
        return $this->_routeBase->isTokenIgnored();
    }

    public function regex()
    {
        if (isset($this->_regex)) {
            return $this->_regex;
        }

        if (!$this->hasRegex()) {
            return false;
        }

        return $this->makeRegex();
    }

    public function hasRegex()
    {
        if (!isset($this->_path) || (isset($this->_regexMatched) && empty($this->_regexMatched[0]))) {
            return false;
        }

        return !(preg_match_all('/\{\w+\??\}\??/', $this->_path, $this->_regexMatched) === false
            || empty($this->_regexMatched[0])
        );
    }

    public function getMiddleware()
    {
        return $this->_routeBase->getMiddleware();
    }

    public function handleMiddleware()
    {
        if (empty($middlewares = $this->getMiddleware())) {
            return;
        }

        $router = $this->getRouter();
        foreach ($middlewares as $middleware) {
            $middlewareData = explode(':', (string) $middleware);
            $middleware     = $middlewareData[0];
            $params         = [];
            if (isset($middlewareData[1])) {
                $params = explode(',', (string) $middlewareData[1]);
            }

            if (
                ($middlewareObj = $router->getRegisteredMiddleware($middleware))
                && ($response = $this->invokeAsReflection($middlewareObj, 'handle')) !== true
            ) {
                $this->setResponse($response);
                $this->sendResponse();
            }
        }
    }

    public function getRoutePrefix()
    {
        return $this->_routeBase->getRoutePrefix();
    }

    public function getRouteParam($name)
    {
        if (!isset($this->_routeParams[$name])) {
            return false;
        }

        return $this->_routeParams[$name];
    }

    public function getRouteParams()
    {
        return $this->_routeParams;
    }

    public function setRouteParamValue($name, $value)
    {
        $this->_routeParamValues[$name] = $value;
    }

    public function getRouteParamValue($name)
    {
        if (!isset($this->_routeParamValues[$name])) {
            return false;
        }

        return $this->_routeParamValues[$name];
    }

    public function getParamValue(ReflectionParameter $param)
    {
        $value     = $param->isOptional() && $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
        $paramName = $param->getName();
        if ($isRouteParam = $this->getRouteParamValue($paramName)) {
            $value = $isRouteParam;
        }

        if (!$type = $param->getType()) {
            return $value;
        }

        if ($type instanceof ReflectionNamedType) {
            $type = $type->getName();
        } else {
            $type = (string) $type;
        }

        if (!class_exists($type)) {
            return $value;
        }

        if (Request::class === $type || is_subclass_of($type, Request::class)) {
            $this->setRequest($type);
            $value = $this->getRequest();
        } elseif ($isRouteParam && $value === $isRouteParam && method_exists($type, '__construct')) {
            $value = new $type($value);
        } elseif (!$param->isOptional()) {
            $value = new $type();
        }

        return $value;
    }

    public function getRouteParamValues()
    {
        return $this->_routeParamValues;
    }

    /**
     * Returns Request for this route.
     *
     * @return Request
     */
    public function getRequest()
    {
        if (!isset($this->_request)) {
            $this->setRequest();
        }

        return $this->_request;
    }

    /**
     * Returns router for this route.
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->_routeBase->getRouter();
    }

    /**
     * Returns router type for this route.
     *
     * @return string
     */
    public function getRouterType()
    {
        return $this->getRouter()->getRequestType();
    }

    public function handleRequest()
    {
        ob_start();
        if (\func_num_args() && ($apiRequest = \func_get_args()[0]) instanceof WP_REST_Request) {
            $this->setRestRequest($apiRequest);
        }

        $this->handleMiddleware();
        $this->handleAction($this);
        ob_clean();

        return $this->sendResponse();
    }

    private function setRestRequest(WP_REST_Request $request)
    {
        $this->_restRequest = $request;
    }

    private function getRestRequest()
    {
        return $this->_restRequest;
    }

    /**
     * Sets Request for this route.
     *
     * @param Request $request
     */
    private function setRequest($request = null)
    {
        if ($request === null) {
            $this->_request = new Request($this);
        } else {
            $this->_request = new $request($this);
        }

        if (isset($this->_restRequest)) {
            $this->_request->setApiRequest($this->_restRequest);
        }

        if (method_exists($this->_request, 'rules')) {
            $validationStatus = $this->_request->validate();
            if ($this->_request->validated() !== true) {
                $this->setResponse(Response::error($validationStatus)->code('VALIDATION'));
                $this->sendResponse();
            }
        }

        return $this->_request;
    }

    private function register($method, $path, $action)
    {
        $this->_methods[] = strtoupper($method);
        $this->path($path);
        $this->action($action);

        return $this;
    }

    private function makeRegex()
    {
        $path = str_replace('/', '\\/', $this->_path);
        foreach ($this->_regexMatched[0] as $param) {
            $name     = trim($param, '{}?');
            $required = true;
            if (strpos($param, '?')) {
                $required = false;
            }

            $this->setRouteParam($name, ['required' => $required]);
            $regexToSet = "(?P<{$name}>[^\\/]+)" . ($required ? '' : '?');
            $path       = str_replace($param, $regexToSet, $path);
        }

        return $path;
    }

    private function setRouteParam($name, $attribute)
    {
        $this->_routeParams[$name] = $attribute;
    }

    private function handleAction()
    {
        $action = $this->getAction();
        if (method_exists($action[0], $action[1])) {
            $response = $this->invokeAsReflection($action[0], $action[1]);
            $this->setResponse($response);
        } else {
            $this->setResponse(Response::message('Route action doesn\'t exists'));
        }
    }

    private function invokeAsReflection($class, $method)
    {
        $reflectionMethod = new ReflectionMethod($class, $method);
        $params           = [];
        foreach ($reflectionMethod->getParameters() as $id => $param) {
            $params[] = $this->getParamValue($param);
        }
        
        $classObj = is_string($class) ? new $class() : $class;
        
        return $reflectionMethod->invoke($reflectionMethod->isStatic() ? null : $classObj, ...$params);
    }

    private function setResponse($response)
    {
        if (is_wp_error($response)) {
            $response = Response::error($response->get_error_data())
                ->code($response->get_error_code())
                ->message($response->get_error_message());
        } elseif (!$response instanceof Response) {
            $response = Response::success($response)->code('SUCCESS');
        }

        if ($status = $response->getStatus()) {
            $responseData['status'] = $status;
        }

        if ($message = $response->getMessage()) {
            $responseData['message'] = $message;
        }

        if ($code = $response->getCode()) {
            $responseData['code'] = $code;
        }

        $responseData['data'] = $response->getData();
        $additional           = ob_get_clean();
        if (!empty($additional)) {
            $responseData['additional'] = $additional;
        }

        $this->_response = [
            'data'        => $responseData,
            'http_status' => $response->getHttpStatusCode(),
            'headers'     => $response->getHeaders(),
        ];
    }

    private function sendResponse()
    {
        if (RequestType::API === $this->getRouterType()) {
            return $this->sendApiResponse();
        }

        $this->sendAjaxResponse();
    }

    private function sendApiResponse()
    {
        $restResponse = new WP_REST_Response();
        $restResponse->set_data($this->_response['data']);
        $restResponse->set_status($this->_response['http_status']);
        $restResponse->set_headers($this->_response['headers']);

        return $restResponse;
    }

    private function sendAjaxResponse()
    {
        if (!headers_sent() && $this->_response['headers']) {
            foreach ($this->_response['headers'] as $key => $value) {
                header("{$key}: {$value}");
            }
        }

        wp_send_json($this->_response['data'], $this->_response['http_status']);
    }
}
