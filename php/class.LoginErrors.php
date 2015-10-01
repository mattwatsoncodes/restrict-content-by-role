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

	/**
	 * Constructor
	 */
	public function __construct( ) {
	}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'login_message', array( $this, 'error_no_access' ), 99 );
	}

	/**
	 * Error to show if no access is granted
	 */
	public function error_no_access() {
		if( isset( $_GET['error'] ) && $_GET['error'] == 'mkdo-rcbr-no-access' ) {

			$error   = get_option( 'mkdo_rcbr_default_restrict_message', esc_html__( 'Please login to access that area of the website.', MKDO_RCBR_TEXT_DOMAIN ) );

			$message = '';
			$message .= '<p class="message">';
			$message .= $error;
			$message .= '</p>';

			return $message;
		}
	}
}
