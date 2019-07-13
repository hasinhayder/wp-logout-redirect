=== WP Logout Redirect ===
Contributors: HasinHayder
Tags: settings, admin, logout, redirection, logout redirect
Requires at least: 2.5.0
Tested up to: 5.2.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This is a simple plugin which will take users to the homepage after logout. There is no option update, no manual url settings, no  hassles - it's plain simple! It does only one job and that is redirecting the user to the homepage after logout from the WordPress admin panel.

Developers can also add their custom redirection URL by using the hook `wplr_home_url`  like this

`
add_filter('wplr_home_url',function($url){
	return "https://wordpress.org";
});
`

= Bugs, technical hints or contribute =

Please give us feedback, contribute and file technical bugs on [GitHub Repo](https://github.com/hasinhayder/wp-logout-redirect).


== Changelog ==

= 1.1 =
* Added support for wplr_home_url filter hook, now users can define their own redirection URL after logout. By default it will be set to the home page url

= 1.0 =
* Initial Release