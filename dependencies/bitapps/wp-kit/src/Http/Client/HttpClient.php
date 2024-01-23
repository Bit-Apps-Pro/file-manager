<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Dependencies\BitApps\WPKit\Http\Client;

use BadMethodCallException;
use BitApps\FM\Dependencies\BitApps\WPKit\Helpers\JSON;

use InvalidArgumentException;

final class HttpClient
{
    private $_headers = [];

    private $_body;

    private $_formParams = [];

    private $_multipart = [];

    private $_json = [];

    private $_queryParams = [];

    private $_params = [];

    private $_baseUri;

    private $_boundary;

    private $_method;

    private $_options = [];

    /**
     * Undocumented function.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->setDefault($config);
    }

    public function __call($method, $params)
    {
        if (\in_array($method, ['post', 'get', 'put', 'delete', 'head', 'option'])) {
            $this->_method = $method;
            $url           = $this->_baseUri . $params[0];
            $query         = http_build_query($this->getQueryParams());
            if (!empty($query)) {
                $url = $url . '?' . $query;
            }

            $type    = strtoupper($method);
            $data    = $this->getPreparedPayload();
            $headers = $this->getHeaders();
            $options = $this->getOptions();

            return $this->request($url, $type, $data, $headers, $options);
        }

        throw new BadMethodCallException($method . ' Method not found in ' . __CLASS__);
    }

    public function setBaseUri($uri)
    {
        $this->_baseUri = $uri;

        return $this;
    }

    public function getBaseUri()
    {
        return $this->_baseUri;
    }

    public function setHeaders(array $headers)
    {
        if (empty($this->_headers)) {
            $this->_headers = $headers;
        } else {
            foreach ($headers as $key => $value) {
                $this->setHeader($key, $value);
            }
        }

        return $this;
    }

    public function getHeaders()
    {
        $headers = [];
        foreach ($this->_headers as $key => $value) {
            $headers[$key] = \is_array($value) ? implode(';', $value) : $value;
        }

        return $headers;
    }

    public function getHeader($key)
    {
        return isset($this->_headers[$key]) ? $this->_headers[$key] : false;
    }

    public function setHeader($key, $value)
    {
        return $this->_headers[ucwords($key)][] = $value;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function setOptions(array $options)
    {
        $this->_options = $options;

        return $this;
    }

    public function setBoundary($boundary)
    {
        $this->_boundary = '-------' . (string) $boundary;

        return $this;
    }

    public function getBoundary()
    {
        if (!isset($this->_boundary)) {
            $this->_boundary = $this->setBoundary(wp_generate_password(24));
        }

        return $this->_boundary;
    }

    public function setContentType($type)
    {
        $this->setHeader('Content-Type', $type);

        return $this;
    }

    public function getContentType($type)
    {
        return isset($this->_headers[$type]) ? $this->_headers[$type] : '';
    }

    public function setParams($data)
    {
        $this->_params = $data;

        return $this;
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function getParam($key)
    {
        return isset($this->_params[$key]) ? $this->_params[$key] : false;
    }

    public function setParam($key, $value)
    {
        return $this->_params[$key] = $value;
    }

    public function setQueryParams($data)
    {
        $this->_queryParams = $data;

        return $this;
    }

    public function getQueryParams()
    {
        return $this->_queryParams;
    }

    public function getQueryParam($key)
    {
        return isset($this->_queryParams[$key]) ? $this->_queryParams[$key] : false;
    }

    public function setQueryParam($key, $value)
    {
        if (isset($this->_queryParams[$key])) {
            if (!\is_array($this->_queryParams[$key])) {
                $this->_queryParams[$key] = [$this->_queryParams[$key]];
            }

            $this->_queryParams[$key][] = $value;
        } else {
            $this->_queryParams[$key] = $value;
        }

        return $this;
    }

    public function setBody($body)
    {
        $this->_body = $body;

        return $this;
    }

    public function getBody()
    {
        return $this->_body;
    }

    public function request($url, $type, $data, $headers = null, $options = null)
    {
        $defaultOptions = [
            'method'  => $type,
            'headers' => empty($headers) ? $this->getHeaders() : $headers,
            'body'    => $data,
            'timeout' => 30,

        ];
        $options = wp_parse_args($options, $defaultOptions);

        $requestResponse = wp_remote_request($url, $options);

        $responseCode = wp_remote_retrieve_response_code($requestResponse);

        if (is_wp_error($requestResponse)) {
            return $requestResponse;
        }

        $responseBody = wp_remote_retrieve_body($requestResponse);

        $decodedData = JSON::decode($responseBody);

        $response = empty($decodedData) ? $responseBody : $decodedData;

        if (!empty($responseCode)) {
            if (!empty($response) && \is_object($response)) {
                $response->status_code = $responseCode;
            } else {
                $response = (object) ['status_code' => $responseCode];
            }
        }

        return $response;
    }

    private function setDefault(array $config)
    {
        if (isset($config['base_uri'])) {
            $this->setBaseUri($config['base_uri']);
        }

        if (isset($config['content_type'])) {
            $this->setContentType($config['content_type']);
        }

        if (isset($config['headers'])) {
            $this->setHeaders($config['headers']);
        }

        if (isset($config['body'])) {
            $this->setBody($config['body']);
        }

        if (isset($config['form_params'])) {
            $this->setFormParams($config['form_params']);
        }

        if (isset($config['json'])) {
            $this->setJson($config['json']);
        }

        if (isset($config['multipart'])) {
            $this->setMultipart($config['multipart']);
        }
    }

    private function setJson($data)
    {
        $this->setContentType('application/json');
        $this->_json = $data;

        return $this;
    }

    private function getJson()
    {
        return $this->_json;
    }

    private function setFormParams($data)
    {
        $this->setContentType('application/x-www-form-urlencoded');
        $this->_formParams = $data;

        return $this;
    }

    private function getFormParams()
    {
        return $this->_formParams;
    }

    private function setMultipart($data)
    {
        $this->setContentType('multipart/form-data; charset=UTF-8');
        $this->_multipart = $data;

        return $this;
    }

    private function getMultipart()
    {
        return $this->_multipart;
    }

    private function getPreparedPayload()
    {
        $payload = null;
        if (!empty($this->_multipart)) {
            if (!empty($this->getBody()) && !empty($this->getFormParams()) && !empty($this->getJson())) {
                throw new InvalidArgumentException('Do not use multipart with json, params or body');
            }

            $payload = $this->getPreparedMultipart();
        } elseif (\is_string($this->getBody())) {
            $payload = $this->getBody();
        } else {
            $merged = array_merge(
                (array) $this->getBody(),
                (array) $this->getJson(),
                (array) $this->getFormParams()
            );
            if (!isset($this->_method) || $this->_method != 'get') {
                $payload = JSON::maybeEncode($merged);
            } else {
                $payload = $merged;
            }
        }

        return $payload;
    }

    private function getPreparedMultipart()
    {
        $multipart = '';
        if (!empty($this->getMultipart()) && \is_array($this->getMultipart())) {
            foreach ($this->getMultipart() as $part) {
                if (\is_array($part) && isset($part['name'], $part['contents'])) {
                    $multipart .= '--' . $this->getBoundary() . '\r\n';
                    $multipart .= 'Content-Disposition: form-data; name="' . $part['name'] . '"';
                    if (isset($part['filename'])) {
                        $multipart .= ';filename="' . $part['filename'] . '"';
                    }

                    $multipart .= '\r\n';
                    if (isset($part['headers'])) {
                        if (\is_array($part['headers'])) {
                            foreach ($part['headers'] as $key => $value) {
                                $multipart .= $key . ':';
                                $multipart .= \is_array($value) ? implode(';', $value) : $value;
                                $multipart .= '\r\n';
                            }
                        } elseif (\is_string($part['headers'])) {
                            $multipart .= $part['headers'] . '\r\n';
                        }
                    }

                    $multipart .= $part['contents'];
                    $multipart .= '\r\n';
                } else {
                    throw new InvalidArgumentException('Multipart must contain name, contents');
                }
            }
        }

        $multipart .= '--' . $this->getBoundary() . '--';

        return $multipart;
    }
}
