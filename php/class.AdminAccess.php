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
		add_filter( 'wp_count_posts', array( $this, 'update_post_list_count' ) );
		add_filter( 'get_pages', array( $this, 'update_tree_view_post_list_access' ), 0, 2 );
		add_filter( 'page_attributes_dropdown_pages_args', array( $this, 'update_parent_dropdown' ), 0, 2 );
		add_filter( 'wp_insert_post_data' , array( $this, 'user_can_update_child_pages' ) , '0', 2 );

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
			}

			// Get all post statuses
			$stati = get_post_stati();

			// We dont want to work with auto-draft
			unset( $stati['auto-draft'] );

			// If the current post type is empty, check all post types
			if( empty( $typenow ) ) {
				$typenow = 'any';
			}

			$args = array(
			   'posts_per_page' => -1,
			   'post_type'      => $typenow,
			   'post_status'    => $stati
			);

			$excluded_posts = get_posts( $args );

			foreach( $excluded_posts as $excluded_post ) {

				$has_access = $this->user_can_access_post( $excluded_post );

				// If the user does not have access, redirect them
				if ( ! $has_access ) {
					$this->excluded_posts[] = $excluded_post->ID;
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

	/**
	 * Update the post list access within the Page Tree View plugin to show only the posts we have access to
	 */
	public function update_tree_view_post_list_access( $pages, $get_posts_args ) {

		$backtrace = debug_backtrace();

		// If the function that called it is 'cms_tpv_get_pages', then filter the posts
		if( is_admin() && isset( $backtrace[3] ) && isset( $backtrace[3]['function'] ) && 'cms_tpv_get_pages' == $backtrace[3]['function'] ) {
            $get_posts_args['fields']       = 'object';
            $get_posts_args['post__not_in'] = $this->excluded_posts;
            $pages                          = get_posts( $get_posts_args );
		}

	    return $pages;
	}

	/**
	 * Update the parent drop down list to show only posts the user can access
	 */
	public function update_parent_dropdown( $dropdown_args, $post ) {

		$mkdo_rcbr_prevent_restricted_child = get_option( 'mkdo_rcbr_prevent_restricted_child', false );

		if( $mkdo_rcbr_prevent_restricted_child ) {
			$excluded_posts  = $this->excluded_posts;
			$include_parents = get_post_ancestors( $post );

			if( is_array( $include_parents ) ) {
				foreach( $include_parents as $parent ) {
					if( ( $key = array_search( $parent, $excluded_posts ) ) !== false ) {
					    unset( $excluded_posts[ $key ] );
					}
				}
			}

			if( is_admin() ) {
				$dropdown_args['exclude'] = implode( ',', $excluded_posts );
			}
		}

		return $dropdown_args;
	}

	/**
	 * Warn user if they cannot update child pages
	 */
	public function user_can_update_child_pages( $data , $postarr ) {

		$mkdo_rcbr_prevent_restricted_child = get_option( 'mkdo_rcbr_prevent_restricted_child', false );

		if( $mkdo_rcbr_prevent_restricted_child ) {

		    $post = get_post();
			$old_parent = null;
			if ( is_object( $post ) ) {
			    $old_parent = $post->post_parent;
			}
		    $new_parent = $data['post_parent'];

		    if ( $old_parent != $new_parent ) {

				$parent = get_post( $new_parent );

				if( ! $this->user_can_access_post( $parent ) ) {
		        	$data['post_parent'] = $old_parent;
					$message = '';
					$message .= __( sprintf( '%sWarning%s', '<h1>', '</h1>' ), MKDO_RCBR_TEXT_DOMAIN );
					$message .= __( sprintf( '%sYou do not have permission to add content under the selected parent.%s', '<p>', '</p>' ) , MKDO_RCBR_TEXT_DOMAIN );
					$message .= __( sprintf( '%sBack to previous page%s', '<p><a href="' . get_edit_post_link( $post ) . '">', '</a></p>' ) , MKDO_RCBR_TEXT_DOMAIN );
					wp_die( $message );
				}
		    }
		}

	    return $data;
	}
}
