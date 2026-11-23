<?php
/**
 *  Helper functions
 *
 *  @package WP Delete User Accounts
 *  @author Ren Ventura
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print the delete account button for logged-in, non-admin users.
 *
 * @param string $button_text Button text. Defaults to "Delete My Account".
 * @return void
 */
function wp_delete_user_account_delete_button( $button_text = '' ) {

	// Bail if user is logged out.
	if ( ! is_user_logged_in() ) {
		return;
	}

	// Bail to prevent administrators from deleting their own accounts.
	if ( current_user_can( 'manage_options' ) ) {
		return;
	}

	// Default button text.
	if ( '' === $button_text ) {
		$button_text = __( 'Delete My Account', 'wp-delete-user-accounts' );
	}

	// Button. The text can come from a shortcode attribute, so it must be escaped (CVE-2025-58704).
	printf( '<button type="button" id="delete-my-account" class="wp-delete-user-account-button">%s</button>', esc_html( $button_text ) );
}
