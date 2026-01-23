=== Clear Media Title On Upload ===
Contributors: runthingsdev
Tags: media, images, title, upload, attachment
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 2.0.0
Requires PHP: 7.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Clears the media title field on upload, instead of setting it to the slugified filename.

== Description ==

When you upload media to WordPress, the title field is automatically set to a slugified version of the filename. This often results in messy, unoptimized titles like "IMG-20240115-photo-final-v2" appearing in gallery captions, alt tags, or title attributes.

This plugin clears the title field for newly uploaded media, ensuring that if a title is displayed anywhere, it's one you've intentionally set yourself.

= Features =

* Automatically clears the title field for new media uploads
* Bulk action to clear titles on existing media library items
* Lightweight with no settings page required
* Developer-friendly with filters

= How It Works =

The plugin hooks into WordPress's `wp_insert_attachment_data` filter and clears the `post_title` field for new uploads before they are saved to the database.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/runthings-empty-media-title` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. That's it! New media uploads will now have an empty title field.

== Frequently Asked Questions ==

= Does this affect existing media in my library? =

New uploads are cleared automatically. For existing media, you can use the bulk action in the Media Library to clear titles on selected items.

= Can I restrict this to certain file types? =

Yes! Use the `runthings_emt_mime_types` filter to limit which MIME types are affected. See the Filters section below.

= Can I exclude certain uploads from having their title cleared? =

Yes! Use the `runthings_emt_skip` filter to conditionally skip clearing the title. See the Filters section below.

= Why would I want an empty title? =

Many themes and plugins display the media title in various places - gallery captions, lightbox overlays, or HTML title attributes. An auto-generated slugified filename like "DSC-0042-final-edit" looks unprofessional. With an empty title, you're in control of what gets displayed.

== Screenshots ==

1. Before: Image uploaded with auto-generated slugified title. After: Image uploaded with empty title field.

== Changelog ==

= 2.0.0 - 23rd January 2026 =
* Complete plugin refactor with namespaced class structure
* Added bulk action "Clear media title" to clear titles on existing media items
* Changed default from `['image']` to `[]` (all media types)
* Added `runthings_emt_skip` filter for developer customization
* Added `runthings_emt_mime_types` filter to extend beyond images

= 1.0.0 - 6th January 2022 =
* Initial release

== Upgrade Notice ==

= 2.0.0 =
Added bulk action, and added filters.

== Filters ==

= runthings_emt_skip =

Skip clearing the title for specific attachments.

**Parameters:**

* `$skip` (bool) - Whether to skip clearing the title. Default false.
* `$data` (array) - Attachment post data (slashed, sanitized, processed).
* `$postarr` (array) - Sanitized post data (not processed).
* `$unsanitized_postarr` (array) - Unsanitized post data.
* `$update` (bool) - Whether this is an update (always false for new uploads).

**Example:**

`
// Skip clearing title for uploads by specific user
add_filter('runthings_emt_skip', function($skip, $data, $postarr) {
    if (get_current_user_id() === 1) {
        return true; // Admin keeps auto-generated titles
    }
    return $skip;
}, 10, 3);
`

`
// Skip clearing title if filename contains "keep-title"
add_filter('runthings_emt_skip', function($skip, $data, $postarr) {
    $filename = $postarr['post_title'] ?? '';
    if (strpos($filename, 'keep-title') !== false) {
        return true;
    }
    return $skip;
}, 10, 3);
`

= runthings_emt_mime_types =

Filter which MIME types should have their title cleared. By default, all media types are affected.

**Parameters:**

* `$allowed_mime_types` (array) - Array of MIME type patterns. Default `[]` (all types).
* `$attachment_mime_type` (string) - The attachment's MIME type.
* `$postarr` (array) - Sanitized post data.

**Example:**

`
// Restrict to images only
add_filter('runthings_emt_mime_types', function($allowed_mime_types) {
    return ['image'];
});
`

`
// Restrict to images and PDFs only
add_filter('runthings_emt_mime_types', function($allowed_mime_types) {
    return ['image', 'application/pdf'];
});
`

== Additional Notes ==

Built by Matthew Harris of runthings.dev, copyright 2022-2026.

Visit [runthings.dev](https://runthings.dev/) for more WordPress plugins and resources.

Contribute or report issues at the [GitHub repository](https://github.com/runthings-dev/runthings-empty-media-title).

Insert space by Xinh Studio from Noun Project, https://thenounproject.com/browse/icons/term/insert-space/ (CC BY 3.0) - https://thenounproject.com/icon/insert-space-239543/

Image by David Khai from Noun Project, https://thenounproject.com/browse/icons/term/image/ (CC BY 3.0) - https://thenounproject.com/icon/image-661355/
