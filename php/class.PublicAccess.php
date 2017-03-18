<?php

namespace mkdo\restrict_content_by_role;

/**
 * Class PublicAccess
 *
 * Check if user has access to various peices of content within the website
 *
 * @package mkdo\restrict_content_by_role
 */
class PublicAccess {

	private $login_errors;

	/**
	 * Constructor
	 *
	 * @param LoginErrors $login_errors Object that renders error messages on the login screen
	 */
	public function __construct( LoginErrors $login_errors ) {
		$this->login_errors = $login_errors;
	}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'wp', array( $this, 'access_control' ) );
		$this->login_errors->run();
	}

	/**
	 * Check if user has access
	 */
	public function access_control() {

		global $post, $wp_roles;

		if ( ! is_admin() && ( is_single() || is_singular() ) ) {

            $current_user                   = wp_get_current_user();
            $roles                          = $current_user->roles;
            $redirect_url                   = wp_login_url ( '', false ) . '?error="mkdo-rcbr-no-access"';
            $mkdo_rcbr_redirect_to_original = get_option( 'mkdo_rcbr_redirect_to_original' );
			if( ! empty( $mkdo_rcbr_redirect_to_original ) ) {
				$redirect_url               = wp_login_url ( get_the_permalink( $post->ID ), false ) . '&error="mkdo-rcbr-no-access"';
			}
            $do_redirect                    = false;
            $has_access                     = false;
            $mkdo_rcbr_roles                = get_post_meta( $post->ID, '_mkdo_rcbr_roles', true );
            $mkdo_rcbr_override             = get_post_meta( $post->ID, '_mkdo_rcbr_override', true );
            $mkdo_rcbr_restrict_sub_content = get_post_meta( $post->ID, '_mkdo_rcbr_restrict_sub_content', true );
            $mkdo_rcbr_custom_redirect      = get_post_meta( $post->ID, '_mkdo_rcbr_custom_redirect', true );
            $mkdo_rcbr_default_redirect     = get_option( 'mkdo_rcbr_default_redirect' );
            $mkdo_rcbr_removed_public_roles = get_option( 'mkdo_rcbr_removed_public_roles' );

			if( ! is_array( $mkdo_rcbr_removed_public_roles ) ) {
				$mkdo_rcbr_removed_public_roles = array();
			}

			if( ! is_array( $mkdo_rcbr_roles ) ) {
				$mkdo_rcbr_roles = array();
			}

            $all_roles           = $wp_roles->roles;
            $all_roles['public'] = array( 'name' => 'Public Access' );
            $all_roles           = array_keys( $all_roles );
            $all_roles           = array_diff( $all_roles, $mkdo_rcbr_removed_public_roles );
            $mkdo_rcbr_roles     = array_diff( $mkdo_rcbr_roles, $mkdo_rcbr_removed_public_roles );
            $role_check          = array_diff( $all_roles, $mkdo_rcbr_roles );

			if ( ! empty( $mkdo_rcbr_default_redirect ) ) {
				$redirect_url = $mkdo_rcbr_default_redirect . '?redirect_url=' . urlencode(get_the_permalink( $post->ID ));
			}

			// If the content is not a public override
			if ( 'public' != $mkdo_rcbr_override ) {

				// If it has roles, determine if it should do a redirect
				if ( ! empty( $role_check ) ) {

					if ( 'content' == $mkdo_rcbr_restrict_sub_content || 'all' == $mkdo_rcbr_restrict_sub_content ) {
						$do_redirect = true;
					}

				// If it is sub content, determine if it should do a redirect
				} else if ( ! empty( $post->ancestors ) ) {
					foreach ( $post->ancestors as $parent ) {

                        $mkdo_rcbr_roles                = get_post_meta( $parent, '_mkdo_rcbr_roles', true );
                        $mkdo_rcbr_override             = get_post_meta( $parent, '_mkdo_rcbr_override', true );
                        $mkdo_rcbr_restrict_sub_content = '';
                        $mkdo_rcbr_custom_redirect      = '';

						if( ! is_array( $mkdo_rcbr_roles ) ) {
							$mkdo_rcbr_roles = array();
						}


                        $mkdo_rcbr_roles                = array_diff( $mkdo_rcbr_roles, $mkdo_rcbr_removed_public_roles );
						$role_check                     = array_diff( $all_roles, $mkdo_rcbr_roles );

						if ( ! empty( $role_check ) || 'public' == $mkdo_rcbr_override ) {

							// If parent is public, no redirect
							if ( 'public' == $mkdo_rcbr_override ) {
								$do_redirect = false;
								break;
							}

							$mkdo_rcbr_restrict_sub_content = get_post_meta( $parent, '_mkdo_rcbr_restrict_sub_content', true );
							$mkdo_rcbr_custom_redirect      = get_post_meta( $parent, '_mkdo_rcbr_custom_redirect', true );

							if ( 'all' == $mkdo_rcbr_restrict_sub_content || 'sub' == $mkdo_rcbr_restrict_sub_content ) {
								$do_redirect = true;
							}

							break;
						}
					}
				}
			}

			// If we are not redirecting, the user will have access to the site
			if ( ! $do_redirect ) {
				$has_access = true;

			// Otherwise check that they are in the right group
			} else if ( $do_redirect ) {

				if ( ! is_array( $mkdo_rcbr_roles ) ) {
					$mkdo_rcbr_roles = array();
				}
				foreach ( $roles as $key => $role ) {
					if ( in_array( $role, $mkdo_rcbr_roles ) ) {
						$has_access = true;
						break;
					}
				}


			}

			// If the user does not have access, redirect them.
			if ( ! $has_access ) {

				if ( ! empty( $mkdo_rcbr_custom_redirect ) ) {
					$redirect_url = $mkdo_rcbr_custom_redirect;
				}
				wp_redirect( $redirect_url, 302 );
				exit;
			}
		}
	}

}
