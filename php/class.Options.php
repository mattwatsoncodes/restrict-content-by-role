<?php

namespace mkdo\restrict_content_by_role;

/**
 * Class Options
 *
 * Options page for the plugin
 *
 * @package mkdo\restrict_content_by_role
 */
class Options {

	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'admin_init', array( $this, 'init_options_page' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'plugin_action_links_' . plugin_basename( MKDO_RCBR_ROOT ) , array( $this, 'add_setings_link' ) );
		add_action( 'admin_init', array( $this, 'reset_roles' ) );
	}

	/**
	 * Initialise the Options Page
	 */
	public function init_options_page() {

		// Register Settings
		register_setting( 'mkdo_rcbr_settings_group', 'mkdo_rcbr_post_types' );
		register_setting( 'mkdo_rcbr_settings_group', 'mkdo_rcbr_admin_post_types' );
		register_setting( 'mkdo_rcbr_settings_group', 'mkdo_rcbr_removed_public_roles' );
		register_setting( 'mkdo_rcbr_settings_group', 'mkdo_rcbr_removed_admin_roles' );
		register_setting( 'mkdo_rcbr_settings_group', 'mkdo_rcbr_prevent_restricted_child' );
		register_setting( 'mkdo_rcbr_settings_group', 'mkdo_rcbr_default_restrict_message' );
		register_setting( 'mkdo_rcbr_settings_group', 'mkdo_rcbr_default_redirect' );
		register_setting( 'mkdo_rcbr_settings_group', 'mkdo_rcbr_redirect_to_original' );
		register_setting( 'mkdo_rcbr_settings_group', 'mkdo_rcbr_hide_from_menus' );

		// Add sections
		add_settings_section( 'mkdo_rcbr_post_types_section', esc_html__( 'Choose Public Post Types', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_post_types_section_cb' ), 'mkdo_rcbr_settings' );
		add_settings_section( 'mkdo_rcbr_admin_post_types_section', esc_html__( 'Choose Admin Post Types', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_admin_post_types_section_cb' ), 'mkdo_rcbr_settings' );
		add_settings_section( 'mkdo_rcbr_removed_public_roles_section', esc_html__( 'Public Access Roles', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_removed_public_roles_section_cb' ), 'mkdo_rcbr_settings' );
		add_settings_section( 'mkdo_rcbr_removed_admin_roles_section', esc_html__( 'Admin Access Roles', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_removed_admin_roles_section_cb' ), 'mkdo_rcbr_settings' );
		add_settings_section( 'mkdo_rcbr_prevent_restricted_child', esc_html__( 'Prevent Restricted Content Child Pages', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_prevent_restricted_child_cb' ), 'mkdo_rcbr_settings' );
		add_settings_section( 'mkdo_rcbr_default_restrict_message_section', esc_html__( 'Restrict Message', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_default_restrict_message_section_cb' ), 'mkdo_rcbr_settings' );
		add_settings_section( 'mkdo_rcbr_reset_page_permissions', esc_html__( 'Reset Role Permissions', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_reset_page_permissions_cb' ), 'mkdo_rcbr_settings' );

    	// Add fields to a section
		add_settings_field( 'mkdo_rcbr_post_types_select', esc_html__( 'Choose Public Post Types:', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_post_types_select_cb' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_post_types_section' );
		add_settings_field( 'mkdo_rcbr_hide_from_menus', esc_html__( 'Hide from Menus:', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_hide_from_menus_cb' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_post_types_section' );
		add_settings_field( 'mkdo_rcbr_admin_post_types_select', esc_html__( 'Choose Admin Post Types:', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_admin_post_types_select_cb' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_admin_post_types_section' );
		add_settings_field( 'mkdo_rcbr_removed_public_roles_select', esc_html__( 'Exclude Public Roles:', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_removed_public_roles_select_cb' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_removed_public_roles_section' );
		add_settings_field( 'mkdo_rcbr_removed_admin_roles_select', esc_html__( 'Exclude Admin Roles:', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_removed_admin_roles_select_cb' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_removed_admin_roles_section' );
		add_settings_field( 'mkdo_rcbr_prevent_restricted_child_select', esc_html__( 'Prevent Child Pages:', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_prevent_restricted_child_select_cb' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_prevent_restricted_child' );
		add_settings_field( 'mkdo_rcbr_default_restrict_message', esc_html__( 'Restriction Message:', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_default_restrict_message_db' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_default_restrict_message_section' );
		add_settings_field( 'mkdo_rcbr_redirect_to_original', esc_html__( 'Redirect to Orignal:', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_redirect_to_original_cb' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_default_restrict_message_section' );
		add_settings_field( 'mkdo_rcbr_default_redirect', esc_html__( 'Redirect URL:', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_default_redirect_cb' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_default_restrict_message_section' );
		add_settings_field( 'mkdo_rcbr_reset_page_permission_buttons', esc_html__( 'Reset Roles:', MKDO_RCBR_TEXT_DOMAIN  ), array( $this, 'mkdo_rcbr_reset_page_permission_buttons_cb' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_reset_page_permissions' );
	}

	/**
	 * Call back for the post_type section
	 */
	public function mkdo_rcbr_post_types_section_cb() {
		echo '<p>';
		esc_html_e( 'Select the Post Types that you wish to activate the Public Access meta box on.', MKDO_RCBR_TEXT_DOMAIN  );
		echo '</p>';
	}

	/**
	 * Call back for the admin post_type section
	 */
	public function mkdo_rcbr_admin_post_types_section_cb() {
		echo '<p>';
		esc_html_e( 'Select the Post Types that you wish to activate the Admin Access meta box on.', MKDO_RCBR_TEXT_DOMAIN  );
		echo '</p>';
	}

	/**
	 * Call back for the removed public roles section
	 */
	public function mkdo_rcbr_removed_public_roles_section_cb() {
		echo '<p>';
		esc_html_e( 'Check the user roles that you do not wish to be available for selection via the Public Access metabox.', MKDO_RCBR_TEXT_DOMAIN  );
		echo '</p>';
	}

	/**
	 * Call back for the removed admin roles section
	 */
	public function mkdo_rcbr_removed_admin_roles_section_cb() {
		echo '<p>';
		esc_html_e( 'Check the user roles that you do not wish to be available for selection via the Admin Access metabox.', MKDO_RCBR_TEXT_DOMAIN  );
		echo '</p>';
	}

	/**
	 * Call back for the prevent child section
	 */
	public function mkdo_rcbr_prevent_restricted_child_cb() {
		echo '<p>';
		esc_html_e( 'Check the box to prevent users from adding child pages to content that they are restricted from editing', MKDO_RCBR_TEXT_DOMAIN  );
		echo '</p>';
	}

	/**
	 * Call back for the restrict message section
	 */
	public function mkdo_rcbr_default_restrict_message_section_cb() {
		echo '<p>';
		esc_html_e( 'Add the message that you wish to appear on the Login Page for restricted users. Or alternativly redirect the restricted user to a URL of your choice.', MKDO_RCBR_TEXT_DOMAIN  );
		echo '</p>';
	}

	/**
	 * Call back for the reset permissions
	 */
	public function mkdo_rcbr_reset_page_permissions_cb() {
		echo '<p>';
		esc_html_e( 'From time to time you may wish to remove all permissions set for a certain role. Use this section to chose the roles that you would like to reset.', MKDO_RCBR_TEXT_DOMAIN  );
		echo '</p>';
	}

	/**
	 * Call back for the post_type selector
	 */
	public function mkdo_rcbr_post_types_select_cb() {

		$post_type_args = array(
			'public' => true,
		);
		$post_types           = get_post_types( $post_type_args );
		$mkdo_rcbr_post_types = get_option( 'mkdo_rcbr_post_types', array( 'page' ) );

		unset( $post_types['attachment'] );

		if ( ! is_array( $mkdo_rcbr_post_types ) ) {
			$mkdo_rcbr_post_types = array();
		}

		?>
		<div class="field field-checkbox field-post-types">
			<ul class="field-input">
				<?php
				foreach ( $post_types as $key => $post_type ) {
					$post_type_object = get_post_type_object( $post_type );
					?>
					<li>
						<label>
							<input type="checkbox" name="mkdo_rcbr_post_types[]" value="<?php echo $key; ?>" <?php if ( in_array( $key, $mkdo_rcbr_post_types ) ) { echo ' checked="checked"'; } ?> />
							<?php _e( $post_type_object->labels->name );?>
						</label>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Call back for the hide from menus field
	 */
	public function mkdo_rcbr_hide_from_menus_cb() {

		$mkdo_rcbr_hide_from_menus = get_option( 'mkdo_rcbr_hide_from_menus', 'false' );

		?>

		<div class="field field-checkbox field-hide-from-menus">
			<ul class="field-input">
				<li>
					<label>
						<input type="checkbox" name="mkdo_rcbr_hide_from_menus" value="true" <?php if ( 'true' === $mkdo_rcbr_hide_from_menus ) { echo ' checked="checked"'; } ?> />
						<?php esc_html_e( 'Hide restricted public content from menus', MKDO_RCBR_TEXT_DOMAIN  ) ;?>
					</label>
				</li>
			</ul>
			<p class="description"><?php esc_html_e( 'If your menu is powered by a widget, this setting will not work.', MKDO_RCBR_TEXT_DOMAIN  ) ;?></p>
		</div>

		<?php
	}

	/**
	 * Call back for the admin post_type selector
	 */
	public function mkdo_rcbr_admin_post_types_select_cb() {

		$post_type_args = array(
			'public' => true,
		);
		$post_types           = get_post_types( $post_type_args );
		$mkdo_rcbr_post_types = get_option( 'mkdo_rcbr_admin_post_types', array( 'page' ) );

		unset( $post_types['attachment'] );

		if ( ! is_array( $mkdo_rcbr_post_types ) ) {
			$mkdo_rcbr_post_types = array();
		}

		?>
		<div class="field field-checkbox field-post-types">
			<ul class="field-input">
				<?php
				foreach ( $post_types as $key => $post_type ) {
					$post_type_object = get_post_type_object( $post_type );
					?>
					<li>
						<label>
							<input type="checkbox" name="mkdo_rcbr_admin_post_types[]" value="<?php echo $key; ?>" <?php if ( in_array( $key, $mkdo_rcbr_post_types ) ) { echo ' checked="checked"'; } ?> />
							<?php _e( $post_type_object->labels->name );?>
						</label>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Call back for the removed public roles selector
	 */
	public function mkdo_rcbr_removed_public_roles_select_cb() {

		global $wp_roles;

		$roles                          = $wp_roles->roles;
		$roles['public']                = array( 'name' => 'Public Access' );
		$mkdo_rcbr_removed_public_roles = get_option( 'mkdo_rcbr_removed_public_roles', array() );

		if ( ! is_array( $mkdo_rcbr_removed_public_roles ) ) {
			$mkdo_rcbr_removed_public_roles = array();
		}

		?>
		<div class="field field-checkbox field-removed-public-roles">
			<ul class="field-input">
				<?php
				foreach ( $roles as $key => $role ) {
					?>
					<li>
						<label>
							<input type="checkbox" name="mkdo_rcbr_removed_public_roles[]" value="<?php echo $key; ?>" <?php if ( in_array( $key, $mkdo_rcbr_removed_public_roles ) ) { echo ' checked="checked"'; } ?> />
							<?php _e( $role['name'] );?>
						</label>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Call back for the removed public roles selector
	 */
	public function mkdo_rcbr_removed_admin_roles_select_cb() {

		global $wp_roles;

		$roles                          = $wp_roles->roles;
		$mkdo_rcbr_removed_admin_roles = get_option( 'mkdo_rcbr_removed_public_roles', array( 'administrator') );

		if ( ! is_array( $mkdo_rcbr_removed_admin_roles ) ) {
			$mkdo_rcbr_removed_admin_roles = array();
		}

		?>
		<div class="field field-checkbox field-removed-admin-roles">
			<ul class="field-input">
				<?php
				foreach ( $roles as $key => $role ) {
					?>
					<li>
						<label>
							<input type="checkbox" name="mkdo_rcbr_removed_admin_roles[]" value="<?php echo $key; ?>" <?php if ( in_array( $key, $mkdo_rcbr_removed_admin_roles ) ) { echo ' checked="checked"'; } ?> />
							<?php _e( $role['name'] );?>
						</label>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Call back for the restrict message field
	 */
	public function mkdo_rcbr_prevent_restricted_child_select_cb() {

		$mkdo_rcbr_prevent_restricted_child = get_option( 'mkdo_rcbr_prevent_restricted_child', false );

		?>

		<div class="field field-checkbox field-restricted-child">
			<ul class="field-input">
				<li>
					<label>
						<input type="checkbox" name="mkdo_rcbr_prevent_restricted_child" value="1" <?php if ( $mkdo_rcbr_prevent_restricted_child ) { echo ' checked="checked"'; } ?> />
						<?php esc_html_e( 'Restrict child pages under restricted content', MKDO_RCBR_TEXT_DOMAIN  ) ;?>
					</label>
				</li>
			</ul>
		</div>

		<?php
	}

	/**
	 * Call back for the restrict message field
	 */
	public function mkdo_rcbr_default_restrict_message_db() {

		$mkdo_rcbr_default_restrict_message = get_option( 'mkdo_rcbr_default_restrict_message', esc_html__( 'Please login to access that area of the website.', MKDO_RCBR_TEXT_DOMAIN ) );

		?>

		<div class="field field-textarea field-restrict-message">
			<p class="field-title">
				<label for="mkdo_rcbr_default_restrict_message" class="screen-reader-text">
					<?php esc_html_e( 'Message', MKDO_RCBR_TEXT_DOMAIN );?>
				</label>
			</p>
			<p class="field-input">
				<textarea name="mkdo_rcbr_default_restrict_message" id="mkdo_rcbr_default_restrict_message"><?php echo $mkdo_rcbr_default_restrict_message;?></textarea>
			</p>
		</div>

		<?php
	}

	/**
	 * Call back for the redirect url
	 */
	public function mkdo_rcbr_default_redirect_cb() {

		$mkdo_rcbr_default_redirect = get_option( 'mkdo_rcbr_default_redirect' );
		?>

		<div class="field field-redirect-url">
			<p class="field-title">
				<label for="mkdo_rcbr_default_redirect" class="screen-reader-text">
					<?php esc_html_e( 'Redirect Url', MKDO_RCBR_TEXT_DOMAIN );?>
				</label>
			</p>
			<p class="field-description">
				<?php esc_html_e( 'Enter the full URL that you wish to redirect to. (Leave blank to redirect to login screen).', MKDO_RCBR_TEXT_DOMAIN );?>
			</p>
			<p class="field-input">
				<input type="text" name="mkdo_rcbr_default_redirect" id="mkdo_rcbr_default_redirect" placeholder="http://example.com/content/" value="<?php echo $mkdo_rcbr_default_redirect;?>" />
			</p>
		</div>

		<?php
	}

	/**
	 * Redirect to the original
	 */
	public function mkdo_rcbr_redirect_to_original_cb() {

		$mkdo_rcbr_redirect_to_original = get_option( 'mkdo_rcbr_redirect_to_original', false );

		?>

		<div class="field field-checkbox field-restricted-child">
			<p class="field-description">
				<?php esc_html_e( 'After login, redirect to the originally requested URL.', MKDO_RCBR_TEXT_DOMAIN );?>
			</p>
			<ul class="field-input">
				<li>
					<label>
						<input type="checkbox" name="mkdo_rcbr_redirect_to_original" value="1" <?php if ( $mkdo_rcbr_redirect_to_original ) { echo ' checked="checked"'; } ?> />
						<?php esc_html_e( 'Redirect to original page', MKDO_RCBR_TEXT_DOMAIN );?>
					</label>
				</li>
			</ul>
		</div>

		<?php
	}

	/**
	 * Reset permissions, permission list
	 */
	public function mkdo_rcbr_reset_page_permission_buttons_cb() {

		global $wp_roles;

		$nonce_key       = 'mkdo_rcbr_reset_roles_nonce';
		$roles           = $wp_roles->roles;
		$roles['public'] = array( 'name' => 'Public Access' );

		?>
		<div class="field field-checkbox field-reset-roles">
			<ul class="field-input">
				<?php
				foreach ( $roles as $key => $role ) {
					?>
					<li>
						<label>
							<input type="checkbox" name="mkdo_rcbr_reset_roles[]" value="<?php echo $key; ?>" />
							<?php _e( $role['name'] );?>
						</label>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
		<?php
		wp_nonce_field( basename(__FILE__), $nonce_key );
	}

	/**
	 * Add the options page
	 */
	public function add_options_page() {
		add_submenu_page( 'options-general.php', esc_html__( 'Restrict Content by Role', MKDO_RCBR_TEXT_DOMAIN ), esc_html__( 'Restrict Content by Role', MKDO_RCBR_TEXT_DOMAIN ), 'manage_options', 'restrict_content_by_role', array( $this, 'render_options_page' ) );
	}

	/**
	 * Render the options page
	 */
	public function render_options_page() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Restrict Content by Role', MKDO_RCBR_TEXT_DOMAIN );?></h2>
			<form action="options.php" method="POST">
	            <?php settings_fields( 'mkdo_rcbr_settings_group' ); ?>
	            <?php do_settings_sections( 'mkdo_rcbr_settings' ); ?>
	            <?php submit_button(); ?>
	        </form>
		</div>
	<?php
	}

	/**
	 * Add 'Settings' action on installed plugin list
	 */
	function add_setings_link( $links ) {
		array_unshift( $links, '<a href="options-general.php?page=restrict_content_by_role">' . esc_html__( 'Settings', MKDO_RCBR_TEXT_DOMAIN ) . '</a>');
		return $links;
	}

	/**
	 * Reset page permissions for selected roles
	 */
	public function reset_roles() {
		$nonce_key = 'mkdo_rcbr_reset_roles_nonce';

		if ( isset( $_POST['mkdo_rcbr_reset_roles'] ) ) {
			if ( ! empty( $_POST[ $nonce_key ] ) && wp_verify_nonce( $_POST[ $nonce_key ], basename( __FILE__ ) ) ) {
				$post_types = get_post_types( $post_type_args );
				unset( $post_types['attachment'] );

				$posts = get_posts(
					array(
						'post_type'      => $post_types,
						'posts_per_page' => -1,
						'meta_query'     => array(
							'relation' => 'OR',
							array(
								'key'     => '_mkdo_rcbr_roles',
								'compare' => 'EXISTS',
							),
							array(
								'key'     => '_mkdo_rcbr_admin_roles',
								'compare' => 'EXISTS',
							),
						),
					)
				);

				foreach ( $posts as $post ) {
					$public_roles = get_post_meta( $post->ID, '_mkdo_rcbr_roles', true );
					$admin_roles  = get_post_meta( $post->ID, '_mkdo_rcbr_admin_roles', true );

					if ( is_array( $public_roles ) ) {
						foreach ( $_POST['mkdo_rcbr_reset_roles'] as $role ) {
							$public_roles[] = $role;
						}
						update_post_meta( $post->ID, '_mkdo_rcbr_roles', $public_roles );
					}

					if ( is_array( $admin_roles ) ) {
						foreach ( $_POST['mkdo_rcbr_reset_roles'] as $role ) {
							$admin_roles[] = $role;
						}
						update_post_meta( $post->ID, '_mkdo_rcbr_admin_roles', $admin_roles );
					}
				}
			}
		}
	}
}
