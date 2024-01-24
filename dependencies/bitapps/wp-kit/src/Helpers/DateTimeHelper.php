<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Dependencies\BitApps\WPKit\Helpers;

use DateTime;
use DateTimeZone;

final class DateTimeHelper
{
    private $_dateFormat;

    private $_timeFormat;

    private $_timezone;

    private $_currentTime;

    private $_currentFormat;

    public function __construct()
    {
        $this->_dateFormat    = get_option('date_format');
        $this->_timeFormat    = get_option('time_format');
        $this->_timezone      = self::wp_timezone();
        $this->_currentTime   = current_time('mysql');
        $this->_currentFormat = 'Y-m-d H:i:s';
    }

    public function getDate($date = null, $currentFormat = null, $currentTZ = null, $expectedFormat = null, $expectedTZ = null)
    {
        if (\is_null($date)) {
            $date          = $this->_currentTime;
            $currentFormat = $this->_currentFormat;
            $currentTZ     = $this->_timezone;
        }

        $currentFormat  = \is_null($currentFormat) ? $this->_currentFormat : $currentFormat;
        $currentTZ      = \is_null($currentTZ) ? $this->_timezone : $currentTZ;
        $expectedFormat = \is_null($expectedFormat) ? $this->_dateFormat : $expectedFormat;
        $expectedTZ     = \is_null($expectedTZ) ? $this->_timezone : $expectedTZ;

        return $this->getFormated($date, $currentFormat, $currentTZ, $expectedFormat, $expectedTZ);
    }

    public function getTime($date = null, $currentFormat = null, $currentTZ = null, $expectedFormat = null, $expectedTZ = null)
    {
        if (\is_null($date)) {
            $date          = $this->_currentTime;
            $currentFormat = $this->_currentFormat;
            $currentTZ     = $this->_timezone;
        }

        $currentFormat  = \is_null($currentFormat) ? $this->_currentFormat : $currentFormat;
        $currentTZ      = \is_null($currentTZ) ? $this->_timezone : $currentTZ;
        $expectedFormat = \is_null($expectedFormat) ? $this->_timeFormat : $expectedFormat;
        $expectedTZ     = \is_null($expectedTZ) ? $this->_timezone : $expectedTZ;

        return $this->getFormated($date, $currentFormat, $currentTZ, $expectedFormat, $expectedTZ);
    }

    public function getDay($nameType, $date = null, $currentFormat = null, $currentTZ = null, $expectedTZ = null)
    {
        if (\is_null($date)) {
            $date          = $this->_currentTime;
            $currentFormat = $this->_currentFormat;
            $currentTZ     = $this->_timezone;
        }

        $currentFormat = \is_null($currentFormat) ? $this->_currentFormat : $currentFormat;
        $currentTZ     = \is_null($currentTZ) ? $this->_timezone : $currentTZ;
        $expectedTZ    = \is_null($expectedTZ) ? $this->_timezone : $expectedTZ;

        switch ($nameType) {
            case 'numeric-with-leading':
                $expectedFormat = 'd';

                break;

            case 'numeric-without-leading':
                $expectedFormat = 'j';

                break;

            case 'short-name':
                $expectedFormat = 'D';

                break;

            case 'full-name':
                $expectedFormat = 'l';

                break;

            default:
                $expectedFormat = 'd';

                break;
        }

        return $this->getFormated($date, $currentFormat, $currentTZ, $expectedFormat, $expectedTZ);
    }

    public function getMonth($nameType, $date = null, $currentFormat = null, $currentTZ = null, $expectedTZ = null)
    {
        if (\is_null($date)) {
            $date          = $this->_currentTime;
            $currentFormat = $this->_currentFormat;
            $currentTZ     = $this->_timezone;
        }

        $currentFormat = \is_null($currentFormat) ? $this->_currentFormat : $currentFormat;
        $currentTZ     = \is_null($currentTZ) ? $this->_timezone : $currentTZ;
        $expectedTZ    = \is_null($expectedTZ) ? $this->_timezone : $expectedTZ;

        switch ($nameType) {
            case 'numeric-with-leading':
                $expectedFormat = 'm';

                break;

            case 'numeric-without-leading':
                $expectedFormat = 'n';

                break;

            case 'short-name':
                $expectedFormat = 'M';

                break;

            case 'full-name':
                $expectedFormat = 'F';

                break;

            default:
                $expectedFormat = 'd';

                break;
        }

        return $this->getFormated($date, $currentFormat, $currentTZ, $expectedFormat, $expectedTZ);
    }

