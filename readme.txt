=== WP Logout Redirect ===
Contributors: HasinHayder
Tags: settings, admin, redirection, security, accessibility
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.2
Stable tag: 2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple yet powerful plugin that redirects users to a custom URL after logout. Featuring a modern, accessible admin panel with dark mode support.

== Description ==

WP Logout Redirect allows you to control where users are redirected after logging out of your WordPress site. By default, WordPress redirects users to the login page, but this plugin gives you full control over the logout experience.

Version 2.1 brings a complete UI overhaul, offering a refined, accessible, and developer-friendly experience.

= Features =

* **Modern Admin Panel** - A refined, editorial-style interface for easy configuration.
* **Dark Mode Support** - Automatically adapts to your system's color scheme.
* **Accessible Design** - Fully optimized for screen readers and keyboard navigation.
* **Quick Presets** - One-click settings for Homepage or Login Page redirects.
* **Page Selector** - Easily pick any of your existing pages from a dropdown.
* **Developer Friendly** - Programmatically override settings using the `wplr_home_url` filter hook.
* **Lightweight & Secure** - Built with performance and WordPress security standards in mind.

= Usage =

After activation, go to **Settings → Logout Redirect** in your WordPress admin panel.

1.  **Manual URL**: Enter any custom URL in the input field.
2.  **Page Selector**: Choose an existing page from the dropdown to automatically fill the URL.
3.  **Presets**: Use the "Quick Presets" buttons for common destinations.
4.  **Save**: Click "Save Configuration" to apply changes.

= Developer Hook =

Developers can programmatically override the redirect URL using the `wplr_home_url` filter hook. This takes precedence over admin settings.

`
add_filter('wplr_home_url', function($url) {
    return 'https://example.com/custom-destination';
});
`

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wp-logout-redirect` directory, or install through the WordPress plugins screen.
2. Activate the plugin.
3. Navigate to **Settings → Logout Redirect** to configure your destination.

== Frequently Asked Questions ==

= Does it support dark mode? =
Yes! The admin interface automatically detects your system settings and switches to a refined dark theme.

= Is it accessible? =
Absolutely. We've implemented ARIA labels, screen-reader-specific text, and proper focus states to ensure a great experience for everyone.

= What happens if I leave the URL empty? =
If the redirect URL is empty, the plugin will default to your site's homepage.

= Can I redirect to an external site? =
Yes, you can enter any valid absolute URL.

== Screenshots ==

1. The modern, editorial-style settings panel.
2. Dark mode interface for low-light environments.
3. Quick presets and page selector in action.

== Changelog ==

= 2.1 =
* Complete UI/UX overhaul with a refined "Editorial" minimalist design.
* Added native Dark Mode support.
* Improved Accessibility with ARIA labels and screen-reader support.
* Added Quick Presets for Homepage and Login page.
* Integrated Page Selector dropdown for easier configuration.
* Robust copy-to-clipboard functionality for developer hooks.
* Updated requirements to WordPress 5.0+ and PHP 7.2+.

= 2.0 =
* Major internal refactoring and performance optimizations.
* Improved security using `wp_safe_redirect()`.

= 1.3 =
* Added admin settings page under Settings → Logout Redirect.
* Added URL validation and error messaging.

= 1.0 =
* Initial Release.

== Upgrade Notice ==

= 2.1 =
This update introduces a beautiful new admin interface with dark mode support and significantly improved accessibility. Highly recommended for a better management experience.