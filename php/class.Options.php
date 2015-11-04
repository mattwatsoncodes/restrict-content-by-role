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
		register_setting( 'mkdo_rcbr_settings_group', 'mkdo_rcbr_removed_admin_roles' );
		register_setting( 'mkdo_rcbr_settings_group', 'mkdo_rcbr_default_restrict_message' );
		register_setting( 'mkdo_rcbr_settings_group', 'mkdo_rcbr_default_redirect' );

		// Add sections
		add_settings_section( 'mkdo_rcbr_post_types_section', 'Choose Public Post Types', array( $this, 'mkdo_rcbr_post_types_section_cb' ), 'mkdo_rcbr_settings' );
		add_settings_section( 'mkdo_rcbr_admin_post_types_section', 'Choose Admin Post Types', array( $this, 'mkdo_rcbr_admin_post_types_section_cb' ), 'mkdo_rcbr_settings' );
		add_settings_section( 'mkdo_rcbr_removed_public_roles_section', 'Public Access Roles', array( $this, 'mkdo_rcbr_removed_public_roles_section_cb' ), 'mkdo_rcbr_settings' );
		add_settings_section( 'mkdo_rcbr_removed_admin_roles_section', 'Admin Access Roles', array( $this, 'mkdo_rcbr_removed_admin_roles_section_cb' ), 'mkdo_rcbr_settings' );
		add_settings_section( 'mkdo_rcbr_default_restrict_message_section', 'Restrict Message', array( $this, 'mkdo_rcbr_default_restrict_message_section_cb' ), 'mkdo_rcbr_settings' );

    	// Add fields to a section
		add_settings_field( 'mkdo_rcbr_post_types_select', 'Choose Public Post Types:', array( $this, 'mkdo_rcbr_post_types_select_cb' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_post_types_section' );
		add_settings_field( 'mkdo_rcbr_admin_post_types_select', 'Choose Admin Post Types:', array( $this, 'mkdo_rcbr_admin_post_types_select_cb' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_admin_post_types_section' );
		add_settings_field( 'mkdo_rcbr_removed_public_roles_select', 'Exclude Public Roles:', array( $this, 'mkdo_rcbr_removed_public_roles_select_cb' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_removed_public_roles_section' );
		add_settings_field( 'mkdo_rcbr_removed_admin_roles_select', 'Exclude Admin Roles:', array( $this, 'mkdo_rcbr_removed_admin_roles_select_cb' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_removed_admin_roles_section' );
		add_settings_field( 'mkdo_rcbr_default_restrict_message', 'Restriction Message:', array( $this, 'mkdo_rcbr_default_restrict_message_db' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_default_restrict_message_section' );
		add_settings_field( 'mkdo_rcbr_default_redirect', 'Redirect URL:', array( $this, 'mkdo_rcbr_default_redirect_cb' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_default_restrict_message_section' );
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
	 * Call back for the restrict message section
	 */
	public function mkdo_rcbr_default_restrict_message_section_cb() {
		echo '<p>';
		esc_html_e( 'Add the message that you wish to appear on the Login Page for restricted users. Or alternativly redirect the restricted user to a URL of your choice.', MKDO_RCBR_TEXT_DOMAIN  );
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
							<?php echo $post_type_object->labels->name;?>
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
							<?php echo $post_type_object->labels->name;?>
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
							<?php echo $role['name'];?>
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

		unset( $roles['administrator'] );

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
							<?php echo $role['name'];?>
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
}
