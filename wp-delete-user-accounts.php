<?php
/**
 * Plugin Name: WP Delete User Accounts
 * Description: Allow your users to manually delete their own accounts.
 * Version: 1.3.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Ren Ventura
 * Author URI: https://renventura.com
 *
 * Text Domain: wp-delete-user-accounts
 *
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package WP Delete User Accounts
 */

/*
	Copyright 2016  Ren Ventura, RenVentura.com

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	Permission is hereby granted, free of charge, to any person obtaining a copy of this
	software and associated documentation files (the "Software"), to deal in the Software
	without restriction, including without limitation the rights to use, copy, modify, merge,
	publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons
	to whom the Software is furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all copies or
	substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Delete_User_Accounts' ) ) :

	/**
	 * Main plugin class.
	 */
	class WP_Delete_User_Accounts {

		/**
		 * Singleton instance.
		 *
		 * @var WP_Delete_User_Accounts|null
		 */
		private static $instance;

		/**
		 * Get the plugin instance, booting it on first call.
		 *
		 * @return WP_Delete_User_Accounts
		 */
		public static function instance() {

			if ( null === self::$instance ) {

				self::$instance = new WP_Delete_User_Accounts();

				self::$instance->constants();
				self::$instance->includes();
				self::$instance->hooks();
			}

			return self::$instance;
		}

		/**
		 *  Define plugin constants
		 *
		 *  @return void
		 */
		public function constants() {

			if ( ! defined( 'WP_DELETE_USER_ACCOUNTS_PLUGIN_FILE' ) ) {
				define( 'WP_DELETE_USER_ACCOUNTS_PLUGIN_FILE', __FILE__ );
			}

			if ( ! defined( 'WP_DELETE_USER_ACCOUNTS_VERSION' ) ) {
				define( 'WP_DELETE_USER_ACCOUNTS_VERSION', '1.3.0' );
			}

			if ( ! defined( 'WP_DELETE_USER_ACCOUNTS_PLUGIN_BASENAME' ) ) {
				define( 'WP_DELETE_USER_ACCOUNTS_PLUGIN_BASENAME', plugin_basename( WP_DELETE_USER_ACCOUNTS_PLUGIN_FILE ) );
			}

			if ( ! defined( 'WP_DELETE_USER_ACCOUNTS_PLUGIN_DIR' ) ) {
				define( 'WP_DELETE_USER_ACCOUNTS_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
			}

			if ( ! defined( 'WP_DELETE_USER_ACCOUNTS_PLUGIN_URL' ) ) {
				define( 'WP_DELETE_USER_ACCOUNTS_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
			}

			if ( ! defined( 'WP_DELETE_USER_ACCOUNTS_TEMPLATE_DIR' ) ) {
				define( 'WP_DELETE_USER_ACCOUNTS_TEMPLATE_DIR', WP_DELETE_USER_ACCOUNTS_PLUGIN_DIR . 'templates/' );
			}
		}

		/**
		 *  Include PHP files
		 *
		 *  @return void
		 */
		public function includes() {
			include_once 'includes/functions.php';
			include_once 'includes/process-ajax.php';
			include_once 'includes/frontend.php';
			include_once 'includes/admin-profile.php';
		}

		/**
		 *  Kick everything off
		 *
		 *  @return void
		 */
		public function hooks() {
			add_action( 'init', array( $this, 'loaded' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueues' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueues' ) );
		}

		/**
		 *  Load plugin text domain
		 *
		 *  @return void
		 */
		public function loaded() {

			$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
			$locale = apply_filters( 'plugin_locale', $locale, 'wp-delete-user-accounts' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core filter.

			// Custom translations in wp-content/languages/wp-delete-user-accounts/ take priority.
			// WordPress loads WordPress.org translations on its own.
			load_textdomain( 'wp-delete-user-accounts', WP_LANG_DIR . '/wp-delete-user-accounts/wp-delete-user-accounts-' . $locale . '.mo' );
		}

		/**
		 *  Enqueue the assets
		 *
		 *  @return void
		 */
		public function enqueues() {

			// Bail if user is logged out.
			if ( ! is_user_logged_in() ) {
				return;
			}

			// Bail to prevent administrators from deleting their own accounts.
			if ( current_user_can( 'manage_options' ) ) {
				return;
			}

			global $post;

			$confirm_text = apply_filters( 'wp_delete_user_account_confirm_delete_text', __( 'DELETE', 'wp-delete-user-accounts' ) );

			$vars = apply_filters(
				'wp_delete_user_accounts_localize_script_vars',
				array(
					'alert_title'            => __( 'Whoa, there!', 'wp-delete-user-accounts' ),
					'alert_text'             => __( 'Once you delete your account, there\'s no getting it back. Make sure you want to do this.', 'wp-delete-user-accounts' ),
					'confirm_text'           => $confirm_text,
					'button_confirm_text'    => __( 'Yep, delete it', 'wp-delete-user-accounts' ),
					'button_cancel_text'     => __( 'Cancel', 'wp-delete-user-accounts' ),
					'button_ok_text'         => __( 'OK', 'wp-delete-user-accounts' ),
					'incorrect_prompt_title' => __( 'Error', 'wp-delete-user-accounts' ),
					'incorrect_prompt_text'  => __( 'Your confirmation input was incorrect.', 'wp-delete-user-accounts' ),
					'general_error_title'    => __( 'Error', 'wp-delete-user-accounts' ),
					'general_error_text'     => __( 'Something went wrong.', 'wp-delete-user-accounts' ),
					'processing_title'       => __( 'Processing...', 'wp-delete-user-accounts' ),
					'processing_text'        => __( 'Just a moment while we process your request.', 'wp-delete-user-accounts' ),
					'input_placeholder'      => sprintf( '%s %s', __( 'Confirm by typing', 'wp-delete-user-accounts' ), $confirm_text ),
					'redirect_url'           => home_url(),
				)
			);

			$vars['nonce'] = wp_create_nonce( 'wp_delete_user_accounts_nonce' );

			$vars['ajaxurl'] = admin_url( 'admin-ajax.php' );

			if ( is_admin() ) {
				$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
				if ( ! $screen || 'profile' !== $screen->base ) {
					return;
				}
				$vars['is_admin'] = 'true';
			} elseif ( ! apply_filters( 'wp_delete_user_accounts_load_assets_on_frontend', ( is_object( $post ) && has_shortcode( $post->post_content, 'wp_delete_user_accounts' ) ) ) ) {
				return;
			}

			wp_enqueue_style( 'wp-delete-user-accounts-css', WP_DELETE_USER_ACCOUNTS_PLUGIN_URL . 'assets/css/wp-delete-user-accounts.css', array(), WP_DELETE_USER_ACCOUNTS_VERSION );
			wp_enqueue_script( 'wp-delete-user-accounts-js', WP_DELETE_USER_ACCOUNTS_PLUGIN_URL . 'assets/js/wp-delete-user-accounts.js', array(), WP_DELETE_USER_ACCOUNTS_VERSION, true );
			wp_localize_script( 'wp-delete-user-accounts-js', 'wp_delete_user_accounts_js', $vars );
		}
	}

endif;

/**
 *  Main function
 *
 *  @return object WP_Delete_User_Accounts instance
 */
function WP_Delete_User_Accounts() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid -- Public API since 1.0.
	return WP_Delete_User_Accounts::instance();
}

// * Start the engine.
WP_Delete_User_Accounts();
