=== WP Delete User Accounts ===

Contributors: renventura
Tags: profiles, accounts
Tested up to: 6.0
Stable tag: 1.2.3
License: GPL 2.0+
License URI: http://www.opensource.org/licenses/gpl-license.php

Allow your users (except for administrators) to manually delete their own accounts.

== Description ==
Allow your users (except for administrators) to manually delete their own accounts. It uses the <a href="http://t4t5.github.io/sweetalert/">Sweet Alert</a> jQuery plugin for slick-looking alerts and prompts.

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