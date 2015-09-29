<?php

namespace mkdo\restrict_content_by_role;

/**
 * Class LoginErrors
 *
 * Errors on the Login Screen for the Plugin
 *
 * @package mkdo\restrict_content_by_role
 */
class LoginErrors {

	private $text_domain;

	public function __construct( ) {
		$this->text_domain = 'restrict-content-by-role';
	}

	public function error_insufficient_permissions() {
		if( isset( $_GET['error'] ) && $_GET['error'] == 'mkdo-rcbr-no-access' ) {

			$error   = get_option( 'mkdo_rcbr_default_restrict_message', esc_html__( 'Sorry, you do not have permission to access that area of the website.', $this->text_domain ) );

			$message = '';
			$message .= '<p class="message">';
			$message .= $error;
			$message .= '</p>';

			return $message;
		}
	}

	public function run() {
		add_action( 'login_message', array( $this, 'error_insufficient_permissions' ) );
	}
}
