<?php

namespace BitApps\FM\Providers\FileManager;

\defined('ABSPATH') || exit();
class ClientOptions
{
    /**
     * Connector URL
     *
     * @var string
     */
    private $_url;

    /**
     * The interface lang to use
     *
     * @var string
     */
    private $_lang;

    /**
     * Data to append to all requests and to upload files
     *
     * @var string
     */
    private $_customData;

    /**
     * Additional css class for file manager node
     *
     * @var string
     */
    private $_cssClass;

    /**
     * Auto load required CSS
     *
     * @var string
     */
    private $_cssAutoLoad;

    /**
     * Remember last opened dir to open it after reload or in next session
     *
     * @var string
     */
    private $_rememberLastDir;

    /**
     * Clear historys(elFinder) on reload(not browser) function
     *
     * @var string
     */
    private $_reloadClearHistory;

    /**
     * Use browser native history with supported browsers
     *
     * @var string
     */
    private $_useBrowserHistory;

    /**
     * Display only certain files based on their mime type
     *
     * @var string
     */
    private $_onlyMimes;

    /**
     * Used to validate file names
     *
     * @var string
     */
    private $_validName;

    /**
     * Hash of default directory path to open
     *
     * @var string
     */
    private $_startPathHash;

    /**
     * Default view mode
     *
     * @var string
     */
    private $_defaultView;

    /**
     * Default sort type
     *
     * @var string
     */
    private $_sortType;

    /**
     * Default sort order
     *
     * @var string
     */
    private $_sortOrder;

    /**
     * Display folders first?
     *
     * @var string
     */
    private $_sortStickFolders;

    /**
     * The width of the elFinder main interface
     *
     * @var string
     */
    private $_width;

    /**
     * The height of the elFinder main interface (in pixels)
     *
     * @var string
     */
    private $_height;

    /**
     * Format dates using client
     *
     * @var string
     */
    private $_clientFormatDate;

    /**
     * Show datetime in UTC timezone
     *
     * @var string
     */
    private $_UTCDate;

    /**
     * File modification datetime format
     *
     * @var string
     */
    private $_dateFormat;

    /**
     * File modification datetime format for last two days (today, yesterday)
     *
     * @var string
     */
    private $_fancyDateFormat;

    /**
     * Style of file mode at cwd-list, info dialog
     *
     * @var string
     */
    private $_fileModeStyle;

    /**
     * Active commands list
     *
     * @var string
     */
    private $_commands;

    /**
     * Commands options used to interact with external callbacks, editors, plugins
     *
     * @var string
     */
    private $_commandsOptions;

    /**
     * Callback function for "getfile" command
     *
     * @var string
     */
    private $_getFileCallback;

    /**
     * Event listeners to bind on elFinder init
     *
     * @var string
     */
    private $_handlers;

    /**
     * UI plugins to load
     *
     * @var string
     */
    private $_ui;

    /**
     * Specifies the configuration for the elFinder UI
     *
     * @var string
     */
    private $_uiOptions;

    /**
     * The configuration for the right-click context menu
     *
     * @var string
     */
    private $_contextmenu;

    /**
     * Whether or not the elFinder interface will be resizable
     *
     * @var string
     */
    private $_resizable;

    /**
     * Timeout for open notification dialogs
     *
     * @var string
     */
    private $_notifyDelay;

    /**
     * Position and width of notification dialogs
     *
     * @var string
     */
    private $_notifyDialog;

    /**
     * Allow to drag and drop to upload files
     *
     * @var string
     */
    private $_dragUploadAllow;

    /**
     * Allow shortcuts
     *
     * @var string
     */
    private $_allowShortcuts;

    /**
     * Amount of thumbnails to create per one request
     *
     * @var string
     */
    private $_loadTmbs;

    /**
     * Lazy load
     *
     * @var string
     */
    private $_showFiles;

    /**
     * Lazy load
     *
     * @var string
     */
    private $_showThreshold;

    /**
     * The AJAX request type
     *
     * @var string
     */
    private $_requestType;

    /**
     * Separate URL to upload file to
     *
     * @var string
     */
    private $_urlUpload;

    /**
     * Timeout for upload using iframe
     *
     * @var string
     */
    private $_iframeTimeout;

    /**
     * Sync content by refreshing cwd every N seconds
     *
     * @var string
     */
    private $_sync;

    /**
     * Cookie option for browsers that does not support localStorage
     *
     * @var string
     */
    private $_cookie;

    /**
     * Passing custom headers during Ajax calls
     *
     * @var string
     */
    private $_customHeaders;

    /**
     * Any custom xhrFields to send across every ajax request, useful for CORS (Cross-origin resource sharing) support
     *
     * @var string
     */
    private $_xhrFields;

    /**
     * Debug config
     *
     * @var string
     */
    private $_debug;

    /**
     * Increase chunk size.
     *
     * @var string
     */
    private $_uploadMaxChunkSize;

    /**
     * Directory path to rm.wav file
     *
     * @var string
     */
    private $_soundPath;

    /**
     * Constructs Finder frontend options
     *
     * @param mixed $debug
     */
    public function __construct($debug)
    {
        $this->_debug = $debug;
    }

    public function getOptions()
    {
        $options = [];

        if (isset($this->_debug)) {
            $options['locale'] = $this->_debug;
        }

        return $options;
    }
}
