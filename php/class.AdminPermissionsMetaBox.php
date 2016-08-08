<?php

namespace mkdo\restrict_content_by_role;

/**
 * Class AdminPermissionsMetaBox
 *
 * Creates a Meta Box to set admin permissions on content
 *
 * @package mkdo\restrict_content_by_role
 */
class AdminPermissionsMetaBox {

	private $nonce_key;
	private $nonce_action;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->nonce_key    = 'restrict_content_by_role_admin_nonce';
		$this->nonce_action = 'restrict_content_by_role_admin';
	}

	/**
	 * Do Work
	 */
	public function run() {
		add_filter( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
	}

	/**
	 * Add the Meta Box
	 */
	public function add_meta_box() {

		$meta_box_id         = 'mkdo_rcbr_admin';
		$meta_box_title      = __( 'Admin Access', MKDO_RCBR_TEXT_DOMAIN );
		$meta_box_post_types = get_option( 'mkdo_rcbr_admin_post_types', array( 'page' ) );
		$meta_box_context    = 'normal';
		$meta_box_priority   = 'low';

		if( ! is_array( $meta_box_post_types ) ) {
			$meta_box_post_types = array();
		}

		// Only show if the user can Manage Options
		if ( current_user_can( 'manage_options' ) ) {

			// Add the meta box to the appropraite post types
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

	/**
	 * Render the Meta Box
	 * @param  Object $post The Post Object
	 */
	public function render_meta_box( $post ) {

		global $wp_roles;

		$roles                                = $wp_roles->roles;
		$is_restricted_by_parent              = false;
		$parent_id                            = 0;
		$mkdo_rcbr_removed_admin_roles        = get_option( 'mkdo_rcbr_removed_admin_roles', array( 'administrator' ) );
		$mkdo_rcbr_admin_roles                = get_post_meta( $post->ID, '_mkdo_rcbr_admin_roles', true );
		$mkdo_rcbr_restrict_admin_sub_content = get_post_meta( $post->ID, '_mkdo_rcbr_restrict_admin_sub_content', true );
		$mkdo_rcbr_admin_override             = get_post_meta( $post->ID, '_mkdo_rcbr_admin_override', true );
		$is_hirachical                        = is_post_type_hierarchical( $post->post_type );

		if ( ! is_array( $mkdo_rcbr_removed_admin_roles ) ) {
			$mkdo_rcbr_removed_admin_roles = array();
		}

		foreach( $roles as $key => $role ) {
			if( in_array( $key, $mkdo_rcbr_removed_admin_roles ) ) {
				unset( $roles[ $key ] );
			}
		}

		if ( ! is_array( $mkdo_rcbr_admin_roles ) ) {
			$mkdo_rcbr_admin_roles = array();
			$default_roles         = array_keys( $wp_roles->roles );
			foreach( $default_roles as $role ) {
				$mkdo_rcbr_admin_roles[$role] = $role;
			}
		} else {
			$mkdo_rcbr_admin_roles[] = 'administrator';
		}

		if ( empty( $mkdo_rcbr_restrict_admin_sub_content ) ) {
			$mkdo_rcbr_restrict_admin_sub_content = 'content';
		}

		if ( empty( $mkdo_rcbr_admin_override ) ) {
			$mkdo_rcbr_admin_override = 'none';
		}

		// Is this post inheriting parent restrictions?
		if ( ! empty( $post->ancestors ) ) {
			foreach ( $post->ancestors as $parent ) {
				$parent_mkdo_rcbr_admin_roles = get_post_meta( $parent, '_mkdo_rcbr_admin_roles', true );
				if ( ! empty( $parent_mkdo_rcbr_admin_roles ) ) {
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
						<?php esc_html_e( 'Override Parent Permissions', MKDO_RCBR_TEXT_DOMAIN );?>
					</label>
				</p>
				<p class="field-description">
					<?php printf( esc_html__( '%sWarning:%s This content of this item has been %srestricted by one of its parents%s.', MKDO_RCBR_TEXT_DOMAIN ), '<strong>', '</strong>', '<strong>', '</strong>' );?>
					<?php printf( esc_html__( 'You can %sedit the parent content permissions%s, or choose an override option.', MKDO_RCBR_TEXT_DOMAIN ), '<a href="' . get_edit_post_link( $parent_id ) . '#mkdo_rcbr">', '</a>' );?>
				</p>
				<ul class="field-input">
					<li>
						<label>
							<input type="radio" name="mkdo_rcbr_admin_override" value="none" <?php if ( 'none' == $mkdo_rcbr_admin_override ) { echo ' checked="checked"'; } ?> />
							<?php esc_html_e( 'No override', MKDO_RCBR_TEXT_DOMAIN );?>
						</label>
					</li>
					<li>
						<label>
							<input type="radio" name="mkdo_rcbr_admin_override" value="public" <?php if ( 'public' == $mkdo_rcbr_admin_override ) { echo ' checked="checked"'; } ?> />
							<?php esc_html_e( 'Grant Public Access (Override permissions set by parent)', MKDO_RCBR_TEXT_DOMAIN );?>
						</label>
					</li>
					<li>
						<label>
							<input type="radio" name="mkdo_rcbr_admin_override" value="override" <?php if ( 'override' == $mkdo_rcbr_admin_override ) { echo ' checked="checked"'; } ?> />
							<?php esc_html_e( 'Set New Permissions (Override permissions set by parent)', MKDO_RCBR_TEXT_DOMAIN );?>
						</label>
					</li>
				</ul>
			</div>

		<?php } ?>

		<div class="field-group field-group-override">
			<div class="field field-checkbox-group field-restrict-content-user-roles">
				<p class="field-title">
					<label>
						<?php esc_html_e( 'Restrict Access by User Role', MKDO_RCBR_TEXT_DOMAIN );?>
					</label>
				</p>
				<p class="field-description">
					<?php
						printf( esc_html__( 'Choose the User Role(s) that you wish to restrict. %sNote:%s You cannot restrict administrators.', MKDO_RCBR_TEXT_DOMAIN ), '<strong>', '</strong>' );
					?>
				</p>
				<?php if( count( $roles ) > 0 ) { ?>
				<ul class="field-input">
					<?php
					foreach ( $roles as $key => $role ) {
						?>
						<li>
							<label>
								<input type="checkbox" name="mkdo_rcbr_admin_roles[]" value="<?php echo $key; ?>" <?php if ( ! in_array( $key, $mkdo_rcbr_admin_roles ) ) { echo ' checked="checked"'; } ?><?php echo $key == 'administrator' ? ' disabled="disabled"' : '';?> />
								<?php _e( $role['name'] );?>
							</label>
						</li>
						<?php
					}
					?>
				</ul>
				<?php } else { ?>
					<p><?php esc_html_e( 'There are no user roles available.', MKDO_RCBR_TEXT_DOMAIN );?></p>
				<?php } ?>
			</div>

			<?php if( $is_hirachical ) { ?>
				<div class="field field-radio-group field-restrict-access-sub-content">
					<p class="field-title">
						<label>
							<?php esc_html_e( 'Restrict Access to Sub Content', MKDO_RCBR_TEXT_DOMAIN );?>
						</label>
					</p>
					<p class="field-description">
						<?php esc_html_e( 'Choose how the content should be restricted.', MKDO_RCBR_TEXT_DOMAIN );?>
					</p>
					<ul class="field-input">
						<li>
							<label>
								<input type="radio" name="mkdo_rcbr_restrict_admin_sub_content" value="content" <?php if ( 'content' == $mkdo_rcbr_restrict_admin_sub_content ) { echo ' checked="checked"'; } ?> />
								<?php esc_html_e( 'Restrict Access to Content Only', MKDO_RCBR_TEXT_DOMAIN );?>
							</label>
						</li>
						<li>
							<label>
								<input type="radio" name="mkdo_rcbr_restrict_admin_sub_content" value="all" <?php if ( 'all' == $mkdo_rcbr_restrict_admin_sub_content ) { echo ' checked="checked"'; } ?> />
								<?php esc_html_e( 'Restrict Access to Content and Sub Content', MKDO_RCBR_TEXT_DOMAIN );?>
							</label>
						</li>
						<li>
							<label>
								<input type="radio" name="mkdo_rcbr_restrict_admin_sub_content" value="sub" <?php if ( 'sub' == $mkdo_rcbr_restrict_admin_sub_content ) { echo ' checked="checked"'; } ?> />
								<?php esc_html_e( 'Restrict Access to Sub Content Only', MKDO_RCBR_TEXT_DOMAIN );?>
							</label>
						</li>
					</ul>
				</div>
			<?php } else { ?>
				<input type="hidden" name="mkdo_rcbr_restrict_admin_sub_content" value="content" />
			<?php }?>

		</div> <!-- / field-group field-group-override -->

		<?php

		wp_nonce_field( $this->nonce_action, $this->nonce_key );

	}

	/**
	 * Save the Meta Box
	 *
	 * @param  int $post_id The Post ID
	 */
	public function save_meta_box( $post_id ) {

		global $wp_roles;

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

		$all_roles                            = array_keys( $wp_roles->roles );
		$mkdo_rcbr_admin_roles                = isset( $_POST['mkdo_rcbr_admin_roles'] )                ?  $_POST['mkdo_rcbr_admin_roles'] : array();
		$mkdo_rcbr_restrict_admin_sub_content = isset( $_POST['mkdo_rcbr_restrict_admin_sub_content'] ) ?  sanitize_text_field( $_POST['mkdo_rcbr_restrict_admin_sub_content'] ) : 'content';
		$mkdo_rcbr_admin_override             = isset( $_POST['mkdo_rcbr_admin_override'] )             ?  sanitize_text_field( $_POST['mkdo_rcbr_admin_override'] ) : null;

		foreach ( $mkdo_rcbr_admin_roles as &$role ) {
			$role = sanitize_text_field( $role );
		}

		// If we are not overriding, get rid of the overrides
		if ( ! empty( $mkdo_rcbr_admin_override ) && 'override' != $mkdo_rcbr_admin_override ) {
			$mkdo_rcbr_admin_roles                = array();
			$mkdo_rcbr_restrict_admin_sub_content = 'content';
		}

		$checked_roles = array_diff( $all_roles, $mkdo_rcbr_admin_roles );

		$checked_roles[] = 'administrator';

		update_post_meta( $post_id, '_mkdo_rcbr_admin_roles', $checked_roles );
		update_post_meta( $post_id, '_mkdo_rcbr_restrict_admin_sub_content', $mkdo_rcbr_restrict_admin_sub_content );
		update_post_meta( $post_id, '_mkdo_rcbr_admin_override', $mkdo_rcbr_admin_override );

	}
}
