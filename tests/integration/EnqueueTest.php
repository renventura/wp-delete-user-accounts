<?php
/**
 * Tests for asset loading and the profile page button.
 *
 * @package WP Delete User Accounts
 */

/**
 * Asset enqueueing and admin profile output.
 */
class EnqueueTest extends WP_UnitTestCase {

	/**
	 * Reset script and style registries between tests.
	 */
	public function set_up() {
		parent::set_up();
		$GLOBALS['wp_scripts'] = new WP_Scripts();
		$GLOBALS['wp_styles']  = new WP_Styles();
	}

	/**
	 * Restore the front-end context.
	 */
	public function tear_down() {
		set_current_screen( 'front' );
		unset( $GLOBALS['post'] );
		parent::tear_down();
	}

	/**
	 * Assets load on a post that contains the shortcode, without jQuery or SweetAlert.
	 */
	public function test_enqueues_on_post_with_shortcode() {
		$this->login( 'subscriber' );
		$this->set_post( '[wp_delete_user_accounts]' );

		WP_Delete_User_Accounts()->enqueues();

		$this->assertTrue( wp_script_is( 'wp-delete-user-accounts-js', 'enqueued' ) );
		$this->assertTrue( wp_style_is( 'wp-delete-user-accounts-css', 'enqueued' ) );
		$this->assertSame( array(), wp_scripts()->registered['wp-delete-user-accounts-js']->deps );
		$this->assertFalse( wp_script_is( 'sweetalert-js', 'registered' ) );

		$data = wp_scripts()->get_data( 'wp-delete-user-accounts-js', 'data' );
		$this->assertStringContainsString( '"ajaxurl":', $data );
		$this->assertStringContainsString( '"nonce":', $data );
	}

	/**
	 * Assets don't load on posts without the shortcode.
	 */
	public function test_skips_post_without_shortcode() {
		$this->login( 'subscriber' );
		$this->set_post( 'Hello' );

		WP_Delete_User_Accounts()->enqueues();

		$this->assertFalse( wp_script_is( 'wp-delete-user-accounts-js', 'enqueued' ) );
	}

	/**
	 * The documented filter forces front-end loading.
	 */
	public function test_frontend_filter_forces_loading() {
		$this->login( 'subscriber' );
		add_filter( 'wp_delete_user_accounts_load_assets_on_frontend', '__return_true' );

		WP_Delete_User_Accounts()->enqueues();

		$this->assertTrue( wp_script_is( 'wp-delete-user-accounts-js', 'enqueued' ) );
	}

	/**
	 * Nothing loads for logged-out visitors or administrators.
	 *
	 * @testWith [""]
	 *           ["administrator"]
	 *
	 * @param string $role Role to log in with, or empty for logged out.
	 */
	public function test_skips_logged_out_and_admins( $role ) {
		if ( $role ) {
			$this->login( $role );
		}
		$this->set_post( '[wp_delete_user_accounts]' );

		WP_Delete_User_Accounts()->enqueues();

		$this->assertFalse( wp_script_is( 'wp-delete-user-accounts-js', 'enqueued' ) );
	}

	/**
	 * Assets load on the profile screen and nowhere else in the admin.
	 */
	public function test_admin_loads_only_on_profile_screen() {
		$this->login( 'subscriber' );

		set_current_screen( 'dashboard' );
		WP_Delete_User_Accounts()->enqueues();
		$this->assertFalse( wp_script_is( 'wp-delete-user-accounts-js', 'enqueued' ) );

		set_current_screen( 'profile' );
		WP_Delete_User_Accounts()->enqueues();
		$this->assertTrue( wp_script_is( 'wp-delete-user-accounts-js', 'enqueued' ) );
		$this->assertStringContainsString( '"is_admin":"true"', wp_scripts()->get_data( 'wp-delete-user-accounts-js', 'data' ) );
	}

	/**
	 * The profile page shows the button to non-admins only.
	 */
	public function test_profile_button() {
		$this->login( 'subscriber' );
		ob_start();
		do_action( 'show_user_profile', wp_get_current_user() );
		$output = ob_get_clean();
		$this->assertStringContainsString( 'id="delete-my-account"', $output );

		$this->login( 'administrator' );
		ob_start();
		do_action( 'show_user_profile', wp_get_current_user() );
		$this->assertStringNotContainsString( 'delete-my-account', ob_get_clean() );
	}

	/**
	 * Log in as a new user with the given role.
	 *
	 * @param string $role Role name.
	 */
	private function login( $role ) {
		wp_set_current_user( self::factory()->user->create( array( 'role' => $role ) ) );
	}

	/**
	 * Set the global post.
	 *
	 * @param string $content Post content.
	 */
	private function set_post( $content ) {
		$GLOBALS['post'] = self::factory()->post->create_and_get( array( 'post_content' => $content ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}
}
