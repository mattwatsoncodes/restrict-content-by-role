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

	private $text_domain;
	private $plugin_path = '';

	function __construct( $plugin_path ) {
		$this->plugin_path = $plugin_path;
		$this->text_domain = 'restrict-content-by-role';
	}

	public function admin_enqueue_scripts() {

		$plugin_css_url = plugins_url( 'css/plugin.css', $this->plugin_path );
		$plugin_js_url  = plugins_url( 'js/plugin.js', $this->plugin_path );

		wp_enqueue_style( $this->text_domain, $plugin_css_url );
		wp_enqueue_script( $this->text_domain, $plugin_js_url, array('jquery'), '1.0.0', true );
	}

	public function run() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

}
