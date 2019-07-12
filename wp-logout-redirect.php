<?php
/*
Plugin Name: WP Logout Redirect
Plugin URI: https://wordpress.org/plugins/wp-logout-redirect/
Description: This plugin will take users directly to the home page after logout.
Version: 1.0
Author: Hasin Hayder
Author URI: https://github.com/hasinhayder
License: GPLv2 or later
Text Domain: wp-logout-redirect
Domain Path: /languages/
*/

add_action('wp_logout',function(){
	if(check_admin_referer('log-out')) {
		wp_redirect( home_url( '/' ) );
		exit();
	}
});