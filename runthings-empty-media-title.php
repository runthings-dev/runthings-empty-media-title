<?php

/**
 * Plugin Name: Clear Media Title On Upload
 * Plugin URI: https://runthings.dev/wordpress-plugins/empty-media-title/
 * Description: Clears the image title attribute field on upload, instead of setting it to the slugified filename.
 * Version: 1.0.0
 * Author: runthingsdev
 * Author URI: https://runthings.dev/
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: runthings-empty-media-title
 */

namespace Runthings\EmptyMediaTitle;

if (!defined('WPINC')) {
    die;
}

define('RUNTHINGS_EMT_VERSION', '1.0.0');
define('RUNTHINGS_EMT_URL', plugin_dir_url(__FILE__));
define('RUNTHINGS_EMT_DIR', plugin_dir_path(__FILE__));

class Bootstrap
{
    public function __construct()
    {
        add_action('plugins_loaded', [$this, 'init']);
    }

    public function init(): void
    {
        require_once RUNTHINGS_EMT_DIR . 'lib/Plugin.php';
        require_once RUNTHINGS_EMT_DIR . 'lib/BulkClear.php';

        new \Runthings\EmptyMediaTitle\Plugin();
        new \Runthings\EmptyMediaTitle\BulkClear();
    }
}

new Bootstrap();