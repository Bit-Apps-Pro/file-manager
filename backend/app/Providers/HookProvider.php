<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Config;
use BitApps\WPKit\Hooks\Hooks;
use BitApps\WPKit\Http\RequestType;
use BitApps\WPKit\Http\Router\Router;
use BitApps\FM\Plugin;

class HookProvider
{
    private $_pluginBackend;

    public function __construct()
    {
        $this->_pluginBackend = Config::get('BACKEND_DIR') . DIRECTORY_SEPARATOR;
        $this->loadAppHooks();
        Hooks::addAction('rest_api_init', [$this, 'loadApi']);
    }

    /**
     * Loads API routes.
     */
    public function loadApi()
    {
        if (
            is_readable($this->_pluginBackend . 'hooks' . DIRECTORY_SEPARATOR . 'api.php')
            && RequestType::is(RequestType::API)
        ) {
            $router = new Router(RequestType::API, Config::SLUG, 'v1');

            include_once $this->_pluginBackend . 'hooks' . DIRECTORY_SEPARATOR . 'api.php';

            $router->register();
        }
    }

    /**
     * Helps to register App hooks.
     */
    protected function loadAppHooks()
    {
        if (
            RequestType::is(RequestType::AJAX)
            && is_readable($this->_pluginBackend . 'hooks' . DIRECTORY_SEPARATOR . 'ajax.php')
        ) {
            $router = new Router(RequestType::AJAX, Config::VAR_PREFIX, '');
            $router->setMiddlewares(Plugin::instance()->middlewares());
            include_once $this->_pluginBackend . 'hooks' . DIRECTORY_SEPARATOR . 'ajax.php';
            $router->register();
        }

        if (is_readable($this->_pluginBackend . 'hooks.php')) {
            include_once $this->_pluginBackend . 'hooks.php';
        }
    }
}
