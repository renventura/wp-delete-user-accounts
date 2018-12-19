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
`$path` - (string) Path to the template
`$atts` - (array) Shortcode attributes

Return:
(string) Full path to new template file

Example:
The following example shows you how to change the template path for the delete button. This is helpful for creating a custom button template.
```php
add_filter( 'wp_delete_user_accounts_shortcode_template', 'wp_delete_user_accounts_custom_shortcode_template', 10, 2 );
/**
 *	Template path for the WP Delete User Accounts delete button
 *
 * 	@param string		$path 		Path to template file
 *	@param array 		$atts 		Shortcode attributes
 */
function wp_delete_user_accounts_custom_shortcode_template( $path, $atts ) {
	return get_stylesheet_directory() . '/templates/custom-shortcode.php' );
}
```

`wp_delete_user_accounts_load_assets_on_frontend` (filter) Load the plugin's CSS and Javascript files wherever you want. By default, the files are only loaded when the current post object's `$post->post_content` has the [wp_delete_user_accounts] shortcode.

Parameters:
`$load` - (boolean) Whether the assets are to be loaded

Return:
(boolean) Whether to load the files

Example:
This example shows you how to add the CSS and JS files to the WooCommerce edit-account endpoint. This only loads the assets - it does not output the button. For an example of outputting the delete button, see the next example.
```php
add_filter( 'wp_delete_user_accounts_load_assets_on_frontend', 'wp_delete_user_accounts_load_assets_on_woocommerce_edit_account' );
/**
 * Add the WP Delete User Accounts CSS and JS files to the WooCommerce edit-account endpoint
 *
 * @param  boolean 		$load 		Whether the assets are to be loaded (by default, will only load when post content has the [wp_delete_user_accounts] shortcode)
 *
 * @return boolean
 */
function wp_delete_user_accounts_load_assets_on_woocommerce_edit_account( $load ) {
	return is_wc_endpoint_url( 'edit-account' ) ? true : $load;
}
```

This shows you how to add the delete button to a WooCommerce template using a WooCommerce hook (in this example, )
```php
add_action( 'woocommerce_after_edit_account_form', 'wp_delete_user_accounts_delete_button_after_edit_account' );
/**
 * Output the WP Delete User Accounts delete button after the edit-account form in WooCommerce
 * @author  Ren Ventura <renventura.com>
 */
function wp_delete_user_accounts_delete_button_after_edit_account() {
	echo do_shortcode('[wp_delete_user_accounts]');
}
```

## Bugs ##
If you find an issue, let me know!

## Changelog ##

__1.2.1__
* Made the DELETE text (for confirming account deletion) translatable

__1.2__
* Added filter for modifying script localization variables (e.g. the redirect URL)

__1.1.1__
* Updated translatable strings

__1.1.0__
* Added filter for loading CSS and JS assets anywhere on the frontend (e.g. within WooCommerce templates).

__1.0.3__
* Changed plugin text domain to dirname instead of basename to remove .php extension from path

__1.0.2__
* Add POT file for translations

__1.0.1__
* Fixed dependency array on enqueues

__1.0__
* Initial version