<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Config;

\defined('ABSPATH') or exit();

class ReviewProvider
{
    const NOT_YET = 'not-asked-yet'; // 0. Not asked yet

    const SUCCESSFUL = 'review-successful'; // 1. Asked and clicked review

    const REMIND = 'remind-me-later'; // 2. Ask me later

    const NOT_INTERESTED = 'not-interested'; // 3. Not interested to review.

    public static $status = [
        'not-asked-yet',
        'review-successful',
        'remind-me-later',
        'not-interested',
    ];

    // Get the current status of the review.
    public function getStatus()
    {
        // Check if any update is saved
        return Config::getOption('notify_review', [self::NOT_YET]);
    }

    public function setStatus($status, $time)
    {
        Config::updateOption('notify_review', [$status, $time], true);
    }

    public function isShowAble()
    {
        $isShowAble = true;

        $status = $this->getStatus();

        if ($status[0] == self::NOT_INTERESTED
        && time() < ((int) $status[1] + (7 * 24 * 60 * 60))) {
            $isShowAble = false;
        }

        if ($status[0] == self::REMIND
         && time() < ((int) $status[1] + (24 * 60 * 60))) {
            $isShowAble = false;
        }

        if ($status[0] == self::SUCCESSFUL) {
            $isShowAble = false;
        }

        return $isShowAble;
    }
}
