<?php
/**
 *	Helper functions
 *
 *	@package WP Delete User Accounts
 *	@author Ren Ventura
 */

function wp_delete_user_account_delete_button( $button_text = '' ) {

	// Bail if user is logged out
	if ( ! is_user_logged_in() ) {
		return;	
	}

	// Bail to prevent administrators from deleting their own accounts
	if ( current_user_can( 'manage_options' ) ) {
		return;
	}

	// Defauly button text
	if ( $button_text == '' ) {
		$button_text = __( 'Delete My Account', 'wp-delete-user-accounts' );
	}

	// Button
	printf( '<button id="delete-my-account">%s</button>', $button_text );
}