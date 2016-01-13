<?php
/**
 *	Front-end side of things
 *
 *	@package WP Delete User Accounts
 *	@author Ren Ventura
 */

if ( ! class_exists( 'WP_Delete_User_Accounts_Frontend' ) ) :

class WP_Delete_User_Accounts_Frontend {

	public function __construct() {

		$this->init();
	}

	/**
	 *	Initialize everything
	 */
	public function init() {

		add_shortcode( 'wp_delete_user_accounts', array( $this, 'wp_delete_user_accounts' ) );
	}

	/**
	 *	Render [wp_delete_user_accounts] shortcode
	 */
	public function wp_delete_user_accounts( $atts ) {

		// Show nothing if user is logged out
		if ( ! is_user_logged_in() ) {
			return '';
		}

		// Bail to prevent administrators from deleting their own accounts
		if ( current_user_can( 'manage_options' ) ) {
			return '';
		}

		// Attributes
		extract( shortcode_atts( array(
			'label' => __( 'This will cause your account to be permanently deleted. You will not be able to recover your account.', 'wp-delete-user-accounts' ),
			'button_text' => '',
		), $atts ) );

		ob_start();
		
		/**
		 *	Template path
		 *
		 *	@param (array) $atts - Shortcode attributes
		 */
		include_once apply_filters( 'wp_delete_user_accounts_shortcode_template', WP_DELETE_USER_ACCOUNTS_TEMPLATE_DIR . 'shortcode-wp_delete_user_accounts.php', $atts );
		
		return ob_get_clean();
	}
}

new WP_Delete_User_Accounts_Frontend;

endif;