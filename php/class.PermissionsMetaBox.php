<?php

namespace mkdo\restrict_content_by_role;

/**
 * Class PermissionsMetaBox
 *
 * Creates a Meta Box to set permissions on content
 *
 * @package mkdo\restrict_content_by_role
 */
class PermissionsMetaBox {

	private $text_domain;
	private $nonce_key;
	private $nonce_action;

	function __construct() {

		$this->text_domain  = 'restrict-content-by-role';
		$this->nonce_key    = 'restrict_content_by_role_nonce';
		$this->nonce_action = 'restrict_content_by_role';
	}

	public function add_meta_box() {

		$post_types          = get_option( 'mkdo_rcbr_post_types', array( 'page' ) );

		if( ! is_array( $post_types ) ) {
			$post_types = array();
		}

		$meta_box_id         = 'mkdo_rcbr';
		$meta_box_title      = __( 'User Role Access', $this->text_domain );
		$meta_box_post_types = $post_types;
		$meta_box_context    = 'normal';
		$meta_box_priority   = 'low';

		if ( current_user_can( 'manage_options' ) ) {
			foreach ( $meta_box_post_types as $post_type ) {

				// Check that the post type is public
				$post_type_object = get_post_type_object( $post_type );
				if ( $post_type_object->public ) {
					add_meta_box(
						$meta_box_id,
						$meta_box_title,
						array( $this, 'render_meta_box' ),
						$post_type,
						$meta_box_context,
						$meta_box_priority
					);
				}
			}
		}
	}

	public function render_meta_box( $post ) {

		global $wp_roles;

		$is_restricted_by_parent        = false;
		$parent_id                      = 0;
		$roles                          = $wp_roles->roles;
		$mkdo_rcbr_roles                = get_post_meta( $post->ID, '_mkdo_rcbr_roles', true );
		$mkdo_rcbr_restrict_sub_content = get_post_meta( $post->ID, '_mkdo_rcbr_restrict_sub_content', true );
		$mkdo_rcbr_restrict_media       = get_post_meta( $post->ID, '_mkdo_rcbr_restrict_media', true );
		$mkdo_rcbr_override             = get_post_meta( $post->ID, '_mkdo_rcbr_override', true );
		$is_hirachical                  = is_post_type_hierarchical( $post->post_type );

		if ( ! is_array( $mkdo_rcbr_roles ) ) {
			$mkdo_rcbr_roles = array();
		}

		if ( empty( $mkdo_rcbr_restrict_sub_content ) ) {
			$mkdo_rcbr_restrict_sub_content = 'content';
		}

		if ( empty( $mkdo_rcbr_override ) ) {
			$mkdo_rcbr_override = 'none';
		}

		// Is this post inheriting parent restrictions?
		if ( ! empty( $post->ancestors ) ) {
			foreach ( $post->ancestors as $parent ) {
				$parent_mkdo_rcbr_roles = get_post_meta( $parent, '_mkdo_rcbr_roles', true );
				if ( ! empty( $parent_mkdo_rcbr_roles ) ) {
					$is_restricted_by_parent = true;
					$parent_id               = $parent;
					break;
				}
			}
		}

		if ( $is_restricted_by_parent ) { ?>

			<div class="field field-radio-group field-override-parent-permissions">
				<p class="field-title">
					<label>
						<?php esc_html_e( 'Override Parent Permissions', $this->text_domain );?>
					</label>
				</p>
				<p class="field-description">
					<?php printf( esc_html__( '%sWarning:%s This content of this item has been %srestricted by one of its parents%s.', $this->text_domain ), '<strong>', '</strong>', '<strong>', '</strong>' );?>
					<?php printf( esc_html__( 'You can %sedit the parent content permissions%s, or choose an override option.', $this->text_domain ), '<a href="' . get_edit_post_link( $parent_id ) . '#mkdo_rcbr">', '</a>' );?>
				</p>
				<ul class="field-input">
					<li>
						<label>
							<input type="radio" name="mkdo_rcbr_override" value="none" <?php if ( 'none' == $mkdo_rcbr_override ) { echo ' checked="checked"'; } ?> />
							<?php esc_html_e( 'No override', $this->text_domain );?>
						</label>
					</li>
					<li>
						<label>
							<input type="radio" name="mkdo_rcbr_override" value="public" <?php if ( 'public' == $mkdo_rcbr_override ) { echo ' checked="checked"'; } ?> />
							<?php esc_html_e( 'Grant Public Access (Override permissions set by parent)', $this->text_domain );?>
						</label>
					</li>
					<li>
						<label>
							<input type="radio" name="mkdo_rcbr_override" value="override" <?php if ( 'override' == $mkdo_rcbr_override ) { echo ' checked="checked"'; } ?> />
							<?php esc_html_e( 'Set New Permissions (Override permissions set by parent)', $this->text_domain );?>
						</label>
					</li>
				</ul>
			</div>

		<?php } ?>

		<div class="field-group field-group-override">
			<div class="field field-checkbox-group field-restrict-content-user-roles">
				<p class="field-title">
					<label>
						<?php esc_html_e( 'Restrict Content to User Roles', $this->text_domain );?>
					</label>
				</p>
				<p class="field-description">
					<?php
					if( $is_hirachical ) {
						esc_html_e( 'Select the User Roles that are able to view this content (or its sub content). If no roles are selected the content will be publicly accessible.', $this->text_domain );
					} else {
						esc_html_e( 'Select the User Roles that are able to view this content. If no roles are selected the content will be publicly accessible.', $this->text_domain );
					}
					?>
				</p>
				<ul class="field-input">
					<?php
					foreach ( $roles as $key => $role ) {
						?>
						<li>
							<label>
								<input type="checkbox" name="mkdo_rcbr_roles[]" value="<?php echo $key; ?>" <?php if ( in_array( $key, $mkdo_rcbr_roles ) ) { echo ' checked="checked"'; } ?> />
								<?php echo $role['name'];?>
							</label>
						</li>
						<?php
					}
					?>
				</ul>
			</div>

			<?php if( $is_hirachical ) { ?>
				<div class="field field-radio-group field-restrict-access-sub-content">
					<p class="field-title">
						<label>
							<?php esc_html_e( 'Restrict Access to Sub Content', $this->text_domain );?>
						</label>
					</p>
					<p class="field-description">
						<?php esc_html_e( 'Select the User Roles that are able to view this content. If no roles are selected the content will be publicly accessible.', $this->text_domain );?>
					</p>
					<ul class="field-input">
						<li>
							<label>
								<input type="radio" name="mkdo_rcbr_restrict_sub_content" value="content" <?php if ( 'content' == $mkdo_rcbr_restrict_sub_content ) { echo ' checked="checked"'; } ?> />
								<?php esc_html_e( 'Restrict Access to Content Only', $this->text_domain );?>
							</label>
						</li>
						<li>
							<label>
								<input type="radio" name="mkdo_rcbr_restrict_sub_content" value="all" <?php if ( 'all' == $mkdo_rcbr_restrict_sub_content ) { echo ' checked="checked"'; } ?> />
								<?php esc_html_e( 'Restrict Access to Content and Sub Content', $this->text_domain );?>
							</label>
						</li>
						<li>
							<label>
								<input type="radio" name="mkdo_rcbr_restrict_sub_content" value="sub" <?php if ( 'sub' == $mkdo_rcbr_restrict_sub_content ) { echo ' checked="checked"'; } ?> />
								<?php esc_html_e( 'Restrict Access to Sub Content Only', $this->text_domain );?>
							</label>
						</li>
					</ul>
				</div>
			<?php } else { ?>
				<input type="hidden" name="mkdo_rcbr_restrict_sub_content" value="content" />
			<?php }?>

			<div class="field field-checkbox field-restrict-access-media">
				<p class="field-title">
					<label>
						<?php esc_html_e( 'Restrict Access to Media', $this->text_domain );?>
					</label>
				</p>
				<p class="field-description">
					<?php printf( esc_html__( 'If you select %s\'Restrict Access to Media\'%s, then any media which is not publicly available elsewhere, and located in the %s\'content\'%s will be restricted to the above settings.', $this->text_domain ), '<strong>', '</strong>', '<strong>', '</strong>' );?>
				</p>
				<ul class="field-input">
					<li>
						<label>
							<input type="checkbox" name="mkdo_rcbr_restrict_media" value="true" <?php if ( ! empty( $mkdo_rcbr_restrict_media ) ) { echo ' checked="checked"'; } ?> />
							<?php esc_html_e( 'Restrict Access to Media', $this->text_domain );?>
						</label>
					</li>
				</ul>
			</div>

		</div> <!-- / field-group field-group-override -->

		<?php

		wp_nonce_field( $this->nonce_action, $this->nonce_key );

	}

