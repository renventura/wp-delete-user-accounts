<?php
/**
 *	Process the AJAX
 *
 *	@package WP Delete User Accounts
 *	@author Ren Ventura
 */

if ( ! class_exists( 'WP_Delete_User_Accounts_AJAX' ) ) :

class WP_Delete_User_Accounts_AJAX {

	public function __construct() {

		$this->init();
	}

	/**
	 *	Initialize everything
	 */
	public function init() {

		add_action( 'wp_ajax_wp_delete_user_account', array( $this, 'process' ) );
	}

	/**
	 *	Process the request
	 *	@todo Setting for reassigning user's posts
	 */
	public function process() {

		// Verify the security nonce and die if it fails
		if ( ! isset( $_POST['wp_delete_user_accounts_nonce'] ) || ! wp_verify_nonce( $_POST['wp_delete_user_accounts_nonce'], 'wp_delete_user_accounts_nonce' ) ) {
			wp_send_json( array(
				'status' => 'fail',
				'title' => __( 'Error!', 'wp-delete-user-accounts' ),
				'message' => __( 'Request failed security check.', 'wp-delete-user-accounts' )
			) );
		}

		// Don't permit admins to delete their own accounts
		if ( current_user_can( 'manage_options' ) ) {
			wp_send_json( array(
				'status' => 'fail',
				'title' => __( 'Error!', 'wp-delete-user-accounts' ),
				'message' => __( 'Administrators cannot delete their own accounts.', 'wp-delete-user-accounts' )
			) );
		}

		// Get the current user
		$user_id = get_current_user_id();

		// Delete the user's account
		$deleted = wp_delete_user( $user_id );

		if ( $deleted ) {

			// Send success message
			wp_send_json( array(
				'status' => 'success',
				'title' => __( 'Success!', 'wp-delete-user-accounts' ),
				'message' => __( 'Your account was successfully deleted. Fair well.', 'wp-delete-user-accounts' )
			) );
		
		} else {

			wp_send_json( array(
				'status' => 'fail',
				'title' => __( 'Error!', 'wp-delete-user-accounts' ),
				'message' => __( 'Request failed.', 'wp-delete-user-accounts' )
			) );
		}
	}
}

new WP_Delete_User_Accounts_AJAX;

endif;
