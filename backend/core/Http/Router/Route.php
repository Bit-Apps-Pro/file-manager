<?php

namespace BitCode\FI\Core\Http\Router;

use Closure;

final class Route
{
    private static $_prefix = 'btcfi_';

    private static $_invokeable;
    
    private static $_middleware;

    private static $_no_auth = false;

    private static $_ignore_token = false;

    public static function middleware($middleware)
    {
        self::$_middleware = $middleware;
        return static::class;
    }
    
    
    public static function prefix($prefix)
    {
        self::$_prefix = $prefix;
        return static::class;
    }
    
    
    public static function group(Closure $callback)
    {
        $callback();
        self::reset();
    }
    
    private static function reset()
    {
        unset(self::$_middleware);
        unset(self::$_prefix);
        
    }

    public static function get($hook, $invokeable)
    {
        return static::request('GET', $hook, $invokeable);
    }

    public static function post($hook, $invokeable)
    {
        return static::request('POST', $hook, $invokeable);
    }

    public static function request($method, $hook, $invokeable)
    {
        if ($_SERVER['REQUEST_METHOD'] != $method || ! isset($_REQUEST['action']) || strpos($_REQUEST['action'], $hook) === false) {
            if (static::$_no_auth) {
                static::$_no_auth = false;
            }
            if (static::$_ignore_token) {
                static::$_ignore_token = false;
            }

            return;
        }
        if (static::$_ignore_token) {
            static::$_ignore_token = false;
            static::$_invokeable[static::$_prefix.$hook][$_SERVER['REQUEST_METHOD'].'_ignore_token'] = true;
        }
        static::$_invokeable[static::$_prefix.$hook][$_SERVER['REQUEST_METHOD']] = $invokeable;
        Hooks::add('wp_ajax_'.static::$_prefix.$hook, [__CLASS__, 'action']);
        if (static::$_no_auth) {
            static::$_no_auth = false;
            Hooks::add('wp_ajax_nopriv_'.static::$_prefix.$hook, [__CLASS__, 'action']);
        }
    }

    public static function action()
    {
        if (
            isset(static::$_invokeable[sanitize_text_field($_REQUEST['action'])][$_SERVER['REQUEST_METHOD'].'_ignore_token'])
            || isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'btcfi_nonce')
        ) {
            $invokeable = static::$_invokeable[sanitize_text_field($_REQUEST['action'])][$_SERVER['REQUEST_METHOD']];
            unset($_POST['_ajax_nonce'], $_POST['action'], $_GET['_ajax_nonce'], $_GET['action']);
            if (method_exists($invokeable[0], $invokeable[1])) {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'form-data') === false && strpos($_SERVER['CONTENT_TYPE'], 'x-www-form-urlencoded') === false) {
                        $inputJSON = file_get_contents('php://input');
                        $data = is_string($inputJSON) ? \json_decode($inputJSON) : $inputJSON;
                    } else {
                        $data = (object) $_POST;
                    }
                } else {
                    $data = (object) $_GET;
                }

                $reflectionMethod = new \ReflectionMethod($invokeable[0], $invokeable[1]);
                $response = $reflectionMethod->invoke($reflectionMethod->isStatic() ? null : new $invokeable[0](), $data);
                if (is_wp_error($response)) {
                    wp_send_json_error($response);
                } else {
                    wp_send_json_success($response);
                }
            } else {
                wp_send_json_error('Method doesn\'t exists');
            }
        } else {
            wp_send_json_error(
                __(
                    'Token expired',
                    Config::SLUG
                ),
                401
            );
        }
    }

    public static function no_auth()
    {
        self::$_no_auth = true;

        return new static();
    }

    public static function ignore_token()
    {
        self::$_ignore_token = true;

        return new static();
    }
}
