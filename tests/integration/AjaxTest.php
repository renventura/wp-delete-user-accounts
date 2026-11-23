<?php
/**
 * Tests for the account deletion AJAX handler.
 *
 * @package WP Delete User Accounts
 */

/**
 * The wp_delete_user_account AJAX action.
 *
 * @group ajax
 */
class AjaxTest extends WP_Ajax_UnitTestCase {

	/**
	 * A valid nonce deletes the current user.
	 */
	public function test_deletes_current_user_with_valid_nonce() {
		$user_id = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $user_id );
		$_POST['wp_delete_user_accounts_nonce'] = wp_create_nonce( 'wp_delete_user_accounts_nonce' );

		$response = $this->call();

		$this->assertSame( 'success', $response['status'] );
		$this->assertFalse( get_userdata( $user_id ) );
	}

	/**
	 * The user's posts go to the trash with the account (no reassignment).
	 */
	public function test_trashes_user_posts() {
		$user_id = self::factory()->user->create( array( 'role' => 'author' ) );
		$post_id = self::factory()->post->create( array( 'post_author' => $user_id ) );
		wp_set_current_user( $user_id );
		$_POST['wp_delete_user_accounts_nonce'] = wp_create_nonce( 'wp_delete_user_accounts_nonce' );

		$this->call();

		$this->assertSame( 'trash', get_post_status( $post_id ) );
	}

	/**
	 * Requests without a valid nonce are rejected and the user survives.
	 *
	 * @testWith [""]
	 *           ["invalid"]
	 *
	 * @param string $nonce Submitted nonce.
	 */
	public function test_rejects_invalid_nonce( $nonce ) {
		$user_id = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $user_id );
		$_POST['wp_delete_user_accounts_nonce'] = $nonce;

		$response = $this->call();

		$this->assertSame( 'fail', $response['status'] );
		$this->assertInstanceOf( WP_User::class, get_userdata( $user_id ) );
	}

	/**
	 * A nonce issued to another user can't delete the current user.
	 */
	public function test_rejects_nonce_from_another_user() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'subscriber' ) ) );
		$nonce = wp_create_nonce( 'wp_delete_user_accounts_nonce' );

		$victim = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $victim );
		$_POST['wp_delete_user_accounts_nonce'] = $nonce;

		$response = $this->call();

		$this->assertSame( 'fail', $response['status'] );
		$this->assertInstanceOf( WP_User::class, get_userdata( $victim ) );
	}

	/**
	 * Administrators can't delete themselves.
	 */
	public function test_administrators_cannot_delete_themselves() {
		$user_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$_POST['wp_delete_user_accounts_nonce'] = wp_create_nonce( 'wp_delete_user_accounts_nonce' );

		$response = $this->call();

		$this->assertSame( 'fail', $response['status'] );
		$this->assertInstanceOf( WP_User::class, get_userdata( $user_id ) );
	}

	/**
	 * The action is only registered for logged-in users.
	 */
	public function test_no_logged_out_handler() {
		$this->assertFalse( has_action( 'wp_ajax_nopriv_wp_delete_user_account' ) );
	}

	/**
	 * Run the AJAX action and decode its JSON response.
	 *
	 * @return array<string, string>
	 */
	private function call() {
		try {
			$this->_handleAjax( 'wp_delete_user_account' );
		} catch ( WPAjaxDieContinueException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch -- wp_send_json() ends the request this way.
		}

		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );

		return $response;
	}
}
