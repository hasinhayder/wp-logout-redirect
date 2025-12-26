# WP Logout Redirect

[![WordPress Plugin](https://img.shields.io/wordpress/v/wp-logout-redirect.svg)](https://wordpress.org/plugins/wp-logout-redirect/)
[![License](https://img.shields.io/badge/license-GPL--2.0-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

A simple yet powerful WordPress plugin that redirects users to a custom URL after logout. Featuring a modern, accessible admin panel with dark mode support.

## Features

- **Modern Admin UI**: A refined, editorial-style minimalist interface.
- **Dark Mode**: Native support that automatically adapts to your system preferences.
- **Accessibility**: Optimized for screen readers and keyboard navigation (ARIA compliant).
- **Quick Presets**: One-click configuration for Homepage or Login page redirects.
- **Page Selector**: Select any published WordPress page from a convenient dropdown.
- **Developer Hooks**: Easily override the redirect URL programmatically.
- **Lightweight**: Zero bloat, fast performance, and secure code.

## Installation

1.  Download the plugin and extract it to `/wp-content/plugins/wp-logout-redirect`.
2.  Activate the plugin through the **Plugins** menu in WordPress.
3.  Go to **Settings > Logout Redirect** to configure your settings.

## Developer Usage

You can programmatically override the redirect URL using the `wplr_home_url` filter hook. This is useful for conditional redirects based on user roles or other logic.

```php
/**
 * Override the logout redirect URL
 */
add_filter('wplr_home_url', function($url) {
    // Custom logic here
    if (is_user_logged_in() && current_user_can('administrator')) {
        return admin_url();
    }
    
    return 'https://example.com/goodbye';
});
```

*Note: The filter hook takes precedence over the admin panel settings.*

## Preview

The plugin features a beautiful, responsive admin interface designed to blend seamlessly with the modern WordPress experience while providing an enhanced "Editorial" aesthetic.

## License

This project is licensed under the GPLv2 or later License - see the [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) file for details.

---
Created by [Hasin Hayder](https://github.com/hasinhayder)