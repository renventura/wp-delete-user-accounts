# WP Delete User Accounts

Allow your users (except for administrators) to manually delete their own accounts. This plugin is used in the tutorial at [EngageWP.com](http://www.engagewp.com/wordpress-allow-users-delete-accounts), and can be used in a live setting.  It uses the [Sweet Alert](http://t4t5.github.io/sweetalert/) jQuery plugin for slick-looking alerts and prompts.

## Installation ##

__Manually__

1. Download the zip file, unzip it and upload plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

## Frequently Asked Questions ##

*How do I use this plugin?*

Install it, and let it run. There are no settings.

## Hooks ##
`wp_delete_user_accounts_shortcode_template` (filter) Override the default template for shortcode output

Parameters:
`$atts` - (array) Shortcode attributes

Return:
(string) Full path to new template file

Example:
```php
add_filter( 'wp_delete_user_accounts_shortcode_template', 'wp_delete_user_accounts_custom_shortcode_template' );
function wp_delete_user_accounts_custom_shortcode_template() {
	return get_stylesheet_directory() . '/templates/custom-shortcode.php' );
}
```

## Bugs ##
If you find an issue, let me know!

## Changelog ##

__1.0.2__
* Add POT file for translations

__1.0.1__
* Fixed dependency array on enqueues

__1.0__
* Initial version