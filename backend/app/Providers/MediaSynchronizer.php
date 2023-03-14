<?php

namespace BitApps\FM\Providers;

\defined('ABSPATH') or exit();
class MediaSynchronizer
{
    public $wp_upload_directory;

    function __construct()
    {
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $this->wp_upload_directory = wp_upload_dir();
        $this->wp_upload_directory = $this->wp_upload_directory['path'];
    }

    // Triggers when a file is uploaded and initiates the uploading process for single or batch files.
    public function onFileUpload($cmd, &$result, $args, $elfinder, $volume)
    {
        $targetPath = $volume->getPath($args['target']);

        if (strpos($targetPath, $this->wp_upload_directory) !== false) {
            $images = [];
            for ($file = 0; $file < \count($args['FILES']['upload']['name']); $file++) {
                $images[] = [
                    'name' => $args['FILES']['upload']['name'][$file],
                    'type' => wp_check_filetype($args['FILES']['upload']['name'][$file], null),
                    'path' => trailingslashit($targetPath) . $args['FILES']['upload']['name'][$file],
                    'url'  => $this->abs_path_to_url(
                        trailingslashit($targetPath)
                         . $args['FILES']['upload']['name'][$file]
                    ),
                ];
            }

            $this->add_media($images);
        }
    }

    private function add_media($images)
    {
        foreach ($images as $image) {
            $attachment = [
                'post_mime_type' => $image['type']['type'],
                'post_title'     => sanitize_file_name($image['name']),
            ];
            $attachmentId  = wp_insert_attachment($attachment, $image['path']);
            $attachData    = wp_generate_attachment_metadata($attachmentId, $image['path']);
            wp_update_attachment_metadata($attachmentId, $attachData);
        }
    }

    private function abs_path_to_url($path = '')
    {
        $url = str_replace(
            wp_normalize_path(untrailingslashit(ABSPATH)),
            site_url(),
            wp_normalize_path($path)
        );

        return esc_url_raw($url);
    }
}
