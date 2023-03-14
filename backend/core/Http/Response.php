<?php

namespace BitApps\FM\Core\Http;

final class Response
{
    const SUCCESS = 'success';

    const ERROR = 'error';

    private static $_instance;

    private static $_message;

    private static $_status;

    private static $_code;

    private static $_data;

    private static $_httpStatus;

    private static $_headers = [];

    public static function instance()
    {
        if (\is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Sets data for success response.
     *
     * @param mixed $data Data to return on response
     *
     * @return self
     */
    public static function success($data)
    {
        self::$_data   = $data;
        self::$_status = self::SUCCESS;

        return self::instance();
    }

    /**
     * Sets data for error response.
     *
     * @param mixed $data Data to return on response
     *
     * @return self
     */
    public static function error($data)
    {
        self::$_data   = $data;
        self::$_status = self::ERROR;

        return self::instance();
    }

    /**
     * Returns data for response.
     *
     * @return mixed $_data
     */
    public static function getData()
    {
        return self::$_data;
    }

    /**
     * Returns status for response.
     *
     * @return string $_status
     */
    public static function getStatus()
    {
        return self::$_status;
    }

    /**
     * Sets message for response.
     *
     * @param string $message Data to return on response
     *
     * @return self
     */
    public static function message($message)
    {
        self::$_message = $message;

        return self::instance();
    }

    /**
     * Returns message response.
     *
     * @return mixed $_message
     */
    public static function getMessage()
    {
        return self::$_message;
    }

    /**
     * Sets code for response.
     *
     * @param string $code status code to return on response
     *
     * @return self
     */
    public static function code($code)
    {
        self::$_code = $code;

        return self::instance();
    }

    /**
     * Returns status code response.
     *
     * @return mixed $_code
     */
    public static function getCode()
    {
        if (!isset(self::$_code) && isset(self::$_status)) {
            return strtoupper(self::$_status);
        }

        return self::$_code;
    }

    /**
     * Sets http status code for response.
     *
     * @param string $code http status code to return on response
     *
     * @return self
     */
    public static function httpStatus($code)
    {
        self::$_httpStatus = $code;

        return self::instance();
    }

    /**
     * Returns status code response.
     *
     * @return mixed $_httpStatus
     */
    public static function getHttpStatusCode()
    {
        $statusCode = self::$_httpStatus;
        if (!$statusCode) {
            $statusCode = self::ERROR === self::$_status ? 400 : 200;
        }

        return $statusCode;
    }

    /**
     * Sets http headers for response.
     *
     * @param string $headers http headers to return on response
     *
     * @return self
     */
    public static function headers($headers)
    {
        self::$_headers = $headers;

        return self::instance();
    }

    /**
     * Sets http headers for response.
     *
     * @param string $header http header
     * @param string $value
     *
     * @return self
     */
    public static function header($header, $value)
    {
        self::$_headers[$header] = $value;

        return self::instance();
    }

    /**
     * Returns headers for response.
     *
     * @return array $_headers
     */
    public static function getHeaders()
    {
        return self::$_headers;
    }
}
