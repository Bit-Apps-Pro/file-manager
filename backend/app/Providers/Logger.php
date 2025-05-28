<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Http\Services\LogService;
use elFinder;
use elFinderVolumeDriver;
use elFinderVolumeLocalFileSystem;

\defined('ABSPATH') || exit();

class Logger
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = new LogService();
    }

    /**
     * Process data for [zipdl file rename get put upload]
     * commands in order to get formatted data for log service.
     *
     * @param string                                               $command
     * @param array                                                $target
     * @param elFinder                                             $finder
     * @param elFinderVolumeDriver | elFinderVolumeLocalFileSystem $volume
     *
     * @return void
     */
    public function log($command, $target, $finder, $volume)
    {
        if (
            isset($target['targets'], $target['targets'][3])
            && $target['targets'][3] === 'application/zip'
        ) {
            return;
        }

        /**
         * Commands to log
         * download: zipdl,file
         * rename: rename
         * view: get
         * update: put
         */
        $commandDetails = [];
        $commandDetails = $this->processFileHash($command, $target, $volume);

        if (isset($commandDetails['files'])) {
            $this->_logger->save($command, $commandDetails);
        }
    }
    
    public function logUpload($command, $status, $target, $finder, $volume)
    {
        $commandDetails = [];
            $commandDetails = $this->processFileHashForUpload($target, $volume);
       
        if (isset($commandDetails['files'])) {
            $this->_logger->save($command, $commandDetails);
        }
    }

    /**
     * Process targeted file hash to path
     *
     * @param string                                               $command
     * @param array                                                $target
     * @param elFinderVolumeDriver | elFinderVolumeLocalFileSystem $volume
     *
     * @return void
     */
    private function processFileHash($command, $target, $volume)
    {
        $details            = [];
        $details['driver']  = \get_class($volume);
        if (!empty($target['targets'])) {
            foreach ($target['targets'] as $file) {
                $details['files'][] = [
                    'path' => str_replace(ABSPATH, '', $volume->getPath($file)),
                    'hash' => $file,
                ];
            }
        } elseif (!empty($target['target'])) {
            $details['files'][] = [
                'path' => str_replace(ABSPATH, '', $volume->getPath($target['target'])),
                'hash' => $target['target'],
            ];
        }

        return $details;
    }

    /**
     * Process targeted file hash to path for upload command
     *
     * @param array                                                $target
     * @param elFinderVolumeDriver | elFinderVolumeLocalFileSystem $volume
     *
     * @return array
     */
    private function processFileHashForUpload($target, $volume)
    {
        if(!empty($target['chunk'])) {
            return [];
        }
        $details['driver']  = \get_class($volume);
        $details['folder']  = [
            'path' => str_replace(ABSPATH, '', $volume->getPath($target['target'])),
            'hash' => $target['target'],
        ];

        if (!empty($target['upload_path'])) {
            foreach ($target['upload_path'] as $index => $file) {
                $details['files'][] = [
                    'path' => str_replace(ABSPATH, '', $volume->getPath($file)),
                    'hash' => $file,
                ];
                if ($index > 300) {
                    break;
                }
            }
        } else if (isset($target["FILES"]["upload"]["full_path"])) {
            $uploadBase = $details['folder']['path'];
            $files = $target["FILES"]["upload"]["full_path"];
            foreach ($files as $index => $file) {
                if ($index > 300 || $file === 'blob') {
                    break;
                }
                $details['files'][] = [
                    'path' => $uploadBase . DIRECTORY_SEPARATOR . $file,
                    'hash' => '',
                ];
            }
            
        }

        return $details;
    }

    private function processFileHashForZipDL($target, $volume)
    {
        $details = [];
        if (!empty($target['targets'])) {
            $details['driver']  = \get_class($volume);
            foreach ($target['targets'] as $file) {
                $details['files'][] = [
                    'path' => str_replace(ABSPATH, '', $volume->getPath($file)),
                    'hash' => $file,
                ];
            }
        }

        return $details;
    }
}
