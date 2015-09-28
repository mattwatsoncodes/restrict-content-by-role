<?php

/**
 * @link              https://github.com/mkdo/restrict-content-by-role
 * @package           mkdo\restrict_content_by_role
 *
 * @wordpress-plugin
 * Plugin Name:       Restrict Content by Role
 * Plugin URI:        https://github.com/mkdo/restrict-content-by-role
 * Description:       Restrict users with certain User Roles from accessing certain peices of content and sub-content within the WordPress Dashboard (WP Admin).
 * Version:           1.0.0
 * Author:            Make Do <hello@makedo.in>
 * Author URI:        http://www.makedo.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       restrict-content-by-role
 * Domain Path:       /languages
 */

// Load Classes

require_once "php/class.MainController.php";
require_once "php/class.AssetsController.php";
require_once "php/class.PermissionsMetaBox.php";
require_once "php/class.PermissionsColumn.php";

// Define Namespaces
use mkdo\restrict_content_by_role\MainController;
use mkdo\restrict_content_by_role\AssetsController;
use mkdo\restrict_content_by_role\PermissionsMetaBox;
use mkdo\restrict_content_by_role\PermissionsColumn;

// Initialize Controllers
$assets_controller    = new AssetsController( __FILE__ );
$permissions_meta_box = new PermissionsMetaBox();
$permissions_column   = new PermissionsColumn();
$controller           = new MainController( __FILE__, $assets_controller, $permissions_meta_box, $permissions_column );

// Run the Plugin
$controller->run();
