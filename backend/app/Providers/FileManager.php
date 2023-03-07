<?php

namespace BitApps\FM\Providers;

\defined('ABSPATH') or exit();

use BitApps\FM\Plugin;
use elFinder;
use elFinderConnector;
use FM_BootStart;

class FileManager
{
    /**
     * @var $version Wordpress file manager plugin version
     *
     * */
    public $version;

    public $version_no;

    /**
     * @var $site Site url
     *
     * */
    public $site;

    /**
     * @var $giribaz_landing_page Landing page for giribaz
     *
     * */
    public $giribaz_landing_page;

    /**
     * @var $support_page Support ticket page
     *
     * */
    public $support_page;

    /**
     * @var $feedback_page Feedback page
     *
     * */
    public $feedback_page;

    /**
     * @var $file_manager_view_path View path of library file manager
     *
     * */
    public $file_manager_view_path;

    public function __construct($name)
    {
    }

    /**
     * File Manager connector function
     *
     * @throws Exception
     */
    public function connector()
    {
        // Allowed mime types
        $mime             = Plugin::instance()->mimes();
        $preferences      = Plugin::instance()->preferences();
        $opts             = [
            'bind' => [
                'put.pre'                                                                                                                                                                                                                                                                                                                                                                                                                                                => [Plugin::instance()->syntaxChecker(), 'checkSyntax'], // Syntax Checking.
                'archive.pre back.pre chmod.pre colwidth.pre copy.pre cut.pre duplicate.pre editor.pre put.pre extract.pre forward.pre fullscreen.pre getfile.pre help.pre home.pre info.pre mkdir.pre mkfile.pre netmount.pre netunmount.pre open.pre opendir.pre paste.pre places.pre quicklook.pre reload.pre rename.pre resize.pre restore.pre rm.pre search.pre sort.pre up.pre upload.pre view.pre zipdl.pre tree.pre parents.pre ls.pre tmb.pre size.pre dim.pre' => [&$this, 'security_check'],
                //				 'archive.pre back.pre chmod.pre colwidth.pre copy.pre cut.pre duplicate.pre editor.pre put.pre extract.pre forward.pre fullscreen.pre getfile.pre help.pre home.pre info.pre mkdir.pre mkfile.pre netmount.pre netunmount.pre open.pre opendir.pre paste.pre places.pre quicklook.pre reload.pre rename.pre resize.pre restore.pre rm.pre search.pre sort.pre up.pre upload.pre view.pre zipdl.pre file.pre tree.pre parents.pre ls.pre tmb.pre size.pre dim.pre get.pre' => array(&$this, 'security_check'),
                'upload'                                                         => [Plugin::instance()->mediaSyncs(), 'onFileUpload'],
                'zipdl.pre file.pre rename.pre put.pre upload.pre'               => [Plugin::instance()->logger(), 'log'],
            ],
            'debug' => WP_DEBUG,
            'roots' => [
                [
                    'alias'           => $preferences->getRootVolumeName(),
                    'driver'          => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
                    'path'            => $preferences->getRootPath(),                     // path to files (REQUIRED)
                    'URL'             => $preferences->getRootUrl(),
                    'uploadDeny'      => [],                // All Mimetypes not allowed to upload
                    'uploadAllow'     => $mime->getTypes(), // All MIME types is allowed
                    'uploadOrder'     => ['order', 'allow', 'deny'],      // allowed Mimetype `image` and `text/plain` only
                    'accessControl'   => [Plugin::instance()->accessControl(), 'control'],
                    'acceptedName'    => [Plugin::instance()->accessControl(), 'validateName'], // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#acceptedName
                    'disabled'        => [],    // List of disabled operations
                    'dispInlineRegex' => '^(?:image|application/(?:vnd\.)?(?:ms(?:-office|word|-excel|-powerpoint)|openxmlformats-officedocument)|text/plain$)', // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#dispInlineRegex
                    'trashHash'       => $preferences->isTrashAllowed() ? 't1_Lw' : '',                     // elFinder's hash of trash folder
                    'winHashFix'      => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
                    // 'defaults'   => array('read' => true, 'write' => true,'locked'=>true),
                    'allowChmodReadOnly' => true, // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#allowChmodReadOnly
                    'statOwner'          => true, // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#statOwner

                    'attributes' => [ // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#attributes
                        [ // hide specipic folder.
                            'pattern' => '!^/img!',
                            'hidden'  => false,
                            'read'    => true,
                            'write'   => true,
                            'locked'  => false,
                        ],
                        [ // hide specipic folder.
                            'pattern' => '!^/inc!',
                            'hidden'  => false,
                            'read'    => true,
                            'write'   => true,
                            'locked'  => false,
                        ],
                        // array( // hide specipic file type.
                        // 	'pattern' => '!.gitignore!',
                        // 	'hidden' => false,
                        // 	'read'   => true,
                        // 	'write'  => true,
                        // 	'locked' => false,
                        // ),
                        // array( // hide all files type.
                        // 	'pattern' => '/^.+/',
                        // 	'hidden' => false,
                        // 	'read'   => true,
                        // 	'write'  => true,
                        // 	'locked' => false,
                        // ),

                    ],
                    // 'copyTo' => true, //https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#copyTo
                    'uploadMaxSize' => 0, // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#uploadMaxSize
                    // 'archiveMimes' => array(), // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#archiveMimes
                    // 'dirMode'        => 0755,            // new dirs mode (default 0755)
                    // 'fileMode'       => 0644,            // new files mode (default 0644)
                    // 'maxTargets'=> 0,
                ],
                [
                    'alias'         => 'Media',
                    'driver'        => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
                    'path'          => FM_MEDIA_BASE_DIR_PATH,                     // path to files (REQUIRED)
                    'URL'           => FM_MEDIA_BASE_DIR_URL,                  // URL to files (REQUIRED)
                    'uploadDeny'    => [],                // All Mimetypes not allowed to upload
                    'uploadAllow'   => $mime->getTypes(), // All MIME types is allowed
                    'uploadOrder'   => ['allow', 'deny'],      // allowed Mimetype `image` and `text/plain` only
                    'accessControl' => [Plugin::instance()->accessControl(), 'control'],
                    'disabled'      => [],    // List of disabled operations
                ],
            ]
        ];
        /**
         * Enable/Disable trash directory.
         */
        if ($preferences->isTrashAllowed()  && is_writable(FM_WP_UPLOAD_DIR['basedir'])) {
            $opts['roots'][] = [
                'id'            => '1',
                'driver'        => 'Trash',
                'path'          => FM_TRASH_DIR_PATH,  // path to files (REQUIRED)
                'tmbURL'        => FM_TRASH_TMB_DIR_URL, // path to files (REQUIRED),
                'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
                'uploadDeny'    => [],                // Recomend the same settings as the original volume that uses the trash
                'uploadAllow'   => $mime->getTypes(), // Same as above
                'uploadOrder'   => ['deny', 'allow'],      // Same as above
                'accessControl' => [Plugin::instance()->accessControl(), 'control'],
                'acceptedName'  => [Plugin::instance()->accessControl(), 'validateName'],              // Same as above

            ];
        }

        /**
         * @filter fm_options :: Options filter
         * Implementation Example: add_filter('fm_options', array($this, 'fm_options_test'), 10, 1);
         *
         * */
        $filetered_opts = apply_filters('fm_options_filter', $opts);
        if (!empty($filetered_opts['roots'])) {
            $opts = $filetered_opts;
        }
        $elFinder = new elFinderConnector(new elFinder($opts));
        $elFinder->run();
        wp_die();
    }

    public function security_check()
    {
        // Checks if the current user have enough authorization to operate.
        if (!current_user_can('manage_options')) {
            wp_die();
        }
        check_ajax_referer('bfm_nonce', 'bfm_nonce');
    }
}
