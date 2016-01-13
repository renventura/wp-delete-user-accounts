<?php
/**
 *	Template for the [wp_delete_user_accounts] shortcode
 *
 *	@package WP Delete User Accounts
 *	@author Ren Ventura
 */
?>

<div class="delete-user-account-container">
	
	<p><?php echo $label; ?></p>

	<?php wp_delete_user_account_delete_button( $button_text ); ?>

</div>