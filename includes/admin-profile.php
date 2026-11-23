<?php
/**
 *  Admin side of things
 *
 *  @package WP Delete User Accounts
 *  @author Ren Ventura
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Delete_User_Accounts_Admin' ) ) :

	/**
	 * Adds the delete button to the user's own profile screen.
	 */
	class WP_Delete_User_Accounts_Admin {

		/**
		 * Register hooks.
		 */
		public function __construct() {

			$this->init();
		}

		/**
		 * Hook into the profile screen.
		 *
		 * @return void
		 */
		public function init() {

			add_action( 'show_user_profile', array( $this, 'delete_account_button_admin' ), 99 );
		}

		/**
		 * Render the delete section on the profile screen.
		 *
		 * @return void
		 */
		public function delete_account_button_admin() {

			// Bail to prevent administrators from deleting their own accounts.
			if ( current_user_can( 'manage_options' ) ) {
				return;
			}

			printf( '<h3>%s</h3>', esc_html__( 'Delete Account', 'wp-delete-user-accounts' ) );

			printf( '<p>%s</p>', esc_html__( 'This will cause your account to be permanently deleted. You will not be able to recover your account.', 'wp-delete-user-accounts' ) );

			wp_delete_user_account_delete_button();
		}
	}

	new WP_Delete_User_Accounts_Admin();

endif;
