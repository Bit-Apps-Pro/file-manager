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
        if ($command === 'upload') {
            $commandDetails = $this->processFileHashForUpload($target, $volume);
        } else {
            $commandDetails = $this->processFileHash($command, $target, $volume);
        }

        $this->_logger->save($command, $commandDetails);
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

    private function processFileHashForUpload($target, $volume)
    {
        $details = [];
        if (!empty($target['upload_path'])) {
            $details['driver']  = \get_class($volume);
            $details['folder']  = [
                'path' => str_replace(ABSPATH, '', $volume->getPath($target['target'])),
                'hash' => $target['target'],
            ];
            foreach ($target['upload_path'] as $index => $file) {
                $details['files'][] = [
                    'path' => str_replace(ABSPATH, '', $volume->getPath($file)),
                    'hash' => $file,
                ];
                if ($index > 300) {
                    break;
                }
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
