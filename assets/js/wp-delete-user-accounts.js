jQuery(document).ready(function($) {

	/**
	 *	Process request to dismiss our admin notice
	 */
	$('#delete-my-account').click(function(e) {

		e.preventDefault();

		// Set the ajaxurl when not in the admin
		if ( wp_delete_user_accounts_js.is_admin != 'true' ) {
			ajaxurl = wp_delete_user_accounts_js.ajaxurl;
		}

		// Data to make available via the $_POST variable
		data = {
			action: 'wp_delete_user_account',
			wp_delete_user_accounts_nonce: wp_delete_user_accounts_js.nonce
		};

		// Send the AJAX POST request
		var send = function() {
			$.post( ajaxurl, data, function(response) {
				if ( typeof( response.status ) != 'undefined' && response.status == 'success' ) {
					// Account deleted
					swal(
						{
							title: response.title,
							text: response.message,
							type: 'success',
							timer: 6000
						},
						function() {
							window.location.href = wp_delete_user_accounts_js.redirect_url;
						}
					);
				} else { // Error occurred
					swal( response.title, response.message, 'error' );
				}
			} );

			return false;
		}

		// Main prompt
		swal(
			{
				title: wp_delete_user_accounts_js.alert_title,
				text: wp_delete_user_accounts_js.alert_text,
				type: 'input',
				animation: 'slide-from-top',
				showCancelButton: true,
				closeOnConfirm: false,
				confirmButtonText: wp_delete_user_accounts_js.confirm_text,
				confirmButtonColor: '#EC5245',
				disableButtonsOnConfirm: true,
				// showLoaderOnConfirm: true,
				// confirmLoadingButtonColor: '#f5f7fa',
				inputPlaceholder: 'Confirm by typing DELETE'
			},
			function(input) {

				if ( input !== 'DELETE' ) {
					// Input was not correct
					swal(
						{
							title: 'Error',
							text: 'Your confirmation input was incorrect.',
							type: 'error',
							showLoaderOnConfirm: false,
						}
					);
					return;
				}

				// Processing modal
				swal(
					{
						title: 'Processing...',
						text: 'Just a moment while we process your request.',
						type: 'info',
						confirmLoadingButtonColor: '#f5f7fa',
						showConfirmButton: false
					}
				);

				// Wait 2 seconds and send request
				setTimeout(send, 2000);
			}
		);
	});
});