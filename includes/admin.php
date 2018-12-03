<?php
/**
 *	Admin side of things
 *
 *	@package WP Delete User Accounts
 *	@author Ren Ventura
 */

if ( ! class_exists( 'WP_Delete_User_Accounts_Admin' ) ) :

class WP_Delete_User_Accounts_Admin {

	public function __construct() {

		$this->init();
	}

	public function init() {

		add_action( 'show_user_profile', array( $this, 'delete_account_button_admin' ), 99 );
	}

	public function delete_account_button_admin() {

		// Bail to prevent administrators from deleting their own accounts
		if ( current_user_can( 'manage_options' ) ) {
			return;
		}

		printf( '<h3>%s</h3>', __( 'Delete Account', 'wp-delete-user-accounts' ) );

		printf( '<p>%s</p>', __( 'This will cause your account to be permanently deleted. You will not be able to recover your account.', 'wp-delete-user-accounts' ) );

		wp_delete_user_account_delete_button();
	}
}

new WP_Delete_User_Accounts_Admin;

endif;