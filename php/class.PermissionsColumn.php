<?php

namespace mkdo\restrict_content_by_role;

/**
 * Class PermissionsColumn
 *
 *
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
				$mkdo_rcbr_restrict_media       = get_post_meta( $post_id, '_mkdo_rcbr_restrict_media', true );

				if ( $mkdo_rcbr_override == 'public' ) {
					$icon_rendered = true;
					?>
						<i class="dashicons-before dashicons-unlock icon-large"></i>
						<span class="screen-reader-text">Public Access to Content</span>
					<?php
				} else if ( ! empty( $mkdo_rcbr_roles ) ) {

					if ( $mkdo_rcbr_restrict_sub_content == 'content' ) {
						$icon_rendered = true;
						?>
							<i class="dashicons-before dashicons-lock icon-large"></i>
							<span class="screen-reader-text">Restricted Access to Content</span>
						<?php
					} else if ( $mkdo_rcbr_restrict_sub_content == 'all' ) {
						$icon_rendered = true;
						?>
							<i class="dashicons-before dashicons-lock icon-large"></i>
							<i class="dashicons-before dashicons-lock icon-small icon-right"></i>
							<span class="screen-reader-text">Restricted Access to Content and Sub Content</span>
						<?php
					} else if ( $mkdo_rcbr_restrict_sub_content == 'sub' ) {
						$icon_rendered = true;
						?>
							<i class="dashicons-before dashicons-unlock icon-large"></i>
							<i class="dashicons-before dashicons-lock icon-small icon-right"></i>
							<span class="screen-reader-text">Restricted Access to Sub Content Only</span>
						<?php
					}
				} else if ( ! empty( $post->ancestors ) ) {
					foreach ( $post->ancestors as $parent ) {

						$mkdo_rcbr_roles                = get_post_meta( $parent, '_mkdo_rcbr_roles', true );
						$mkdo_rcbr_restrict_sub_content = '';
						$mkdo_rcbr_restrict_media       = '';

						if ( ! empty( $mkdo_rcbr_roles ) ) {
							$mkdo_rcbr_restrict_sub_content = get_post_meta( $parent, '_mkdo_rcbr_restrict_sub_content', true );
							$mkdo_rcbr_restrict_media       = get_post_meta( $parent, '_mkdo_rcbr_restrict_media', true );

							if ( $mkdo_rcbr_restrict_sub_content == 'all' ) {
								$icon_rendered = true;
								?>
									<i class="dashicons-before dashicons-lock icon-large icon-muted"></i>
									<i class="dashicons-before dashicons-lock icon-small icon-right icon-dark"></i>
									<span class="screen-reader-text">Restricted Access to Content (via Parent)</span>
								<?php
							} else if ( $mkdo_rcbr_restrict_sub_content == 'sub' ) {
								$icon_rendered = true;
								?>
									<i class="dashicons-before dashicons-unlock icon-large icon-muted"></i>
									<i class="dashicons-before dashicons-lock icon-small icon-right icon-dark"></i>
									<span class="screen-reader-text">Restricted Access to Content (via Parent)</span>
								<?php
							}

							break;
						}
					}
				}

				if ( ! $icon_rendered ) {
					$icon_rendered = true;
					?>
						<i class="dashicons-before dashicons-unlock icon-large icon-muted"></i>
						<span class="screen-reader-text">Public Access</span>
					<?php
				}

				break;
		}
	}

	public function run() {
		add_filter( 'manage_pages_columns', array( $this, 'add_column' ) );
		add_action( 'manage_pages_custom_column', array( $this, 'add_column_content' ), 10, 2 );
	}
}
