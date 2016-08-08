<?php

namespace mkdo\restrict_content_by_role;

/**
 * Class MainController
 *
 * The main loader for this plugin
 *
 * @package mkdo\restrict_content_by_role
 */
class MainController {

	private $options;
	private $assets_controller;
	private $metabox_controller;
	private $permissions_column;
	private $access_controller;
	private $upgrade;
	private $menu_access;

	/**
	 * Constructor
	 *
	 * @param Options            $options              Object defining the options page
	 * @param AssetsController   $assets_controller    Object to load the assets
	 * @param PermissionsMetaBox $permissions_meta_box Object defining the permissions meta box
	 * @param PermissionsColumn  $permissions_column   Object defining the permissions column
	 * @param AccessController   $access_controller    Object to control content access
	 */
	public function __construct( Options $options, AssetsController $assets_controller, MetaBoxController $metabox_controller, PermissionsColumn $permissions_column, AccessController $access_controller, Upgrade $upgrade, MenuAccess $menu_access ) {
        $this->options            = $options;
        $this->assets_controller  = $assets_controller;
        $this->metabox_controller = $metabox_controller;
        $this->permissions_column = $permissions_column;
        $this->access_controller  = $access_controller;
        $this->upgrade            = $upgrade;
		$this->menu_access            = $menu_access;
	}

	/**
	 * Do Work
	 */
	public function run() {
		load_plugin_textdomain( MKDO_RCBR_TEXT_DOMAIN, false, MKDO_RCBR_ROOT . '\languages' );

		$this->upgrade->run();
		$this->options->run();
		$this->assets_controller->run();
		$this->metabox_controller->run();
		$this->permissions_column->run();
		$this->access_controller->run();
		$this->menu_access->run();
	}
}
