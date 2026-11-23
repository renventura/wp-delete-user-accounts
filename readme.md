# WP Delete User Accounts

Allow your users (except for administrators) to manually delete their own accounts. Users confirm by typing DELETE into a native browser dialog. No jQuery or third-party scripts are loaded.

Requires WordPress 6.0+ and PHP 7.4+.

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
	return get_stylesheet_directory() . '/templates/custom-shortcode.php';
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

`wp_delete_user_accounts_localize_script_vars` (filter) Change the dialog text or the redirect URL after deletion.

```php
add_filter( 'wp_delete_user_accounts_localize_script_vars', function ( $vars ) {
	$vars['redirect_url'] = home_url( '/goodbye/' );
	return $vars;
} );
```

## Development ##

Tooling runs in Docker, so only Docker is required:

```sh
docker compose run --rm php composer install
docker compose run --rm php composer check   # PHPCS, PHPStan, PHPUnit
PHP_VERSION=7.4 docker compose run --rm php composer test   # another PHP version
docker compose down
```

- `composer lint` / `composer lint:fix`: WordPress Coding Standards and PHP 7.4+ compatibility (PHPCS).
- `composer analyse`: PHPStan (level 6) with WordPress stubs.
- `composer test`: integration tests against the WordPress test suite and MySQL.
- Regenerate the translation template with WP-CLI: `wp i18n make-pot . languages/wp-delete-user-accounts.pot`.

CI runs the same checks plus the official WordPress Plugin Check on every pull request. Publishing a GitHub release deploys to WordPress.org.

## Bugs ##
If you find an issue, let me know!

## Changelog ##

__1.3.0__
* Security: Escape the shortcode `button_text` attribute to fix an authenticated (Contributor+) stored XSS (CVE-2025-58704)
* Fixed the shortcode rendering only once per page
* Replaced the unmaintained SweetAlert library and the jQuery dependency with a native `<dialog>`
* Hardened the AJAX handler (sanitized nonce, explicit login check)
* Fixed asset loading in the admin when the current screen is not available
* Every delete button on a page opens the dialog (adds a `wp-delete-user-account-button` class)
* Load translations on `init`, as WordPress 6.7+ expects
* Added integration tests, coding standards and static analysis, run in CI
* Requires WordPress 6.0+ and PHP 7.4+
* WP Tested Up To 7.1, PHP Tested Up To 8.6

__1.2.4__
* Fixed unescaped shortcode label
* WP Tested Up To 6.7

__1.2.3__
* WP Tested Up To 6.0

__1.2.2__
* Remove wp_logout(), which was causing processing to get stuck
* Use `$.ajax()` instead of `$.post()`

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