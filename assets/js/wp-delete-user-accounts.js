/**
 * Confirm and process account deletion using a native <dialog>.
 *
 * Settings come from wp_localize_script() as window.wp_delete_user_accounts_js.
 * All text is inserted with textContent, never as HTML.
 */
( function () {
	'use strict';

	var settings = window.wp_delete_user_accounts_js;

	if ( ! settings ) {
		return;
	}

	var ajaxUrl = settings.ajaxurl || window.ajaxurl;
	var ui = null;

	/**
	 * Build the dialog once and reuse it.
	 */
	function getDialog() {
		if ( ui ) {
			return ui;
		}

		var dialog = document.createElement( 'dialog' );
		dialog.className = 'wp-delete-user-accounts-dialog';
		dialog.setAttribute( 'aria-labelledby', 'wp-delete-user-accounts-dialog-title' );

		var form = document.createElement( 'form' );
		form.method = 'dialog';

		var title = document.createElement( 'h2' );
		title.id = 'wp-delete-user-accounts-dialog-title';

		var text = document.createElement( 'p' );

		var input = document.createElement( 'input' );
		input.type = 'text';
		input.autocomplete = 'off';
		input.placeholder = settings.input_placeholder;
		input.setAttribute( 'aria-label', settings.input_placeholder );

		var actions = document.createElement( 'div' );
		actions.className = 'wp-delete-user-accounts-dialog-actions';

		var cancel = document.createElement( 'button' );
		cancel.type = 'button';
		cancel.className = 'wp-delete-user-accounts-cancel';

		var confirm = document.createElement( 'button' );
		confirm.type = 'submit';
		confirm.className = 'wp-delete-user-accounts-confirm';

		actions.append( cancel, confirm );
		form.append( title, text, input, actions );
		dialog.append( form );
		document.body.append( dialog );

		ui = { dialog: dialog, form: form, title: title, text: text, input: input, cancel: cancel, confirm: confirm, onSubmit: null };

		cancel.addEventListener( 'click', function () {
			dialog.close();
		} );

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			if ( ui.onSubmit ) {
				ui.onSubmit();
			}
		} );

		return ui;
	}

	/**
	 * Render a dialog state.
	 *
	 * @param {Object} state title, text, input (bool), confirmText, cancelText, onSubmit, busy (bool).
	 */
	function render( state ) {
		var d = getDialog();

		d.title.textContent = state.title || '';
		d.text.textContent = state.text || '';
		d.input.hidden = ! state.input;
		d.confirm.hidden = ! state.confirmText;
		d.confirm.textContent = state.confirmText || '';
		d.cancel.hidden = ! state.cancelText;
		d.cancel.textContent = state.cancelText || '';
		d.dialog.setAttribute( 'aria-busy', state.busy ? 'true' : 'false' );
		d.dialog.dataset.state = state.name || '';
		d.onSubmit = state.onSubmit || function () {
			d.dialog.close();
		};

		if ( ! d.dialog.open ) {
			d.dialog.showModal();
		}

		( state.input ? d.input : d.confirm ).focus();
	}

	function showMessage( title, text, onClose ) {
		render( {
			name: 'message',
			title: title,
			text: text,
			confirmText: settings.button_ok_text || 'OK',
			onSubmit: function () {
				getDialog().dialog.close();
				if ( onClose ) {
					onClose();
				}
			},
		} );
	}

	function redirect() {
		window.location.href = settings.redirect_url;
	}

	function send() {
		render( { name: 'processing', title: settings.processing_title, text: settings.processing_text, busy: true } );

		var body = new FormData();
		body.append( 'action', 'wp_delete_user_account' );
		body.append( 'wp_delete_user_accounts_nonce', settings.nonce );

		fetch( ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' } )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( response ) {
				if ( response && 'success' === response.status ) {
					showMessage( response.title, response.message, redirect );
					// The account is gone, so leave the page even if the user does nothing.
					setTimeout( redirect, 6000 );
				} else {
					showMessage(
						( response && response.title ) || settings.general_error_title,
						( response && response.message ) || settings.general_error_text
					);
				}
			} )
			.catch( function ( error ) {
				showMessage( settings.general_error_title, settings.general_error_text );
				window.console && console.error( error ); // eslint-disable-line no-console
			} );
	}

	function confirmDelete() {
		var d = getDialog();
		d.input.value = '';

		render( {
			name: 'confirm',
			title: settings.alert_title,
			text: settings.alert_text,
			input: true,
			confirmText: settings.button_confirm_text,
			cancelText: settings.button_cancel_text,
			onSubmit: function () {
				if ( d.input.value !== settings.confirm_text ) {
					showMessage( settings.incorrect_prompt_title, settings.incorrect_prompt_text );
					return;
				}
				send();
			},
		} );
	}

	document.addEventListener( 'click', function ( e ) {
		var button = e.target.closest( '.wp-delete-user-account-button, #delete-my-account' );
		if ( ! button ) {
			return;
		}
		e.preventDefault();
		confirmDelete();
	} );
}() );
