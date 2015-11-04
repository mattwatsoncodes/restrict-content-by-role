<?php

namespace mkdo\restrict_content_by_role;

/**
 * Class AdminAccess
 *
 * Check if user has access to various peices of content within the website
 *
 * @package mkdo\restrict_content_by_role
 */
class AdminAccess {

	private $excluded_posts;

	/**
	 * Constructor
	 *
	 * @param LoginErrors $login_errors Object that renders error messages on the login screen
	 */
	public function __construct() {
		$this->excluded_posts = array();
	}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'admin_init', array( $this, 'get_excluded_posts' ) );
		add_action( 'pre_get_posts', array( $this, 'update_post_list_access' ) );
		add_filter( 'wp_count_posts', array( $this, 'update_post_list_count') );
	}

	/**
	 * Check if user has access
	 */
	public function get_excluded_posts() {

		if( is_admin() && ! current_user_can( 'manage_options' ) ) {

			global $typenow, $pagenow;

			// If we dont have access to the current page, redirect to the post list
			if( $pagenow == 'post.php' ) {

				if( isset($_GET['post'] ) ) {
					$post_id    = $_GET['post'];
					$post       = get_post( $post_id );
					$has_access = $this->user_can_access_post( $post );

					if( ! $has_access ) {
						wp_safe_redirect( admin_url( 'edit.php?post_type=' . $post->post_type ), $status = 301 );
						exit;
					}
				}

			// If we are on the post list, hide the ones that we do not have access to
			} else if( $pagenow == 'edit.php' ){

				$stati = get_post_stati();

				unset( $stati['auto-draft'] );

				$args = array(
				   'posts_per_page' => -1,
				   'post_type'      => $typenow,
				   'post_status'    => $stati
				);

				$posts = get_posts( $args );

				foreach( $posts as $post ) {

					$has_access = $this->user_can_access_post( $post );

					// If the user does not have access, redirect them
					if ( ! $has_access ) {
						$this->excluded_posts[] = $post->ID;
					}
				}
			}
		}
	}

	/**
	 * Check if a user can access an individual post
	 */
	public function user_can_access_post( $post ) {

		$current_user                   = wp_get_current_user();
		$roles                          = $current_user->roles;
		$do_redirect                    = false;
		$has_access                     = false;
		$mkdo_rcbr_roles                = get_post_meta( $post->ID, '_mkdo_rcbr_admin_roles', true );
		$mkdo_rcbr_override             = get_post_meta( $post->ID, '_mkdo_rcbr_admin_override', true );
		$mkdo_rcbr_restrict_sub_content = get_post_meta( $post->ID, '_mkdo_rcbr_restrict_admin_sub_content', true );

		// If the content is not a public override
		if ( 'public' != $mkdo_rcbr_override ) {

			// If it has roles, determine if it should do a redirect
			if ( ! empty( $mkdo_rcbr_roles ) ) {

				if ( 'content' == $mkdo_rcbr_restrict_sub_content || 'all' == $mkdo_rcbr_restrict_sub_content ) {
					$do_redirect = true;
				}

			// If it is sub content, determine if it should do a redirect
			} else if ( ! empty( $post->ancestors ) ) {
				foreach ( $post->ancestors as $parent ) {

					$mkdo_rcbr_roles                = get_post_meta( $parent, '_mkdo_rcbr_admin_roles', true );
					$mkdo_rcbr_override             = get_post_meta( $parent, '_mkdo_rcbr_admin_override', true );

					if ( ! empty( $mkdo_rcbr_roles ) || 'public' == $mkdo_rcbr_override ) {

						// If parent is public, no redirect
						if ( 'public' == $mkdo_rcbr_override ) {
							$do_redirect = false;
							break;
						}

						$mkdo_rcbr_restrict_sub_content = get_post_meta( $parent, '_mkdo_rcbr_restrict_admin_sub_content', true );

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

		return $has_access;
	}

	/**
	 * Update the post list to show only the posts we have access to
	 */
	public function update_post_list_access( $query ) {

	    if ( is_admin() && $query->is_main_query() ) {
			set_query_var( 'post__not_in', $this->excluded_posts );
	    }
	}

	/**
	 * Update the post list count to be accurate
	 */
	public function update_post_list_count( $counts ) {

		global $typenow;

		foreach( $counts as $key => &$count ) {

			$args = array(
			   'posts_per_page' => -1,
			   'post_type'      => $typenow,
			   'post__not_in'   => $this->excluded_posts,
			   'post_status'    => $key
			);

			$posts = get_posts( $args );
			$count = count( $posts );
		}

		return $counts;
	}
}
