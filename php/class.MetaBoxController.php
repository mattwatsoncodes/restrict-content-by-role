<?php

namespace mkdo\restrict_content_by_role;

/**
 * Class MetaBoxController
 *
 * A control to load metaboxes
 *
 * @package mkdo\restrict_content_by_role
 */
class MetaBoxController {

	private $admin_permissions_meta_box;
	private $permissions_meta_box;

	/**
	 * Constructor
	 */
	public function __construct( AdminPermissionsMetaBox $admin_permissions_meta_box, PermissionsMetaBox $permissions_meta_box ) {

		$this->admin_permissions_meta_box = $admin_permissions_meta_box;
		$this->permissions_meta_box = $permissions_meta_box;

	}

	/**
	 * Do Work
	 */
	public function run() {
		$this->permissions_meta_box->run();
		$this->admin_permissions_meta_box->run();
	}

}
