<?php

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
/**
 * Class For Database.
 */

namespace BitApps\FM\Core\Database;

use Exception;

/**
 * Wrapper for wpdb.
 *
 * @static $prefix $wpdb $prefix
 *
 * @see wpdb File: wp-includes/wp-db.php
 */
final class Connection
{
    public static $instance;

    private static $_dbPrefix;

    private static $_previousError;

    private static $_previousErrorState;

    private static $_isLogEnabled = false;

    private static $_queries = [];

    private static $_errors = [];

    public function __get($var)
    {
        global $wpdb;
        if (property_exists($wpdb, $var)) {
            return $wpdb->{$var};
        }

        $instance = self::instance();

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

        $instance = self::instance();

        if (method_exists($instance, $name)) {
            return \call_user_func_array([$instance, $name], $arguments);
        }

        throw new Exception("Method " . esc_html($name) ."not found");
    }

    public static function suppressError()
    {
        global $wpdb;
        self::$_previousError      = $wpdb->last_error;
        self::$_previousErrorState = $wpdb->suppress_errors;
        $wpdb->last_error          = '';
        $wpdb->suppress_errors     = true;
    }

    public static function restoreErrorState()
    {
        global $wpdb;
        $wpdb->last_error      = self::$_previousError;
        $wpdb->suppress_errors = self::$_previousErrorState;
    }

    public static function getPrefix()
    {
        return self::wpPrefix() . self::pluginDBPrefix();
    }

    public static function pluginDBPrefix()
    {
        return self::$_dbPrefix;
    }

    public static function setPluginDBPrefix($prefix)
    {
        self::$_dbPrefix = $prefix;
    }

    public static function wpPrefix()
    {
        return self::prop('prefix');
    }

    public static function enableQuery()
    {
        self::$_isLogEnabled = true;
    }

    public static function queries()
    {
        return self::$_queries;
    }

    public static function errors()
    {
        return self::$_errors;
    }

    public static function prop($var)
    {
        global $wpdb;
        if (property_exists($wpdb, $var)) {
            return $wpdb->{$var};
        }

        $instance = self::instance();

        if (property_exists($instance, $var)) {
            return $instance->{$var};
        }
    }

    private static function _forwadCall($instance, $method, $args)
    {
        if (self::$_isLogEnabled &&  $method === 'query') {
            Connection::suppressError();
            $returnedData     = \call_user_func_array([$instance, $method], $args);
            self::$_queries[] = self::prop('last_query');
            self::$_errors[]  = self::prop('last_error');
            Connection::restoreErrorState();

            return $returnedData;
        }

        return \call_user_func_array([$instance, $method], $args);
    }

    private static function instance()
    {
        if (\is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }
}
