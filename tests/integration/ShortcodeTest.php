<?php
/**
 * Tests for the [wp_delete_user_accounts] shortcode and delete button output.
 *
 * @package WP Delete User Accounts
 */

/**
 * Shortcode rendering and escaping.
 */
class ShortcodeTest extends WP_UnitTestCase {

	/**
	 * Log in as a subscriber, the role that sees the button.
	 */
	public function set_up() {
		parent::set_up();
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'subscriber' ) ) );
	}

	/**
	 * Payloads that must never reach the page as markup.
	 *
	 * @return array<string, array{string}>
	 */
	public function xss_payloads() {
		return array(
			'img onerror'    => array( '<img src=x onerror=alert(1)>' ),
			'script'         => array( '<script>alert(1)</script>' ),
			'svg onload'     => array( '<svg onload=alert(1)>' ),
			'quote breakout' => array( '"><img src=x onerror=alert(1)>' ),
		);
	}

	/**
	 * Regression for CVE-2025-58704: button_text was printed unescaped.
	 *
	 * Calls the shortcode callback directly so the result doesn't depend on core's attribute parser,
	 * which on newer WordPress versions neutralizes some payloads before they reach the plugin.
	 *
	 * @dataProvider xss_payloads
	 *
	 * @param string $payload Attribute value.
	 */
	public function test_button_text_is_escaped( $payload ) {
		$output = $this->render( array( 'button_text' => $payload ) );

		$this->assertStringNotContainsString( '<img', $output );
		$this->assertStringNotContainsString( '<script', $output );
		$this->assertStringNotContainsString( '<svg', $output );
		$this->assertStringContainsString( esc_html( $payload ), $output );
	}

	/**
	 * The label attribute is escaped too.
	 *
	 * @dataProvider xss_payloads
	 *
	 * @param string $payload Attribute value.
	 */
	public function test_label_is_escaped( $payload ) {
		$output = $this->render( array( 'label' => $payload ) );

		$this->assertStringNotContainsString( '<img', $output );
		$this->assertStringNotContainsString( '<script', $output );
		$this->assertStringNotContainsString( '<svg', $output );
	}

	/**
	 * The CVE's real-world path: a contributor saves a post (kses active), a logged-in user views it.
	 * The \x3c escape survives kses and older core versions decode it to "<" in shortcode_parse_atts().
	 */
	public function test_contributor_post_cannot_inject_markup() {
		$viewer      = get_current_user_id();
		$contributor = self::factory()->user->create( array( 'role' => 'contributor' ) );

		wp_set_current_user( $contributor );
		kses_init();
		$post_id = wp_insert_post(
			array(
				'post_title'   => 'XSS',
				'post_status'  => 'pending',
				'post_content' => wp_slash( '[wp_delete_user_accounts button_text="\x3cimg src=x onerror=alert(1)\x3e" label="\x3cimg src=x onerror=alert(2)\x3e"]' ),
			)
		);
		kses_remove_filters();

		wp_set_current_user( $viewer );
		$output = do_shortcode( get_post_field( 'post_content', $post_id, 'raw' ) );

		$this->assertStringContainsString( 'delete-user-account-container', $output );
		$this->assertStringNotContainsString( '<img', $output );
	}

	/**
	 * Defaults render when no attributes are given.
	 */
	public function test_default_output() {
		$output = do_shortcode( '[wp_delete_user_accounts]' );

		$this->assertStringContainsString( 'This will cause your account to be permanently deleted.', $output );
		$this->assertStringContainsString( '>Delete My Account</button>', $output );
		$this->assertStringContainsString( 'type="button"', $output );
		$this->assertStringContainsString( 'class="wp-delete-user-account-button"', $output );
	}

	/**
	 * Regression: the template used include_once, so only the first shortcode on a page rendered.
	 */
	public function test_renders_more_than_once_per_request() {
		$output = do_shortcode( '[wp_delete_user_accounts button_text="One"][wp_delete_user_accounts button_text="Two"]' );

		$this->assertStringContainsString( '>One</button>', $output );
		$this->assertStringContainsString( '>Two</button>', $output );
	}

	/**
	 * Logged-out visitors see nothing.
	 */
	public function test_hidden_when_logged_out() {
		wp_set_current_user( 0 );

		$this->assertSame( '', do_shortcode( '[wp_delete_user_accounts]' ) );
	}

	/**
	 * Administrators see nothing, so they can't lock themselves out.
	 */
	public function test_hidden_for_administrators() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$this->assertSame( '', do_shortcode( '[wp_delete_user_accounts]' ) );
	}

	/**
	 * Render the shortcode callback with raw attributes.
	 *
	 * @param array<string, string> $atts Shortcode attributes.
	 * @return string
	 */
	private function render( array $atts ) {
		global $shortcode_tags;

		return call_user_func( $shortcode_tags['wp_delete_user_accounts'], $atts, '', 'wp_delete_user_accounts' );
	}
}
