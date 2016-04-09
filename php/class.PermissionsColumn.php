<?php

namespace mkdo\restrict_content_by_role;

/**
 * Class PermissionsColumn
 *
 * Creates a column detailing when permissions have been set
 *
 * @package mkdo\restrict_content_by_role
 */
class PermissionsColumn {

	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Do Work
	 */
	public function run() {

		// Get all the post types that this column needs to appear on
		$post_types          = get_option( 'mkdo_rcbr_post_types', array( 'page' ) );

		if( ! is_array( $post_types ) ) {
			$post_types = array();
		}

		// Loop through the post types, and add the functions to the appropraite filters
		foreach( $post_types as $key => $post_type ) {
			if( 'post' == $post_type || 'page' == $post_type ) {
				$post_type = $post_type . 's';
			}

			add_filter( 'manage_' . $post_type . '_columns', array( $this, 'add_column' ) );
			add_action( 'manage_' . $post_type . '_custom_column', array( $this, 'add_column_content' ), 10, 2 );
		}
	}

	/**
	 * Add a column
	 *
	 * @param array $columns Array of columns
	 */
	public function add_column( $columns ) {

		$columns['mkdo_rcbr_access'] = 'Access';

		return $columns;
	}

	/**
	 * Add column content
	 *
	 * @param string $column   Column name
	 * @param int    $post_id  ID of the post
	 */
	public function add_column_content( $column, $post_id ) {

		global $post, $wp_roles;

		switch ( $column ) {
			case 'mkdo_rcbr_access' :
				$icon_rendered                  = false;
				$found_action                   = false;
				$roles                          = $wp_roles->roles;
				$roles['public']                = array( 'name' => 'Public Access' );
				$all_roles                      = array_keys( $roles );
				$mkdo_rcbr_roles                = get_post_meta( $post_id, '_mkdo_rcbr_roles', true );
				$mkdo_rcbr_override             = get_post_meta( $post_id, '_mkdo_rcbr_override', true );
				$mkdo_rcbr_restrict_sub_content = get_post_meta( $post_id, '_mkdo_rcbr_restrict_sub_content', true );

				if( ! is_array( $mkdo_rcbr_roles ) ) {
					$mkdo_rcbr_roles = array();
				}

				$checked_roles                  = array_diff( $all_roles, $mkdo_rcbr_roles );

				// If content has been overridden
				if ( 'public' == $mkdo_rcbr_override ) {
					$icon_rendered = true;
					?>
						<i class="dashicons-before dashicons-unlock dashicon-large"></i>
						<span class="screen-reader-text">
							<?php esc_html_e( 'Public Access to Content', MKDO_RCBR_TEXT_DOMAIN );?>
						</span>
					<?php

				// If content has permissions set
				} else if ( ! empty( $checked_roles ) ) {

					if ( 'content' == $mkdo_rcbr_restrict_sub_content ) {
						$icon_rendered = true;
						?>
							<i class="dashicons-before dashicons-lock dashicon-large"></i>
							<span class="screen-reader-text">
								<?php esc_html_e( 'Restricted Access to Content', MKDO_RCBR_TEXT_DOMAIN );?>
							</span>
						<?php

					} else if ( 'all' == $mkdo_rcbr_restrict_sub_content ) {
						$icon_rendered = true;
						?>
							<i class="dashicons-before dashicons-lock dashicon-large"></i>
							<i class="dashicons-before dashicons-lock dashicon-small dashicon-right"></i>
							<span class="screen-reader-text">
								<?php esc_html_e( 'Restricted Access to Content and Sub Content', MKDO_RCBR_TEXT_DOMAIN );?>
							</span>
						<?php
					} else if ( 'sub' == $mkdo_rcbr_restrict_sub_content ) {
						$icon_rendered = true;
						?>
							<i class="dashicons-before dashicons-unlock dashicon-large"></i>
							<i class="dashicons-before dashicons-lock dashicon-small dashicon-right"></i>
							<span class="screen-reader-text">
								<?php esc_html_e( 'Restricted Access to Sub Content Only', MKDO_RCBR_TEXT_DOMAIN );?>
							</span>
						<?php
					}

				// If content has children, get a parent with permissions set
				} else if ( ! empty( $post->ancestors ) ) {
					foreach ( $post->ancestors as $parent ) {

						$mkdo_rcbr_roles                = get_post_meta( $parent, '_mkdo_rcbr_roles', true );
						$mkdo_rcbr_override             = get_post_meta( $parent, '_mkdo_rcbr_override', true );
						$mkdo_rcbr_restrict_sub_content = '';

						if ( ! empty( $mkdo_rcbr_roles ) || 'public' == $mkdo_rcbr_override ) {

							if ( 'public' == $mkdo_rcbr_override ) {
								break;
							}

							$mkdo_rcbr_restrict_sub_content = get_post_meta( $parent, '_mkdo_rcbr_restrict_sub_content', true );
							$mkdo_rcbr_restrict_media       = get_post_meta( $parent, '_mkdo_rcbr_restrict_media', true );

							if ( 'all' == $mkdo_rcbr_restrict_sub_content ) {
								$icon_rendered = true;
								?>
									<i class="dashicons-before dashicons-lock dashicon-large dashicon-muted"></i>
									<i class="dashicons-before dashicons-lock dashicon-small dashicon-right dashicon-dark"></i>
									<span class="screen-reader-text">
										<?php esc_html_e( 'Restricted Access to Content (via Parent)', MKDO_RCBR_TEXT_DOMAIN );?>
									</span>
								<?php
							} else if ( 'sub' == $mkdo_rcbr_restrict_sub_content ) {
								$icon_rendered = true;
								?>
									<i class="dashicons-before dashicons-unlock dashicon-large dashicon-muted"></i>
									<i class="dashicons-before dashicons-lock dashicon-small dashicon-right dashicon-dark"></i>
									<span class="screen-reader-text">
										<?php esc_html_e( 'Restricted Access to Content (via Parent)', MKDO_RCBR_TEXT_DOMAIN );?>
									</span>
								<?php
							}

							break;
						}
					}
				}

				// If no icon has been rendered
				if ( ! $icon_rendered ) {
					$icon_rendered = true;
					?>
						<i class="dashicons-before dashicons-unlock dashicon-large dashicon-muted"></i>
						<span class="screen-reader-text">
							<?php esc_html_e( 'Public Access to Content (No permissions set)', MKDO_RCBR_TEXT_DOMAIN );?>
						</span>
					<?php
				}

				break;
		}
	}
}
