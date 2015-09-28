<?php

namespace mkdo\restrict_content_by_role;

/**
 * Class MainController
 * @package mkdo\restrict_content_by_role
 */
class MainController {

	private $plugin_path;
	private $text_domain;
	private $assets_controller;
	private $permissions_meta_box;
	private $permissions_column;

	function __construct( $plugin_path, AssetsController $assets_controller, PermissionsMetaBox $permissions_meta_box, PermissionsColumn $permissions_column ) {
		$this->plugin_path          = $plugin_path;
		$this->text_domain          = 'restrict-content-by-role';
		$this->assets_controller    = $assets_controller;
		$this->permissions_meta_box = $permissions_meta_box;
		$this->permissions_column   = $permissions_column;
	}

	public function run() {
		load_plugin_textdomain( $this->text_domain, false, $this->plugin_path . '\languages' );

		$this->assets_controller->run();
		$this->permissions_meta_box->run();
		$this->permissions_column->run();
	}
}
