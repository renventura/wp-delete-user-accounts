<?php
/**
 *  Template for the [wp_delete_user_accounts] shortcode
 *
 *  @package WP Delete User Accounts
 *  @author Ren Ventura
 *
 *  @var string $label       Escaped on output.
 *  @var string $button_text Escaped on output.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="delete-user-account-container">
	
	<p><?php echo esc_html( $label ); ?></p>

	<?php wp_delete_user_account_delete_button( $button_text ); ?>

</div>