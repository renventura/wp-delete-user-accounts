<?php
/**
 * Plugin Name: WP Delete User Accounts
 * Plugin URI: http://www.engagewp.com/
 * Description: Allow your users to manually delete their own accounts.
 * Version: 1.0
 * Author: Ren Ventura
 * Author URI: http://www.engagewp.com/
 *
 * Text Domain: wp-delete-user-accounts
 *
 * License: GPL 2.0+
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */

 /*
	Copyright 2016  Ren Ventura, EngageWP.com

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

if ( ! class_exists( 'WP_Delete_User_Accounts' ) ) :

class WP_Delete_User_Accounts {

	private static $instance;

	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Delete_User_Accounts ) ) {
			
			self::$instance = new WP_Delete_User_Accounts;

			self::$instance->constants();
			self::$instance->includes();
			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 *	Define plugin constants
	 */
	public function constants() {

		if ( ! defined( 'WP_DELETE_USER_ACCOUNTS_PLUGIN_FILE' ) ) {
			define( 'WP_DELETE_USER_ACCOUNTS_PLUGIN_FILE', __FILE__ );
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

		if ( ! defined( 'WP_DELETE_USER_ACCOUNTS_VERSION' ) ) {
			define( 'WP_DELETE_USER_ACCOUNTS_VERSION', 1.0 );
		}
	}

	/**
	 *	Include PHP files
	 */
	public function includes() {

		$path = realpath( WP_DELETE_USER_ACCOUNTS_PLUGIN_DIR . 'includes' );

		$objects = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path ), RecursiveIteratorIterator::SELF_FIRST );

		foreach ( $objects as $name => $object ) {

			if ( is_file( $name ) ) {

				if ( pathinfo( $name, PATHINFO_EXTENSION ) !== 'php' ) {
					continue;
				}

				include_once $name;
			}
		}
	}

	/**
	 *	Kick everything off
	 */
	public function hooks() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueues' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueues' ) );
	}

	/**
	 *	Enqueue the assets
	 */
	public function enqueues() {

		// Bail if user is logged out
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Bail to prevent administrators from deleting their own accounts
		if ( current_user_can( 'manage_options' ) ) {
			return;
		}

		global $post;

		$vars = array(
			'alert_title' => __( 'Whoa, there!', 'wp-delete-user-accounts' ),
			'alert_text' => __( 'Once you delete your account, there\'s no getting it back. Make sure you want to do this.', 'wp-delete-user-accounts' ),
			'confirm_text' => __( 'Yep, delete it', 'wp-delete-user-accounts' ),
			'incorrect_prompt_title' => __( 'Error', 'wp-delete-user-accounts' ),
			'incorrect_prompt_text' => __( 'Your confirmation input was incorrect.', 'wp-delete-user-accounts' ),
			'processing_title' => __( 'Processing...', 'wp-delete-user-accounts' ),
			'processing_text' => __( 'Just a moment while we process your request.', 'wp-delete-user-accounts' ),
			'input_placeholder' => __( 'Confirm by typing DELETE', 'wp-delete-user-accounts' ),
			'redirect_url' => home_url(),
			'nonce' => wp_create_nonce( 'wp_delete_user_accounts_nonce' ),
		);

		if ( is_admin() ) {

			if ( get_current_screen()->base == 'profile' ) {
				wp_enqueue_style( 'wp-delete-user-accounts-css', WP_DELETE_USER_ACCOUNTS_PLUGIN_URL . 'assets/css/wp-delete-user-accounts.css', '', WP_DELETE_USER_ACCOUNTS_VERSION );
				wp_enqueue_script( 'sweetalert-js', WP_DELETE_USER_ACCOUNTS_PLUGIN_URL . 'assets/js/sweetalert.min.js', array( 'jquery' ), WP_DELETE_USER_ACCOUNTS_VERSION, true );
				wp_enqueue_script( 'wp-delete-user-accounts-js', WP_DELETE_USER_ACCOUNTS_PLUGIN_URL . 'assets/js/wp-delete-user-accounts.js', array( 'jquery', 'sweetalert' ), WP_DELETE_USER_ACCOUNTS_VERSION, true );
				wp_localize_script( 'wp-delete-user-accounts-js', 'wp_delete_user_accounts_js', array_merge( $vars, array( 'is_admin' => 'true' ) ) );
				wp_enqueue_script( 'sweetalert', WP_DELETE_USER_ACCOUNTS_PLUGIN_URL . 'assets/js/sweetalert.min.js', array( 'jquery' ), WP_DELETE_USER_ACCOUNTS_VERSION, true );
			}
		
		} elseif ( has_shortcode( $post->post_content, 'wp_delete_user_accounts' ) ) {

			wp_enqueue_style( 'wp-delete-user-accounts-css', WP_DELETE_USER_ACCOUNTS_PLUGIN_URL . 'assets/css/wp-delete-user-accounts.css', '', WP_DELETE_USER_ACCOUNTS_VERSION );
			wp_enqueue_script( 'sweetalert-js', WP_DELETE_USER_ACCOUNTS_PLUGIN_URL . 'assets/js/sweetalert.min.js', array( 'jquery' ), WP_DELETE_USER_ACCOUNTS_VERSION, true );
			wp_enqueue_script( 'wp-delete-user-accounts-js', WP_DELETE_USER_ACCOUNTS_PLUGIN_URL . 'assets/js/wp-delete-user-accounts.js', array( 'jquery', 'sweetalert' ), WP_DELETE_USER_ACCOUNTS_VERSION, true );
			wp_localize_script( 'wp-delete-user-accounts-js', 'wp_delete_user_accounts_js', array_merge( $vars, array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) ) );
			wp_enqueue_script( 'sweetalert', WP_DELETE_USER_ACCOUNTS_PLUGIN_URL . 'assets/js/sweetalert.min.js', array( 'jquery' ), WP_DELETE_USER_ACCOUNTS_VERSION, true );
		}
	}
}

endif;

/**
 *	Main function
 *
 *	@return object WP_Delete_User_Accounts instance
 */
function WP_Delete_User_Accounts() {
	return WP_Delete_User_Accounts::instance();
}

//* Start the engine
WP_Delete_User_Accounts();