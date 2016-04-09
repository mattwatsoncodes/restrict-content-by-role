<?php

namespace mkdo\restrict_content_by_role;

/**
 * Class Upgrade
 *
 * Upgrade the plugin
 *
 * @package mkdo\restrict_content_by_role
 */
class Upgrade {

	/**
	 * Constructor
	 *
	 * @param LoginErrors $login_errors Object that renders error messages on the login screen
	 */
	public function __construct() {
	}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'plugins_loaded', array( $this, 'upgrade' ) );
	}

	/**
	 * Do the plugin upgrade
	 */
	public function upgrade() {

        $version     = get_option( 'mkdo_rcbr_version', null );

		// 3.3.0
		if( null == $version ) {

			add_option( 'mkdo_rcbr_version', MKDO_RCBR_VERSION );

			$check_posts = get_posts(
				array(
					'posts_per_page' => -1,
					'post_status'    => 'any',
					'post_type'      => 'any'
				)
			);

			// Loop through all posts, and if the roles have been set, we need to add the public
			// role, so it dosnt appear checked when it hasnt been.
			foreach( $check_posts as $check_post ) {
				$mkdo_rcbr_roles = get_post_meta( $check_post->ID, '_mkdo_rcbr_roles', true );

				if( is_array( $mkdo_rcbr_roles ) ) {
					if( ! in_array( 'public', $mkdo_rcbr_roles ) ) {
						$mkdo_rcbr_roles[] = 'public';
					}
				}

				update_post_meta( $check_post->ID, '_mkdo_rcbr_roles', $mkdo_rcbr_roles );
			}
		}
	}
}
