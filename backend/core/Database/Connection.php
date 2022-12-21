<?php
/**
 * Class For Database
 *
 * @category Database
 *
 * @author   Bit Code Developer <developer@bitcode.pro>
 */

namespace BitApps\FM\Core\Database;

use BitApps\FM\Config;

/**
 * Wrapper for wpdb
 *
 * @static $prefix $wpdb $prefix
 *
 * @see wpdb File: wp-includes/wp-db.php
 */

final class Connection
{
    public static $instance = null;

    private static $_previousErrorState = null;

    private static $_previousError = null;

    private static $_is_log_enabled = false;
    
    private static $_queries = [];
    
    private static $_errors = [];




    public static function suppressError()
    {
        global $wpdb;
        self::$_previousError = $wpdb->last_error;
        self::$_previousErrorState = $wpdb->suppress_errors;
        $wpdb->last_error = '';
        $wpdb->suppress_errors = true;
    }

    public static function restoreErrorState()
    {
        global $wpdb;
        $wpdb->last_error = self::$_previousError;
        $wpdb->suppress_errors = self::$_previousErrorState;
    }

    public static function getPrefix()
    {
        return self::wpPrefix() . Config::VAR_PREFIX;
    }

    public static function wpPrefix()
    {
        return self::get_blog_prefix(get_current_blog_id());
    }

    public static function enableQuery()
    {
        self::$_is_log_enabled = true;
    }
    
    public static function queries()
    {
        return self::$_queries;
    }

    public static function erros()
    {
        return self::$_errors;
    }

    public static function prop($var)
    {
        global $wpdb;
        if (property_exists($wpdb, $var)) {
            return $wpdb->{$var};
        }

        $instance = self::_instance();

        if (property_exists($instance, $var)) {
            return $instance->{$var};
        }
    }


    private static function _forwadCall($instance, $method, $args) {

        
        if (self::$_is_log_enabled && $method === 'query') {
            Connection::suppressError();
            $returned_data =  call_user_func_array([$instance, $method], $args);
            self::$_queries[] = self::prop("last_query");
            self::$_errors[] = self::prop("last_error");
            Connection::restoreErrorState();
            return $returned_data;
        }
        return call_user_func_array([$instance, $method], $args);
    }

    private static function _instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function __get($var)
    {
        global $wpdb;
        if (property_exists($wpdb, $var)) {
            return $wpdb->{$var};
        }

        $instance = self::_instance();

        if (property_exists($instance, $var)) {
            return $instance->{$var};
        }
    }

    public function __call($name, $arguments)
    {
        global $wpdb;

        if (method_exists($wpdb, $name)) {
            return $this->_forwadCall($wpdb, $name, $arguments);
        }
    }

    public static function __callStatic($name, $arguments)
    {
        global $wpdb;

        if (method_exists($wpdb, $name)) {
            return self::_forwadCall($wpdb, $name, $arguments);
        }

        $instance = self::_instance();

        if (method_exists($instance, $name)) {
            return call_user_func_array([$instance, $name], $arguments);
        }

        throw new \Exception("Method $name not found");
    }
}
