<?php

namespace BitApps\FM\Providers\FileManager;

\defined('ABSPATH') or exit();

use BitApps\FM\Providers\FileManager\Options as FinderOptions;
use elFinder;
use elFinderConnector;

class FileManagerProvider
{
    /**
     * Options for elFinder
     *
     * @var FinderOptions
     */
    private $_finderOptions;

    public function __construct(FinderOptions $finderOptions)
    {
        $this->_finderOptions = $finderOptions;
    }

    public function getFinder()
    {
        return new elFinderConnector(new elFinder($this->_finderOptions->getOptions()));
    }
}
