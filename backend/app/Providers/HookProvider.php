<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Config;
use BitApps\FM\Core\Utils\Hooks;
use BitApps\FM\Core\Http\Request;
use BitApps\FM\Core\Http\Router\Router;
use FilesystemIterator;

class HookProvider
{
    private $_pluginBackend;

    public function __construct()
    {
        $this->_pluginBackend = Config::get('BASEDIR') . DIRECTORY_SEPARATOR;
        $this->loadTriggersAjax();
        $this->loadAppHooks();
        $this->loadActionsHooks();
        Hooks::add('rest_api_init', [$this, 'loadApi']);
    }

    /**
     * Helps to register App hooks
     *
     * @return null
     */
    protected function loadAppHooks()
    {
        if (Request::Check('ajax') && is_readable($this->_pluginBackend.'hooks'.DIRECTORY_SEPARATOR.'ajax.php')) {
            include $this->_pluginBackend.'hooks'.DIRECTORY_SEPARATOR.'ajax.php';
        }
        if (is_readable($this->_pluginBackend.'hooks.php')) {
            include $this->_pluginBackend.'hooks.php';
        }
    }

    /**
     * Helps to register Triggers ajax
     *
     * @return null
     */
    protected function loadTriggersAjax()
    {
        // $this->_includeTaskHooks('Triggers');
    }

    /**
     * Helps to register integration ajax
     *
     * @return void
     */
    public function loadActionsHooks()
    {
        // $this->_includeTaskHooks('Actions');
    }

    /**
     * Backend Routes and Hooks
     *
     * @param string $task_name Triggers|Actions
     * 
     * @return void
     */
    private function _includeTaskHooks($task_name)
    {
        $task_dir = $this->_pluginBackend.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$task_name;
        $dirs = new FilesystemIterator($task_dir);
        foreach ($dirs as $dirInfo) {
            if ($dirInfo->isDir()) {
                $task_name = basename($dirInfo);
                $task_path = $task_dir.DIRECTORY_SEPARATOR.$task_name.DIRECTORY_SEPARATOR;
                if (is_readable($task_path.'Routes.php') && Request::Check('ajax') && Request::Check('admin')) {
                    include $task_path.'Routes.php';
                }
                if (is_readable($task_path.'Hooks.php')) {
                    include $task_path.'Hooks.php';
                }
            }
        }
    }

    /**
     * Loads API routes
     *
     * @return null
     */
    public function loadApi()
    {
        if (is_readable($this->_pluginBackend.'hooks'.DIRECTORY_SEPARATOR.'api.php') && Request::Check('api')) {
            $router = new Router(Config::SLUG, 'v1');
            include $this->_pluginBackend.'hooks'.DIRECTORY_SEPARATOR.'api.php';
        }
    }
}