	public function save_meta_box( $post_id ) {

		// If it is just a revision don't worry about it
		if ( wp_is_post_revision( $post_id ) ) {
			return $post_id;
		}

		// Check it's not an auto save routine
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Verify the nonce to defend against XSS
		if ( ! isset( $_POST[$this->nonce_key] ) || ! wp_verify_nonce( $_POST[$this->nonce_key], $this->nonce_action ) ) {
			return $post_id;
		}

		// Check that the current user has permission to edit the post
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$mkdo_rcbr_roles                = isset( $_POST['mkdo_rcbr_roles'] )                ?  $_POST['mkdo_rcbr_roles'] : array();
		$mkdo_rcbr_restrict_sub_content = isset( $_POST['mkdo_rcbr_restrict_sub_content'] ) ?  sanitize_text_field( $_POST['mkdo_rcbr_restrict_sub_content'] ) : 'content';
		$mkdo_rcbr_restrict_media       = isset( $_POST['mkdo_rcbr_restrict_media'] )       ?  true : false;
		$mkdo_rcbr_override    		    = isset( $_POST['mkdo_rcbr_override'] )             ?  sanitize_text_field( $_POST['mkdo_rcbr_override'] ) : null;

		foreach ( $mkdo_rcbr_roles as &$role ) {
			$role = sanitize_text_field( $role );
		}

		// If we are not overriding, get rid of the overrides
		if ( ! empty( $mkdo_rcbr_override ) && 'override' != $mkdo_rcbr_override ) {
			$mkdo_rcbr_roles                = array();
			$mkdo_rcbr_restrict_sub_content = 'content';
			$mkdo_rcbr_restrict_media       = false;
		}

		update_post_meta( $post_id, '_mkdo_rcbr_roles', $mkdo_rcbr_roles );
		update_post_meta( $post_id, '_mkdo_rcbr_restrict_sub_content', $mkdo_rcbr_restrict_sub_content );
		update_post_meta( $post_id, '_mkdo_rcbr_restrict_media', $mkdo_rcbr_restrict_media );
		update_post_meta( $post_id, '_mkdo_rcbr_override', $mkdo_rcbr_override );

	}

	public function run() {
		add_filter( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
	}
}
