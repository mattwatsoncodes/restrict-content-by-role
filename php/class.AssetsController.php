<?php
namespace mkdo\restrict_content_by_role;
/**
 * Class AssetsController
 *
 * Sets up the JS and CSS needed for this plugin
 *
 * @package mkdo\restrict_content_by_role
 */
class AssetsController {

	/**
	 * Constructor
	 */
	function __construct() {
	}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Enqeue Scripts
	 */
	public function admin_enqueue_scripts() {

		$plugin_css_url = plugins_url( 'css/plugin.css', MKDO_RCBR_ROOT );
		$plugin_js_url  = plugins_url( 'js/plugin.js', MKDO_RCBR_ROOT );

		wp_enqueue_style( MKDO_RCBR_TEXT_DOMAIN, $plugin_css_url );
		wp_enqueue_script( MKDO_RCBR_TEXT_DOMAIN, $plugin_js_url, array('jquery'), '1.0.0', true );
	}
}
