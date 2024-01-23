<?php
/**
 * @license MIT
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Dependencies\BitApps\WPValidator;

trait SanitizationMethods
{
    protected function sanitizeEmail($value, $params = [])
    {
        return sanitize_email($value);
    }

    protected function sanitizeFileName($value, $params = [])
    {
        return sanitize_file_name($value, $params = []);
    }

    protected function sanitizeKey($value, $params = [])
    {
        return sanitize_key($value);
    }

    // protected function sanitizeMeta($value)
    // {
    //     return sanitize_meta($value);
    // }

    protected function sanitizeHtmlClass($value, $params = [])
    {
        return sanitize_html_class($value);
    }

    // protected function sanitizeOption($value)
    // {
    //     return sanitize_option($value);
    // }
    // protected function sanitizeTermField($value)
    // {
    //     return sanitize_term_field($value);
    // }

    protected function sanitizeText($value, $params = [])
    {
        return sanitize_text_field($value);
    }

    protected function sanitizeTitle($value, $params = [])
    {
        return sanitize_title($value);
    }

    protected function sanitizeUser($value, $params = [])
    {
        return sanitize_user($value);
    }

    protected function sanitizeUrl($value, $params = [])
    {
        return esc_url_raw($value);
    }

    protected function sanitizeTrim($value, $params = [])
    {
        return is_string($value) ? trim($value) : $value;
    }

    protected function sanitizeEscape($value, $params = [])
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    protected function sanitizeCapitalize($value, $params = [])
    {
        return ucwords(strtolower($value));
    }

    protected function sanitizeLowercase($value)
    {
        return strtolower($value);
    }

    protected function sanitizeUppercase($value)
    {
        return strtoupper($value);
    }

    protected function sanitizeUcfirst($value)
    {
        return ucfirst($value);
    }
}
