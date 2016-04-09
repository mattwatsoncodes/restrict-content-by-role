<?php

namespace mkdo\restrict_content_by_role;

/**
 * Class AccessController
 *
 * Check if user has access to various peices of content within the website
 *
 * @package mkdo\restrict_content_by_role
 */
class AccessController {

	private $public_access;
	private $admin_access;

	/**
	 * Constructor
	 *
	 * @param LoginErrors $login_errors Object that renders error messages on the login screen
	 */
	public function __construct( AdminAccess $admin_access, PublicAccess $public_access ) {
        $this->public_access = $public_access;
        $this->admin_access  = $admin_access;
	}

	/**
	 * Do Work
	 */
	public function run() {

		$this->public_access->run();
		$this->admin_access->run();
	}

}
