# Clear Media Title On Upload

Clears the image title attribute field on upload, instead of setting it to the slugified filename.

## Description

When you upload an image to WordPress, the title field is automatically set to a slugified version of the filename. This often results in messy, unoptimized titles like "IMG-20240115-photo-final-v2" appearing in gallery captions, alt tags, or title attributes.

This plugin clears the title field for newly uploaded images, ensuring that if a title is displayed anywhere, it's one you've intentionally set yourself.

### Features

- Automatically clears the title field for new image uploads
- Only affects new uploads - existing media library items are unchanged
- Lightweight with no settings page required
- Developer-friendly with a filter for custom exceptions

### How It Works

The plugin hooks into WordPress's `wp_insert_attachment_data` filter and clears the `post_title` field for new image uploads before they are saved to the database.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/runthings-empty-media-title` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. That's it! New image uploads will now have an empty title field.

## Frequently Asked Questions

### Does this affect existing images in my media library?

No, this plugin only affects new uploads. Existing images retain their current titles.

### Does this work for all file types?

No, this plugin only clears titles for image files (JPEG, PNG, GIF, WebP, etc.). Other attachment types like PDFs, videos, and audio files are unaffected.

### Can I exclude certain uploads from having their title cleared?

Yes! Use the `runthings_emt_skip` filter to conditionally skip clearing the title. See the Filters section below.

### Why would I want an empty title?

Many themes and plugins display the image title in various places - gallery captions, lightbox overlays, or HTML title attributes. An auto-generated slugified filename like "DSC-0042-final-edit" looks unprofessional. With an empty title, you're in control of what gets displayed.

## Screenshots

### Before and After
![Before: Image uploaded with auto-generated slugified title. After: Image uploaded with empty title field.](https://raw.githubusercontent.com/runthings-dev/runthings-empty-media-title/master/.wordpress-org/screenshot-1.jpg)

## Filters

### runthings_emt_skip

Skip clearing the title for specific attachments.

**Parameters:**

- `$skip` (bool) - Whether to skip clearing the title. Default false.
- `$data` (array) - Attachment post data (slashed, sanitized, processed).
- `$postarr` (array) - Sanitized post data (not processed).
- `$unsanitized_postarr` (array) - Unsanitized post data.
- `$update` (bool) - Whether this is an update (always false for new uploads).

**Example - Skip for specific user:**

```php
add_filter('runthings_emt_skip', function($skip, $data, $postarr) {
    if (get_current_user_id() === 1) {
        return true; // Admin keeps auto-generated titles
    }
    return $skip;
}, 10, 3);
```

**Example - Skip if filename contains keyword:**

```php
add_filter('runthings_emt_skip', function($skip, $data, $postarr) {
    $filename = $postarr['post_title'] ?? '';
    if (strpos($filename, 'keep-title') !== false) {
        return true;
    }
    return $skip;
}, 10, 3);
```

### runthings_emt_mime_types

Filter which MIME types should have their title cleared. By default, only images are affected. Return an empty array to match all MIME types.

**Parameters:**

- `$allowed_mime_types` (array) - Array of MIME type patterns. Default `['image']`. Empty array matches all.
- `$attachment_mime_type` (string) - The attachment's MIME type.
- `$postarr` (array) - Sanitized post data.

**Example - Clear title for all media types:**

```php
add_filter('runthings_emt_mime_types', '__return_empty_array');
```

**Example - Clear title for images and PDFs only:**

```php
add_filter('runthings_emt_mime_types', function($allowed_mime_types) {
    return ['image', 'application/pdf'];
});
```

## Changelog

### 2.0.0
- Complete plugin refactor with namespaced class structure
- Added `runthings_emt_skip` filter for developer customization
- Added `runthings_emt_mime_types` filter to extend beyond images

### 1.0.0 - 6th January 2022
- Initial release

## License

This plugin is licensed under GPL v3 or later, and is free to use on personal and commercial projects.

## Author

Built by Matthew Harris of runthings.dev, copyright 2022-2026.

Visit [runthings.dev](https://runthings.dev/) for more WordPress plugins and resources.

Contribute or report issues at the [GitHub repository](https://github.com/runthings-dev/runthings-empty-media-title).

Insert space by Xinh Studio from [Noun Project](https://thenounproject.com/browse/icons/term/insert-space/) (CC BY 3.0) - https://thenounproject.com/icon/insert-space-239543/

Image by David Khai from [Noun Project](https://thenounproject.com/browse/icons/term/image/) (CC BY 3.0) - https://thenounproject.com/icon/image-661355/