    public function getFormated($dateString, $currentFormat, $currentTZ, $expectedFormat, $expectedTZ)
    {
        if ($currentFormat === false) {
            $dateObject = new DateTime($dateString, $currentTZ);
        } else {
            $dateObject = DateTime::createFromFormat($currentFormat, $dateString, $currentTZ);
        }

        if (!\is_null($expectedTZ)) {
            $dateObject->setTimezone($expectedTZ);
        }

        if ($dateObject) {
            return $dateObject->format($expectedFormat);
        }

        return false;
    }

    public function getUnicodeLikeFormat($type, $format = null)
    {
        $type = strtolower($type);

        switch ($type) {
            case 'date':
                $format = \is_null($format) ? $this->_dateFormat : $format;

                break;

            case 'time':
                $format = \is_null($format) ? $this->_timeFormat : $format;

                break;

            case 'timestamp':
                $format = \is_null($format) ? $this->_currentFormat : $format;

                break;

            default:
                break;
        }

        if (strpos($format, 'd') !== false) {
            $format = str_replace('d', 'dd', $format);
        }

        if (strpos($format, 'j') !== false) {
            $format = str_replace('j', 'd', $format);
        }

        if (strpos($format, 'D') !== false) {
            $format = str_replace('D', 'eee', $format);
        }

        if (strpos($format, 'I') !== false) {
            $format = str_replace('I', 'eeee', $format);
        }

        if (strpos($format, 'S') !== false) {
            $format = str_replace('S', 'F', $format);
        }

        if (strpos($format, 'M') !== false) {
            $format = str_replace('M', 'MMM', $format);
        }

        if (strpos($format, 'F') !== false) {
            $format = str_replace('F', 'MMMM', $format);
        }

        if (strpos($format, 'm') !== false) {
            $format = str_replace('m', 'MM', $format);
        }

        if (strpos($format, 'n') !== false) {
            $format = str_replace('n', 'M', $format);
        }

        if (strpos($format, 'y') !== false) {
            $format = str_replace('y', 'yy', $format);
        }

        if (strpos($format, 'Y') !== false) {
            $format = str_replace('Y', 'yyyy', $format);
        }

        if (strpos($format, 'a') !== false) {
            $format = str_replace('a', 'aaaa', $format);
        }

        if (strpos($format, 'A') !== false) {
            $format = str_replace('A', 'aaaa', $format);
        }

        if (strpos($format, 'g') !== false) {
            $format = str_replace('g', 'h', $format);
        }

        if (strpos($format, 'G') !== false) {
            $format = str_replace('G', 'H', $format);
        }

        if (strpos($format, 'h') !== false) {
            $format = str_replace('h', 'hh', $format);
        }

        if (strpos($format, 'H') !== false) {
            $format = str_replace('H', 'HH', $format);
        }

        if (strpos($format, 'i') !== false) {
            $format = str_replace('i', 'mm', $format);
        }

        if (strpos($format, 's') !== false) {
            $format = str_replace('s', 'ss', $format);
        }

        return $format;
    }

    public function getUnicodeToPhpFormat($type, $format = null)
    {
        $type = strtolower($type);

        switch ($type) {
            case 'date':
                $format = \is_null($format) ? $this->_dateFormat : $format;

                break;

            case 'time':
                $format = \is_null($format) ? $this->_timeFormat : $format;

                break;

            case 'timestamp':
                $format = \is_null($format) ? $this->_currentFormat : $format;

                break;

            default:
                break;
        }

        if (strpos($format, 'd') !== false) {
            $format = str_replace('dd', 'd', $format);
        }

        if (strpos($format, 'E') !== false) {
            $format = str_replace('E', 'D', $format);
        }

        if (strpos($format, 'MMMM') !== false) {
            $format = str_replace('MMMM', 'F', $format);
        } elseif (strpos($format, 'MMM') !== false) {
            $format = str_replace('MMM', 'M', $format);
        } elseif (strpos($format, 'MM') !== false) {
            $format = str_replace('MM', 'm', $format);
        }

        if (strpos($format, 'yyyy') !== false) {
            $format = str_replace('yyyy', 'Y', $format);
        } elseif (strpos($format, 'yy') !== false) {
            $format = str_replace('yy', 'y', $format);
        }

        return $format;
    }

    public static function wp_timezone_string()
    {
        if (\function_exists('wp_timezone_string')) {
            return wp_timezone_string();
        }

        $timezoneString = get_option('timezone_string');

        if ($timezoneString) {
            return $timezoneString;
        }

        $offset  = (float) get_option('gmt_offset');
        $hours   = (int) $offset;
        $minutes = ($offset - $hours);

        $sign    = ($offset < 0) ? '-' : '+';
        $absHour = abs($hours);
        $absMins = abs($minutes * 60);

        return sprintf('%s%02d:%02d', $sign, $absHour, $absMins);
    }

    public static function wp_timezone()
    {
        if (\function_exists('wp_timezone')) {
            return wp_timezone();
        }

        return new DateTimeZone(self::wp_timezone_string());
    }
}
