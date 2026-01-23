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
        // List view bulk action
        add_filter('bulk_actions-upload', [$this, 'add_bulk_action']);
        add_filter('handle_bulk_actions-upload', [$this, 'handle_bulk_action'], 10, 3);
        add_action('admin_notices', [$this, 'show_admin_notice']);

        // Grid view bulk action
        add_action('admin_enqueue_scripts', [$this, 'enqueue_grid_script']);
        add_action('wp_ajax_runthings_emt_bulk_clear', [$this, 'ajax_bulk_clear']);
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

        return add_query_arg([
            'runthings_emt_cleared' => $cleared_count,
            'runthings_emt_nonce'   => wp_create_nonce('runthings_emt_notice'),
        ], $redirect_url);
    }

    /**
     * Show admin notice after bulk action.
     */
    public function show_admin_notice(): void
    {
        if (!isset($_GET['runthings_emt_cleared'], $_GET['runthings_emt_nonce'])) {
            return;
        }

        if (!wp_verify_nonce(sanitize_key($_GET['runthings_emt_nonce']), 'runthings_emt_notice')) {
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

    /**
     * Enqueue script for grid view bulk action.
     *
     * @param string $hook_suffix The current admin page.
     */
    public function enqueue_grid_script(string $hook_suffix): void
    {
        if ($hook_suffix !== 'upload.php') {
            return;
        }

        wp_enqueue_script(
            'runthings-emt-grid-bulk',
            RUNTHINGS_EMT_URL . 'assets/js/grid-bulk.js',
            ['media-views'],
            RUNTHINGS_EMT_VERSION,
            true
        );

        wp_localize_script('runthings-emt-grid-bulk', 'runthingsEmtBulk', [
            'nonce'        => wp_create_nonce('runthings_emt_bulk_clear'),
            'buttonText'   => __('Clear titles', 'runthings-empty-media-title'),
            'confirmText'  => __('Clear titles for selected media?', 'runthings-empty-media-title'),
            'successText'  => __('Titles cleared successfully.', 'runthings-empty-media-title'),
            'errorText'    => __('Error clearing titles.', 'runthings-empty-media-title'),
        ]);
    }

    /**
     * Handle AJAX bulk clear request from grid view.
     */
    public function ajax_bulk_clear(): void
    {
        check_ajax_referer('runthings_emt_bulk_clear', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => __('Permission denied.', 'runthings-empty-media-title')]);
        }

        $ids = isset($_POST['ids']) ? array_map('intval', (array) $_POST['ids']) : [];

        if (empty($ids)) {
            wp_send_json_error(['message' => __('No items selected.', 'runthings-empty-media-title')]);
        }

        $cleared = 0;
        foreach ($ids as $id) {
            $post = get_post($id);
            if (!$post || $post->post_type !== 'attachment') {
                continue;
            }

            if (!current_user_can('edit_post', $id)) {
                continue;
            }

            $result = wp_update_post([
                'ID'         => $id,
                'post_title' => '',
            ]);

            if ($result && !is_wp_error($result)) {
                $cleared++;
            }
        }

        wp_send_json_success(['cleared' => $cleared]);
    }
}

