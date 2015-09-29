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

	private $text_domain;

	public function __construct() {
		$this->text_domain = 'restrict-content-by-role';
	}

	public function init_options_page() {

		// Register Settings
		register_setting( 'mkdo_rcbr_settings_group', 'mkdo_rcbr_post_types' );
		register_setting( 'mkdo_rcbr_settings_group', 'mkdo_rcbr_default_restrict_message' );

		// Add sections
		add_settings_section( 'mkdo_rcbr_post_types_section', 'Choose Post Types', array( $this, 'mkdo_rcbr_post_types_section_cb' ), 'mkdo_rcbr_settings' );
		add_settings_section( 'mkdo_rcbr_default_restrict_message_section', 'Restrict Message', array( $this, 'mkdo_rcbr_default_restrict_message_section_cb' ), 'mkdo_rcbr_settings' );

    	// Add fields to a section
		add_settings_field( 'mkdo_rcbr_post_types_select', 'Choose Post Types:', array( $this, 'mkdo_rcbr_post_types_select_cb' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_post_types_section' );
		add_settings_field( 'mkdo_rcbr_default_restrict_message', 'Enter Restriction Message:', array( $this, 'mkdo_rcbr_default_restrict_message_db' ), 'mkdo_rcbr_settings', 'mkdo_rcbr_default_restrict_message_section' );
	}

	/**
	 * Add the options page
	 */
	public function add_options_page() {
		add_submenu_page( 'options-general.php', esc_html__( 'Restrict Content by Role', $this->text_domain ), esc_html__( 'Restrict Content by Role', $this->text_domain ), 'manage_options', 'restrict_content_by_role', array( $this, 'render_options_page' ) );
	}

	/**
	 * Render the options page
	 */
	public function render_options_page() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Restrict Content by Role', $this->text_domain );?></h2>
			<form action="options.php" method="POST">
	            <?php settings_fields( 'mkdo_rcbr_settings_group' ); ?>
	            <?php do_settings_sections( 'mkdo_rcbr_settings' ); ?>
	            <?php submit_button(); ?>
	        </form>
		</div>
	<?php
	}

	/**
	 * Call back for the post_type section
	 */
	public function mkdo_rcbr_post_types_section_cb() {
		echo '<p>';
		esc_html_e( 'Select the Post Types that you wish to activate this plugin on.', $this->text_domain  );
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

		if( ! is_array( $mkdo_rcbr_post_types ) ) {
			$mkdo_rcbr_post_types = array();
		}

		?>

		<ul>
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
		<?php
	}

	/**
	 * Call back for the restrict message section
	 */
	public function mkdo_rcbr_default_restrict_message_section_cb() {
		echo '<p>';
		esc_html_e( 'Add the message that you wish to appear on the Login Page for restricted users', $this->text_domain  );
		echo '</p>';
	}

	/**
	 * Call back for the restrict message field
	 */
	public function mkdo_rcbr_default_restrict_message_db() {

		$mkdo_rcbr_default_restrict_message = get_option( 'mkdo_rcbr_default_restrict_message', __( 'Sorry, you do not have permission to access that area of the website.', $this->text_domain ) );

		?>

		<p>
			<label for="mkdo_rcbr_default_restrict_message">
				<?php esc_html_e( 'Message', $this->text_domain );?>
			</label>
		</p>
		<p>
			<textarea name="mkdo_rcbr_default_restrict_message" id="mkdo_rcbr_default_restrict_message" style="width:100%;" rows="4"><?php echo $mkdo_rcbr_default_restrict_message;?></textarea>
		</p>

		<?php
	}

	public function run() {
		add_action( 'admin_init', array( $this, 'init_options_page' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
	}
}
