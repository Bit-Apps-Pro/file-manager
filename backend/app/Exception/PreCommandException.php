<?php

namespace BitApps\FM\Exception;

use Exception;

\defined('ABSPATH') or exit();

class PreCommandException extends Exception
{
    public function getError()
    {
        return [
            'preventexec' => true,
            'results'     => [
                'error' => $this->getMessage(),
            ]
        ];
    }
}
