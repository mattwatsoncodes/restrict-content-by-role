<?php

namespace mkdo\restrict_content_by_role;

/**
 * Class MenuAccess
 *
 * Check if user has access to various peices of content within the website,
 * and hide from menus if they dont
 *
 * @package mkdo\restrict_content_by_role
 */
class MenuAccess {

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'wp_get_nav_menu_items', array( $this, 'menu_control' ), null, 3 );
	}

	/**
	 * Check if user has access
	 */
	public function menu_control( $items, $menu, $args ) {

		$mkdo_rcbr_hide_from_menus = get_option( 'mkdo_rcbr_hide_from_menus', 'false' );
		if ( empty( $mkdo_rcbr_hide_from_menus ) || 'false' === $mkdo_rcbr_hide_from_menus || is_admin() ) {
			return $items;
		}

		global $wp_roles;

		$do_hide                        = false;
		$has_access                     = false;
		$current_user                   = wp_get_current_user();
		$roles                          = $current_user->roles;
		$mkdo_rcbr_removed_public_roles = get_option( 'mkdo_rcbr_removed_public_roles' );

		foreach ( $items as $menu_id => $item ) {

			$object = get_post( $item->object_id );

			if ( is_object( $object ) && 'nav_menu_item' !== $object->post_type ) {

				$do_hide                        = false;
				$has_access                     = false;
	            $mkdo_rcbr_roles                = get_post_meta( $item->object_id, '_mkdo_rcbr_roles', true );
	            $mkdo_rcbr_override             = get_post_meta( $item->object_id, '_mkdo_rcbr_override', true );
	            $mkdo_rcbr_restrict_sub_content = get_post_meta( $item->object_id, '_mkdo_rcbr_restrict_sub_content', true );

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

				// If the content is not a public override
				if ( 'public' != $mkdo_rcbr_override ) {

					// If it has roles, determine if it should do a hide
					if ( ! empty( $role_check ) ) {

						if ( 'content' == $mkdo_rcbr_restrict_sub_content || 'all' == $mkdo_rcbr_restrict_sub_content ) {
							$do_hide = true;
						}

					// If it is sub content, determine if it should do a hide
					} else if ( ! empty( $object->ancestors ) ) {
						foreach ( $object->ancestors as $parent ) {

	                        $mkdo_rcbr_roles                = get_post_meta( $parent, '_mkdo_rcbr_roles', true );
	                        $mkdo_rcbr_override             = get_post_meta( $parent, '_mkdo_rcbr_override', true );
	                        $mkdo_rcbr_restrict_sub_content = '';

							if( ! is_array( $mkdo_rcbr_roles ) ) {
								$mkdo_rcbr_roles = array();
							}


	                        $mkdo_rcbr_roles                = array_diff( $mkdo_rcbr_roles, $mkdo_rcbr_removed_public_roles );
							$role_check                     = array_diff( $all_roles, $mkdo_rcbr_roles );

							if ( ! empty( $role_check ) || 'public' == $mkdo_rcbr_override ) {

								// If parent is public, no hide
								if ( 'public' == $mkdo_rcbr_override ) {
									$do_hide = false;
									break;
								}

								$mkdo_rcbr_restrict_sub_content = get_post_meta( $parent, '_mkdo_rcbr_restrict_sub_content', true );

								if ( 'all' == $mkdo_rcbr_restrict_sub_content || 'sub' == $mkdo_rcbr_restrict_sub_content ) {
									$do_hide = true;
								}

								break;
							}
						}
					}
				}

				// If we are not hiding, the user will have access to the site
				if ( ! $do_hide ) {
					$has_access = true;

				// Otherwise check that they are in the right group
				} else if ( $do_hide ) {

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

				// If the user does not have access, hide the menu item
				if ( ! $has_access ) {
					unset( $items[ $menu_id ] );
				}
			}
		}

		return $items;
	}

}
