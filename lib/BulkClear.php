<?php

namespace Runthings\EmptyMediaTitle;

if (!defined('WPINC')) {
    die;
}

class BulkClear
{
    private const BULK_ACTION = 'runthings_emt_clear_title';

    public function __construct()
    {
        add_filter('bulk_actions-upload', [$this, 'add_bulk_action']);
        add_filter('handle_bulk_actions-upload', [$this, 'handle_bulk_action'], 10, 3);
        add_action('admin_notices', [$this, 'show_admin_notice']);
    }

    /**
     * Add the bulk action to the dropdown.
     *
     * @param array $actions Existing bulk actions.
     * @return array Modified bulk actions.
     */
    public function add_bulk_action(array $actions): array
    {
        $actions[self::BULK_ACTION] = __('Clear media title', 'runthings-empty-media-title');
        return $actions;
    }

    /**
     * Handle the bulk action.
     *
     * @param string $redirect_url The redirect URL.
     * @param string $action       The action being taken.
     * @param array  $post_ids     The items to process.
     * @return string The redirect URL.
     */
    public function handle_bulk_action(string $redirect_url, string $action, array $post_ids): string
    {
        if ($action !== self::BULK_ACTION) {
            return $redirect_url;
        }

        $cleared_count = 0;

        foreach ($post_ids as $post_id) {
            $post = get_post($post_id);
            if (!$post || $post->post_type !== 'attachment') {
                continue;
            }

            $result = wp_update_post([
                'ID'         => $post_id,
                'post_title' => '',
            ]);

            if ($result && !is_wp_error($result)) {
                $cleared_count++;
            }
        }

        return add_query_arg('runthings_emt_cleared', $cleared_count, $redirect_url);
    }

    /**
     * Show admin notice after bulk action.
     */
    public function show_admin_notice(): void
    {
        if (!isset($_GET['runthings_emt_cleared'])) {
            return;
        }

        $count = intval($_GET['runthings_emt_cleared']);

        printf(
            '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
            esc_html(
                sprintf(
                    /* translators: %d: number of media items */
                    _n(
                        'Cleared title for %d media item.',
                        'Cleared titles for %d media items.',
                        $count,
                        'runthings-empty-media-title'
                    ),
                    $count
                )
            )
        );
    }
}

