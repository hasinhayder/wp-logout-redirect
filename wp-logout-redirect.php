<?php
/*
Plugin Name: WP Logout Redirect
Plugin URI: https://wordpress.org/plugins/wp-logout-redirect/
Description: This plugin will take users directly to the home page after logout.
Version: 2.0
Author: Hasin Hayder
Author URI: https://github.com/hasinhayder
License: GPLv2 or later
Text Domain: wp-logout-redirect
Domain Path: /languages
*/

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

/**
 * WP Logout Redirect Class
 */
class WP_Logout_Redirect {
	
	/**
	 * Option name for storing redirect URL
	 */
	const OPTION_NAME = 'wplr_redirect_url';
	
	/**
	 * Initialize the plugin
	 */
	public static function init() {
		add_action('wp_logout', array(__CLASS__, 'handle_logout'));
		add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
		add_action('admin_init', array(__CLASS__, 'register_settings'));
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_assets'));
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(__CLASS__, 'add_settings_link'));
	}

	/**
	 * Enqueue admin assets
	 */
	public static function enqueue_admin_assets($hook) {
		if ('settings_page_wp-logout-redirect' !== $hook) {
			return;
		}

		wp_enqueue_style(
			'wplr-admin-style',
			plugins_url('admin-style.css', __FILE__),
			array(),
			'2.0.',
		);

		wp_enqueue_script(
			'wplr-admin-script',
			plugins_url('admin-script.js', __FILE__),
			array(),
			'2.0.', 
			true
		);

		wp_localize_script('wplr-admin-script', 'wplrData', array(
			'saving' => __('Saving...', 'wp-logout-redirect'),
			'saved' => __('Saved!', 'wp-logout-redirect'),
			'homeUrl' => home_url('/'),
			'loginUrl' => wp_login_url(),
		));
	}
	
	/**
	 * Handle logout redirection
	 */
	public static function handle_logout() {
		$redirect_url = self::get_redirect_url();
		$redirect_url = apply_filters('wplr_home_url', $redirect_url);
		
		wp_safe_redirect($redirect_url);
		exit();
	}
	
	/**
	 * Get the redirect URL from settings or default to home URL
	 *
	 * @return string
	 */
	public static function get_redirect_url() {
		$saved_url = get_option(self::OPTION_NAME, '');
		
		if (empty($saved_url)) {
			return home_url('/');
		}
		
		return esc_url_raw($saved_url);
	}
	
	/**
	 * Add admin menu
	 */
	public static function add_admin_menu() {
		add_options_page(
			__('WP Logout Redirect Settings', 'wp-logout-redirect'),
			__('Logout Redirect', 'wp-logout-redirect'),
			'manage_options',
			'wp-logout-redirect',
			array(__CLASS__, 'render_settings_page')
		);
	}
	
	/**
	 * Register settings
	 */
	public static function register_settings() {
		register_setting(
			'wplr_settings_group',
			self::OPTION_NAME,
			array(
				'type' => 'string',
				'sanitize_callback' => array(__CLASS__, 'sanitize_redirect_url'),
				'default' => ''
			)
		);
		
		add_settings_section(
			'wplr_main_section',
			__('Redirect Settings', 'wp-logout-redirect'),
			array(__CLASS__, 'render_section_description'),
			'wp-logout-redirect'
		);
		
		add_settings_field(
			'wplr_redirect_url_field',
			__('Redirect URL', 'wp-logout-redirect'),
			array(__CLASS__, 'render_redirect_url_field'),
			'wp-logout-redirect',
			'wplr_main_section'
		);
	}
	
	/**
	 * Sanitize redirect URL
	 *
	 * @param string $url
	 * @return string
	 */
	public static function sanitize_redirect_url($url) {
		if (empty($url)) {
			return '';
		}
		
		// Validate URL format
		$sanitized_url = esc_url_raw($url);
		
		if (empty($sanitized_url)) {
			add_settings_error(
				self::OPTION_NAME,
				'invalid_url',
				__('Please enter a valid URL.', 'wp-logout-redirect'),
				'error'
			);
			return get_option(self::OPTION_NAME, '');
		}
		
		return $sanitized_url;
	}
	
	/**
	 * Render settings page
	 */
	public static function render_settings_page() {
		if (!current_user_can('manage_options')) {
			return;
		}
		$redirect_url = get_option(self::OPTION_NAME, '');
		$current_url = !empty($redirect_url) ? $redirect_url : home_url('/');
		?>
		<div class="wplr-admin-wrap">
			<div class="wplr-header">
				<div class="wplr-header-icon">
					<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M16 4L12 10H20L16 4Z" fill="currentColor"/>
						<path d="M6 16L12 12V20L6 16Z" fill="currentColor"/>
						<path d="M26 16L20 12V20L26 16Z" fill="currentColor"/>
						<circle cx="16" cy="16" r="4" fill="currentColor" opacity="0.5"/>
					</svg>
				</div>
				<div class="wplr-header-content">
					<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
					<p class="wplr-subtitle"><?php _e('Configure the redirect destination after user logout', 'wp-logout-redirect'); ?></p>
				</div>
			</div>

			<div class="wplr-layout">
				<div class="wplr-main">
					<?php settings_errors(); ?>

					<div class="wplr-card wplr-card-primary">
						<div class="wplr-card-header">
							<h2 class="wplr-card-title"><?php _e('Redirect Configuration', 'wp-logout-redirect'); ?></h2>
							<span class="wplr-badge"><?php _e('Active', 'wp-logout-redirect'); ?></span>
						</div>

						<form action="options.php" method="post" class="wplr-form">
							<?php
							settings_fields('wplr_settings_group');
							do_settings_sections('wp-logout-redirect');
							?>

							<div class="wplr-url-preview">
								<span class="wplr-preview-label"><?php _e('Current Redirect Target', 'wp-logout-redirect'); ?></span>
								<a href="<?php echo esc_url($current_url); ?>" target="_blank" class="wplr-preview-url">
									<?php echo esc_html($current_url); ?>
									<svg width="14" height="14" viewBox="0 0 14 14" fill="none">
										<path d="M10.5 3.5L3.5 10.5M3.5 3.5L10.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
									</svg>
								</a>
							</div>

							<div class="wplr-presets">
								<span class="wplr-presets-label"><?php _e('Quick Presets', 'wp-logout-redirect'); ?></span>
								<div class="wplr-preset-buttons">
									<button type="button" class="wplr-preset-btn" data-url="<?php echo esc_url(home_url('/')); ?>">
										<svg width="14" height="14" viewBox="0 0 14 14" fill="none">
											<path d="M2.333 7H11.667M11.667 7L7.583 2.917M11.667 7L7.583 11.083" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<?php _e('Homepage', 'wp-logout-redirect'); ?>
									</button>
									<button type="button" class="wplr-preset-btn" data-url="<?php echo esc_url(wp_login_url()); ?>">
										<svg width="14" height="14" viewBox="0 0 14 14" fill="none">
											<path d="M7 2.333V11.667M7 11.667L2.917 7.583M7 11.667L11.083 7.583" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<?php _e('Login Page', 'wp-logout-redirect'); ?>
									</button>
									<button type="button" class="wplr-preset-btn wplr-preset-clear">
										<svg width="14" height="14" viewBox="0 0 14 14" fill="none">
											<path d="M4.083 4.083L9.917 9.917M9.917 4.083L4.083 9.917" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
										</svg>
										<?php _e('Use Default', 'wp-logout-redirect'); ?>
									</button>
								</div>
							</div>

							<div class="wplr-form-actions">
								<button type="submit" class="wplr-submit-btn">
									<span class="wplr-btn-text"><?php _e('Save Configuration', 'wp-logout-redirect'); ?></span>
									<svg class="wplr-btn-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
										<path d="M13.5 8L2.5 8M2.5 8L6.5 12M2.5 8L6.5 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</button>
							</div>
						</form>
					</div>
				</div>

				<div class="wplr-sidebar">
					<div class="wplr-card wplr-card-developer">
						<div class="wplr-dev-header">
							<svg width="18" height="18" viewBox="0 0 18 18" fill="none">
								<path d="M9 2L2 7V11L9 16L16 11V7L9 2Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
								<path d="M9 11V7M9 7L11 9M9 7L7 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<h3><?php _e('Developer Hook', 'wp-logout-redirect'); ?></h3>
						</div>
						<p class="wplr-dev-text"><?php _e('Programmatically override the redirect URL using the filter:', 'wp-logout-redirect'); ?></p>
						<div class="wplr-code-block">
							<div class="wplr-code-header">
								<span class="wplr-code-lang">php</span>
								<button type="button" class="wplr-copy-btn" title="<?php _e('Copy to clipboard', 'wp-logout-redirect'); ?>">
									<svg width="14" height="14" viewBox="0 0 14 14" fill="none">
										<rect x="2" y="2" width="8" height="8" rx="1" stroke="currentColor" stroke-width="1.5"/>
										<path d="M5 9V11C5 11.8 5.2 12 6 12H11C11.8 12 12 11.8 12 11V6C12 5.2 11.8 5 11 5H9" stroke="currentColor" stroke-width="1.5"/>
									</svg>
								</button>
							</div>
							<pre class="wplr-code-content"><code>add_filter('wplr_home_url', function($url) {
    return 'https://example.com';
});</code></pre>
						</div>
						<p class="wplr-dev-note">
							<svg width="12" height="12" viewBox="0 0 12 12" fill="none">
								<circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="1"/>
								<path d="M6 3V6M6 6V9M6 6H3M6 6H9" stroke="currentColor" stroke-width="1"/>
							</svg>
							<?php _e('Filter hook takes precedence over admin settings', 'wp-logout-redirect'); ?>
						</p>
					</div>

					<div class="wplr-card wplr-card-info">
						<h4><?php _e('Plugin Info', 'wp-logout-redirect'); ?></h4>
						<dl class="wplr-info-list">
							<div class="wplr-info-item">
								<dt><?php _e('Version', 'wp-logout-redirect'); ?></dt>
								<dd>2.0</dd>
							</div>
							<div class="wplr-info-item">
								<dt><?php _e('Author', 'wp-logout-redirect'); ?></dt>
								<dd>Hasin Hayder</dd>
							</div>
						</dl>
						<a href="https://wordpress.org/plugins/wp-logout-redirect/" target="_blank" class="wplr-external-link">
							<?php _e('View on WordPress.org', 'wp-logout-redirect'); ?>
							<svg width="12" height="12" viewBox="0 0 12 12" fill="none">
								<path d="M6 2L10 6M10 6L6 10M10 6H2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Render section description (hidden in new design)
	 */
	public static function render_section_description() {
		// Section description is now inline in the card header
	}

	/**
	 * Render redirect URL field
	 */
	public static function render_redirect_url_field() {
		$value = get_option(self::OPTION_NAME, '');
		$placeholder = home_url('/');

		// Get all published pages
		$pages = get_pages(array(
			'sort_column' => 'post_title',
			'sort_order' => 'ASC',
			'post_status' => 'publish',
		));
		?>
		<div class="wplr-input-wrapper">
			<label for="<?php echo esc_attr(self::OPTION_NAME); ?>" class="screen-reader-text"><?php esc_html_e('Redirect URL', 'wp-logout-redirect'); ?></label>
			<div class="wplr-field-group">
				<input
					type="url"
					name="<?php echo esc_attr(self::OPTION_NAME); ?>"
					id="<?php echo esc_attr(self::OPTION_NAME); ?>"
					value="<?php echo esc_attr($value); ?>"
					placeholder="<?php echo esc_attr($placeholder); ?>"
					class="regular-text"
					aria-label="<?php esc_attr_e('Redirect URL', 'wp-logout-redirect'); ?>"
				/>
			</div>

			<span class="wplr-or-divider" aria-hidden="true"><?php esc_html_e('or', 'wp-logout-redirect'); ?></span>

			<label for="wplr_page_selector" class="screen-reader-text"><?php esc_html_e('Select a page', 'wp-logout-redirect'); ?></label>
			<div class="wplr-field-group wplr-field-group-select">
				<select id="wplr_page_selector" class="wplr-page-select wplr-select-custom" aria-label="<?php esc_attr_e('Select a page to redirect to', 'wp-logout-redirect'); ?>">
					<option value=""><?php esc_html_e('Select a page...', 'wp-logout-redirect'); ?></option>
					<?php
					if (!empty($pages)) {
						foreach ($pages as $page) {
							$page_url = get_permalink($page->ID);
							$is_selected = ($page_url === $value) ? ' selected' : '';
							printf(
								'<option value="%s"%s>%s</option>',
								esc_attr($page_url),
								$is_selected,
								esc_html($page->post_title)
							);
						}
					}
					?>
				</select>
			</div>
		</div>

		<p class="description">
			<?php echo sprintf(
				esc_html__('Enter the full URL (e.g., %s). Leave empty to redirect to the homepage.', 'wp-logout-redirect'),
				'<code>https://example.com</code>'
			); ?>
		</p>
		<?php
	}
	
	/**
	 * Add settings link on plugins page
	 *
	 * @param array $links
	 * @return array
	 */
	public static function add_settings_link($links) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			admin_url('options-general.php?page=wp-logout-redirect'),
			__('Settings', 'wp-logout-redirect')
		);
		array_unshift($links, $settings_link);
		return $links;
	}
}

// Initialize the plugin
WP_Logout_Redirect::init();
