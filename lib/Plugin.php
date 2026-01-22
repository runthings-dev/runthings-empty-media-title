<?php

namespace Runthings\EmptyMediaTitle;

if (!defined('WPINC')) {
    die;
}

class Plugin
{
    public function __construct()
    {
        add_filter('wp_insert_attachment_data', [$this, 'clear_media_title'], 10, 4);
    }

    /**
     * Clear the image attachment's title on new uploads.
     *
     * @param array $data                Attachment post data.
     * @param array $postarr             Sanitized post data.
     * @param array $unsanitized_postarr Unsanitized post data.
     * @param bool  $update              Whether this is an update.
     * @return array Modified attachment data.
     */
    public function clear_media_title(array $data, array $postarr, array $unsanitized_postarr, bool $update): array
    {
        if ($update) {
            return $data;
        }

        $mime = $postarr['post_mime_type'] ?? '';
        if ($mime === '' || !wp_match_mime_types('image', $mime)) {
            return $data;
        }

        /**
         * Filter to skip clearing the media title.
         *
         * Return true to skip clearing the title for this attachment.
         *
         * @param bool  $skip                 Whether to skip. Default false.
         * @param array $data                 Attachment post data.
         * @param array $postarr              Sanitized post data.
         * @param array $unsanitized_postarr  Unsanitized post data.
         * @param bool  $update               Whether this is an update.
         */
        $skip = apply_filters('runthings_emt_skip', false, $data, $postarr, $unsanitized_postarr, $update);

        if ($skip) {
            return $data;
        }

        $data['post_title'] = '';

        return $data;
    }
}

