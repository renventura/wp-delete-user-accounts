<?php
/**
 *  Process the AJAX
 *
 *  @package WP Delete User Accounts
 *  @author Ren Ventura
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Delete_User_Accounts_AJAX' ) ) :

	/**
	 * Handles the account deletion request.
	 */
	class WP_Delete_User_Accounts_AJAX {

		/**
		 * Register hooks.
		 */
		public function __construct() {

			$this->init();
		}

		/**
		 *  Initialize everything
		 *
		 * @return void
		 */
		public function init() {

			add_action( 'wp_ajax_wp_delete_user_account', array( $this, 'process' ) );
		}

		/**
		 *  Process the request
		 *
		 *  @todo Setting for reassigning user's posts
		 *
		 *  @return void
		 */
		public function process() {

			// Verify the security nonce and die if it fails.
			$nonce = isset( $_POST['wp_delete_user_accounts_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_delete_user_accounts_nonce'] ) ) : '';

			if ( ! is_user_logged_in() || ! wp_verify_nonce( $nonce, 'wp_delete_user_accounts_nonce' ) ) {
				wp_send_json(
					array(
						'status'  => 'fail',
						'title'   => __( 'Error!', 'wp-delete-user-accounts' ),
						'message' => __( 'Request failed security check.', 'wp-delete-user-accounts' ),
					)
				);
			}

			// Don't permit admins to delete their own accounts.
			if ( current_user_can( 'manage_options' ) ) {
				wp_send_json(
					array(
						'status'  => 'fail',
						'title'   => __( 'Error!', 'wp-delete-user-accounts' ),
						'message' => __( 'Administrators cannot delete their own accounts.', 'wp-delete-user-accounts' ),
					)
				);
			}

			// Get the current user.
			$user_id = get_current_user_id();

			// wp_delete_user() lives in an admin include. admin-ajax.php loads it, but don't rely on that.
			if ( ! function_exists( 'wp_delete_user' ) ) {
				require_once ABSPATH . 'wp-admin/includes/user.php';
			}

			// Delete the user's account.
			$deleted = wp_delete_user( $user_id );

			if ( $deleted ) {

				// Send success message.
				wp_send_json(
					array(
						'status'  => 'success',
						'title'   => __( 'Success!', 'wp-delete-user-accounts' ),
						'message' => __( 'Your account was successfully deleted. Farewell.', 'wp-delete-user-accounts' ),
					)
				);

			} else {

				wp_send_json(
					array(
						'status'  => 'fail',
						'title'   => __( 'Error!', 'wp-delete-user-accounts' ),
						'message' => __( 'Request failed.', 'wp-delete-user-accounts' ),
					)
				);
			}
		}
	}

	new WP_Delete_User_Accounts_AJAX();

endif;
