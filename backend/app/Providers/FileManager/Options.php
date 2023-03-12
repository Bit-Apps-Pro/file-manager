<?php

namespace BitApps\FM\FileManager;

\defined('ABSPATH') || exit();
class Options
{
    /**
     * Set locale. Currently only UTF-8 locales are supported. Passed to setLocale PHP function.
     *
     * Warning: Setting the locale correctly is very important.
     *  Especially if you do not match the character encoding to that of the server file system,
     *  it will create a security vulnerability.
     *
     * Default value: 'en_US.UTF-8'
     *
     * @var string
     */
    private $_locale;

    /*
     * elFinderVolumeDriver mime.type file path as defaults.
     * This can be overridden in each of the volume by setting the volume root mimefile.
     *  The default value '' meaning uses a file 'php/mime.type'.
     */
    private $_defaultMimefile;

    /**
     * Session handling wrapper class object.
     * It must be implement elFinderSessionInterface.
     *
     * @var elFinderSessionInterface
     */
    private $_session;

    /**
     * Set sessionCacheKey. PHP $_SESSION array key of elFinder caches.
     *
     * Default value: 'elFinderCaches'
     *
     * @var string
     */
    private $_sessionCacheKey;

    /**
     * Finder save session data as UTF-8.
     * If the session storage mechanism of the system does not allow UTF-8, and it must be set true.
     *
     * Default value: false
     *
     * @var string
     * */
    private $_base64encodeSessionData;

    /**
     * Temp directory path for Upload. Default uses sys_get_temp_dir()
     *
     * @var string
     */
    private $_uploadTempPath;

    /*
     * Temp directory path for temporally working files. Default uses ./.tmp if it writable.
     *
     * Default value: './.tmp' or sys_get_temp_dir()
     */
    private $_commonTempPath;

    /**
     * Connection flag files path that connection check of current request.
     *  A file is created every time an access is made to this location and it is deleted at the end of the request.
     * It is recommended to specify RAM disk such as "/dev/shm".
     *
     * Default value: commonTempPath or ''
     *
     * @var string
     */
    private $_connectionFlagsPath;

    /**
     * Max allowed archive files size (0 - no limit)
     * Default value: 0
     *
     * @var int
     */
    private $_maxArcFilesSize;

    /**
     * Root options of the network mounting volume
     * Default value: array()
     * Example:
     * 'optionsNetVolumes' => array(
     *    // key '*' is common additional volume root options
     *    '*'   => array(),
     *    // key of elFinder::$netDrivers is each protocol volumes
     *    'ftp' => array()
     *  )
     *
     * @var array
     */
    private $_optionsNetVolumes;

    /**
     * Max number of limits of selectable items (0 - no limit)
     *
     * Default value: 1000
     *
     * @var int
     */
    private $_maxTargets;

    /*
     * Throw Error on exec()
     * true need try{} block for $connector->run();
     *
     * Default value: false
     */
    private $_throwErrorOnExec;

    /**
     * Send debug to client.
     *
     * Default value: false
     *
     * @var bool
     */
    private $_debug;

    /**
     * Bind callbacks for user actions
     *
     * @see https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#bind
     *
     * @var array<string, callable>
     */
    private $_bind;
}
