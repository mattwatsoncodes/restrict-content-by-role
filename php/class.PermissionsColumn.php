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

	private $text_domain;

	function __construct() {
		$this->text_domain  = 'restrict-content-by-role';
	}

	function add_column( $columns ) {

		$columns['mkdo_rcbr_access'] = 'Access';

		return $columns;
	}

	function add_column_content( $column, $post_id ) {

		global $post;

		switch ( $column ) {
			case 'mkdo_rcbr_access' :
				$icon_rendered                  = false;
				$found_action                   = false;

				$mkdo_rcbr_roles                = get_post_meta( $post_id, '_mkdo_rcbr_roles', true );
				$mkdo_rcbr_override             = get_post_meta( $post_id, '_mkdo_rcbr_override', true );
				$mkdo_rcbr_restrict_sub_content = get_post_meta( $post_id, '_mkdo_rcbr_restrict_sub_content', true );

				// If content has been overridden
				if ( 'public' == $mkdo_rcbr_override ) {
					$icon_rendered = true;
					?>
						<i class="dashicons-before dashicons-unlock icon-large"></i>
						<span class="screen-reader-text">
							<?php esc_html_e( 'Public Access to Content', $this->text_domain );?>
						</span>
					<?php

				// If content has permissions set
				} else if ( ! empty( $mkdo_rcbr_roles ) ) {

					if ( 'content' == $mkdo_rcbr_restrict_sub_content ) {
						$icon_rendered = true;
						?>
							<i class="dashicons-before dashicons-lock icon-large"></i>
							<span class="screen-reader-text">
								<?php esc_html_e( 'Restricted Access to Content', $this->text_domain );?>
							</span>
						<?php

					} else if ( 'all' == $mkdo_rcbr_restrict_sub_content ) {
						$icon_rendered = true;
						?>
							<i class="dashicons-before dashicons-lock icon-large"></i>
							<i class="dashicons-before dashicons-lock icon-small icon-right"></i>
							<span class="screen-reader-text">
								<?php esc_html_e( 'Restricted Access to Content and Sub Content', $this->text_domain );?>
							</span>
						<?php
					} else if ( 'sub' == $mkdo_rcbr_restrict_sub_content ) {
						$icon_rendered = true;
						?>
							<i class="dashicons-before dashicons-unlock icon-large"></i>
							<i class="dashicons-before dashicons-lock icon-small icon-right"></i>
							<span class="screen-reader-text">
								<?php esc_html_e( 'Restricted Access to Sub Content Only', $this->text_domain );?>
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
									<i class="dashicons-before dashicons-lock icon-large icon-muted"></i>
									<i class="dashicons-before dashicons-lock icon-small icon-right icon-dark"></i>
									<span class="screen-reader-text">
										<?php esc_html_e( 'Restricted Access to Content (via Parent)', $this->text_domain );?>
									</span>
								<?php
							} else if ( 'sub' == $mkdo_rcbr_restrict_sub_content ) {
								$icon_rendered = true;
								?>
									<i class="dashicons-before dashicons-unlock icon-large icon-muted"></i>
									<i class="dashicons-before dashicons-lock icon-small icon-right icon-dark"></i>
									<span class="screen-reader-text">
										<?php esc_html_e( 'Restricted Access to Content (via Parent)', $this->text_domain );?>
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
						<i class="dashicons-before dashicons-unlock icon-large icon-muted"></i>
						<span class="screen-reader-text">
							<?php esc_html_e( 'Public Access to Content (No permissions set)', $this->text_domain );?>
						</span>
					<?php
				}

				break;
		}
	}

	public function run() {

		$post_types          = get_option( 'mkdo_rcbr_post_types', array( 'page' ) );

		if( ! is_array( $post_types ) ) {
			$post_types = array();
		}

		foreach( $post_types as $key => $post_type ) {
			if( 'post' == $post_type || 'page' == $post_type ) {
				$post_type = $post_type . 's';
			}

			add_filter( 'manage_' . $post_type . '_columns', array( $this, 'add_column' ) );
			add_action( 'manage_' . $post_type . '_custom_column', array( $this, 'add_column_content' ), 10, 2 );
		}
	}
}
