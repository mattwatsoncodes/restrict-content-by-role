<?php

/**
 * @link              https://github.com/mkdo/restrict-content-by-role
 * @package           mkdo\restrict_content_by_role
 *
 * Plugin Name:       Restrict Content by Role
 * Plugin URI:        https://github.com/mkdo/restrict-content-by-role
 * Description:       Restrict users with certain User Roles from accessing certain peices of content and sub-content within the WordPress Dashboard (WP Admin).
 * Version:           1.1.0
 * Author:            Make Do <hello@makedo.in>
 * Author URI:        http://www.makedo.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       restrict-content-by-role
 * Domain Path:       /languages
 */

// Constants
define( 'MKDO_RCBR_ROOT', __FILE__ );
define( 'MKDO_RCBR_TEXT_DOMAIN', 'restrict-content-by-role' );

// Load Classes
require_once "php/class.MainController.php";
require_once "php/class.Options.php";
require_once "php/class.AssetsController.php";
require_once "php/class.PermissionsMetaBox.php";
require_once "php/class.PermissionsColumn.php";
require_once "php/class.LoginErrors.php";
require_once "php/class.AccessController.php";

// Use Namespaces
use mkdo\restrict_content_by_role\MainController;
use mkdo\restrict_content_by_role\Options;
use mkdo\restrict_content_by_role\AssetsController;
use mkdo\restrict_content_by_role\PermissionsMetaBox;
use mkdo\restrict_content_by_role\PermissionsColumn;
use mkdo\restrict_content_by_role\LoginErrors;
use mkdo\restrict_content_by_role\AccessController;

// Initialize Classes
$options              = new Options();
$assets_controller    = new AssetsController();
$permissions_meta_box = new PermissionsMetaBox();
$permissions_column   = new PermissionsColumn();
$login_errors         = new LoginErrors();
$access_controller    = new AccessController( $login_errors );
$controller           = new MainController( $options, $assets_controller, $permissions_meta_box, $permissions_column, $access_controller );

// Run the Plugin
$controller->run();
