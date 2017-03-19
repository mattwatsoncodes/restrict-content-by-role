<?php

/**
 * @link              https://github.com/mkdo/restrict-content-by-role
 * @package           mkdo\restrict_content_by_role
 *
 * Plugin Name:       Restrict Content by Role
 * Plugin URI:        https://github.com/mkdo/restrict-content-by-role
 * Description:       Restrict users with certain User Roles from accessing certain pieces of content and sub-content, both publicly and within the WordPress Dashboard (WP Admin).
 * Version:           3.5.6
 * Author:            Make Do <hello@makedo.net>
 * Author URI:        http://www.makedo.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       restrict-content-by-role
 * Domain Path:       /languages
 */

// Constants
define( 'MKDO_RCBR_VERSION', '3.5.6' );
define( 'MKDO_RCBR_ROOT', __FILE__ );
define( 'MKDO_RCBR_TEXT_DOMAIN', 'restrict-content-by-role' );

// Load Classes
require_once "php/class.MainController.php";
require_once "php/class.Options.php";
require_once "php/class.AssetsController.php";
require_once "php/class.AdminPermissionsMetaBox.php";
require_once "php/class.PermissionsMetaBox.php";
require_once "php/class.PermissionsColumn.php";
require_once "php/class.LoginErrors.php";
require_once "php/class.AccessController.php";
require_once "php/class.MetaBoxController.php";
require_once "php/class.AdminAccess.php";
require_once "php/class.PublicAccess.php";
require_once "php/class.Upgrade.php";
require_once "php/class.MenuAccess.php";

// Use Namespaces
use mkdo\restrict_content_by_role\MainController;
use mkdo\restrict_content_by_role\Options;
use mkdo\restrict_content_by_role\AssetsController;
use mkdo\restrict_content_by_role\AdminPermissionsMetaBox;
use mkdo\restrict_content_by_role\PermissionsMetaBox;
use mkdo\restrict_content_by_role\PermissionsColumn;
use mkdo\restrict_content_by_role\LoginErrors;
use mkdo\restrict_content_by_role\AccessController;
use mkdo\restrict_content_by_role\MetaBoxController;
use mkdo\restrict_content_by_role\AdminAccess;
use mkdo\restrict_content_by_role\PublicAccess;
use mkdo\restrict_content_by_role\Upgrade;
use mkdo\restrict_content_by_role\MenuAccess;

// Initialize Classes
$options                    = new Options();
$assets_controller          = new AssetsController();
$admin_permissions_meta_box = new AdminPermissionsMetaBox();
$permissions_meta_box       = new PermissionsMetaBox();
$metabox_controller         = new MetaBoxController( $admin_permissions_meta_box, $permissions_meta_box );
$permissions_column         = new PermissionsColumn();
$login_errors               = new LoginErrors();
$admin_access               = new AdminAccess();
$public_access              = new PublicAccess( $login_errors );
$access_controller          = new AccessController( $admin_access, $public_access );
$upgrade                    = new Upgrade();
$menu_access                = new MenuAccess();
$controller                 = new MainController( $options, $assets_controller, $metabox_controller, $permissions_column, $access_controller, $upgrade, $menu_access );

// Run the Plugin
$controller->run();
