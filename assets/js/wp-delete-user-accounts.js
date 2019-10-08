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
			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: data,
				dataType: 'json',
				success: function(response) {
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
				},
				error: function(jqXHR, textStatus, errorThrown) {
					swal( wp_delete_user_accounts_js.general_error_title, wp_delete_user_accounts_js.general_error_text, 'error' );
					console.log(jqXHR, textStatus, errorThrown);
				}
			});

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
				confirmButtonText: wp_delete_user_accounts_js.button_confirm_text,
				cancelButtonText: wp_delete_user_accounts_js.button_cancel_text,
				confirmButtonColor: '#EC5245',
				disableButtonsOnConfirm: true,
				// showLoaderOnConfirm: true,
				// confirmLoadingButtonColor: '#f5f7fa',
				inputPlaceholder: wp_delete_user_accounts_js.input_placeholder
			},
			function(input) {

				if ( input !== wp_delete_user_accounts_js.confirm_text ) {
					// Input was not correct
					swal(
						{
							title: wp_delete_user_accounts_js.incorrect_prompt_title,
							text: wp_delete_user_accounts_js.incorrect_prompt_text,
							type: 'error',
							showLoaderOnConfirm: false,
						}
					);
					return;
				}

				// Processing modal
				swal(
					{
						title: wp_delete_user_accounts_js.processing_title,
						text: wp_delete_user_accounts_js.processing_text,
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