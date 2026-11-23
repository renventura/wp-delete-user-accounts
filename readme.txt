=== WP Delete User Accounts ===

Contributors: renventura
Tags: profiles, accounts, delete account, gdpr, users
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Allow your users (except for administrators) to manually delete their own accounts.

== Description ==
Allow your users (except for administrators) to manually delete their own accounts. Users confirm by typing DELETE into a native browser dialog. No jQuery or third-party scripts are loaded.

By default, this plugin adds a button to a user's profile page in the wp-admin. You can also add a delete button to any page or post using the `[wp_delete_user_accounts]` shortcode.

NOTE: Delete buttons are not displayed when logged in as an administrator. This is done to protect against locking yourself out of your site. To see the delete button, you’ll need to log in with an account that does not have the administrator role.

For more technical info, including available hooks (actions and filters), please see the plugin's [readme file on Github](https://github.com/renventura/wp-delete-user-accounts).

== Installation ==

= Automatically =

1. Search for WP Delete User Accounts in the Add New Plugin section of the WordPress admin.
2. Install and activate.

= Manually =

1. Download the zip file, decompress the file and upload the main plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin.

== Screenshots ==

1. "Delete My Account" button is shown on the edit-profile page, and can be displayed on the front-end via a shortcode.

2. Confirm account deletion prompt.

3. Processing account deletion.

4. Account deleted successfully. 

== Changelog ==

= 1.3.0 =
* Replaced the unmaintained SweetAlert library and the jQuery dependency with a native dialog
* Hardened the AJAX handler (sanitized nonce, explicit login check)
* Fixed asset loading in the admin when the current screen is not available
* The delete button can appear more than once on a page
* Load translations on `init`, as WordPress 6.7+ expects
* Requires WordPress 6.0+ and PHP 7.4+
* Tested with WordPress 7.1 and PHP 8.6

= 1.2.5 =
* Security: Escape the shortcode `button_text` attribute to fix an authenticated (Contributor+) stored XSS (CVE-2025-58704)
* Fixed the shortcode rendering only once per page
* WP Tested Up To 7.1

= 1.2.4 =
* Fixed unescaped shortcode label
* WP Tested Up To 6.7

= 1.2.3 =
* WP Tested Up To 6.0

= 1.2.2 =
* Remove wp_logout(), which was causing processing to get stuck
* Use `$.ajax()` instead of `$.post()`

= 1.2.1 =
* Made the DELETE text (for confirming account deletion) translatable

= 1.2 =
* Added filter for modifying script localization variables (e.g. the redirect URL)

= 1.1.1 =
* Updated translatable strings

= 1.1 =
* Added filter for loading CSS and JS assets anywhere on the frontend (e.g. within WooCommerce templates).

= 1.0.3 =
* Changed plugin text domain to dirname instead of basename to remove .php extension from path

= 1.0.2 =
* Add POT file for translations

= 1.0.1 =
* Fixed dependency array on enqueues

= 1.0 =
* Initial version