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
     * Clear the attachment's title on new uploads.
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

        $attachment_mime_type = $postarr['post_mime_type'] ?? '';
        if ($attachment_mime_type === '') {
            return $data;
        }

        /**
         * Filter the MIME types that should have their title cleared.
         *
         * By default, all media types are affected. Return an array of MIME type
         * patterns to restrict (e.g., ['image'] for images only).
         * Uses wp_match_mime_types() for matching.
         *
         * @param array  $allowed_mime_types   Array of MIME type patterns. Default [] (all types).
         * @param string $attachment_mime_type The attachment's MIME type.
         * @param array  $postarr              Sanitized post data.
         */
        $allowed_mime_types = apply_filters('runthings_emt_mime_types', [], $attachment_mime_type, $postarr);

        // Non-empty array means restrict to those MIME types only
        if (!empty($allowed_mime_types) && empty(wp_match_mime_types($allowed_mime_types, $attachment_mime_type))) {
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

