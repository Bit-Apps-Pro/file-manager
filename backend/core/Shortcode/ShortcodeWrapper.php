<?php

namespace BitApps\FM\Core\Shortcode;

/**
 * A wrapper class for actions and filters.
 */
final class ShortcodeWrapper
{
    /**
     * A wrapper for do_shortcode().
     *
     * @param string $content    Content to search for shortcodes.
     * @param bool   $ignoreHtml When true, shortcodes inside HTML elements will be skipped.
     *                           Default false.
     *
     * @return string Content with shortcodes filtered out.
     */
    public function doShortcode($content, $ignoreHtml = false)
    {
        do_shortcode($content, $ignoreHtml);
    }

    /**
     * A wrapper for add_shortcode().
     *
     * @param string   $tag      Shortcode tag to be searched in post content.
     * @param callable $callback The callback function to run when the shortcode is found.
     *                           Every shortcode callback is passed three parameters by default,
     *                           including an array of attributes (`$atts`), the shortcode content
     *                           or null if not set (`$content`), and finally the shortcode tag
     *                           itself (`$shortcode_tag`), in that order.
     *
     * @return void
     */
    public function addShortcode($tag, $callback)
    {
        add_shortcode($tag, $callback);
    }

    /**
     * A wrapper for remove_shortcode().
     *
     * @param string $tag Shortcode tag to remove hook for.
     *
     * @return void
     */
    public function removeShortcode($tag)
    {
        remove_shortcode($tag);
    }

    /**
     * A wrapper for shortcode_exists().
     *
     * @param string $tag Shortcode tag to check.
     *
     * @return bool Whether the given shortcode exists.
     */
    public function shortcodeExists($tag)
    {
        return shortcode_exists($tag);
    }

    /**
     * A wrapper for has_shortcode().
     *
     * @param string $content Content to search for shortcodes.
     * @param string $tag     Shortcode tag to check.
     *
     * @return bool Whether the passed content contains the given shortcode.
     */
    public function hasShortcode($content, $tag)
    {
        return has_shortcode($content, $tag);
    }
}
