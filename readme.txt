=== Restrict Content by Role ===
Contributors: mkdo, mwtsn
Donate link:
Tags: restrict, restrict content, lockdown, lockdown content, pages, lockdown pages, management, manage pages, manage user roles, manage users, manage roles, user, roles, permissions, manage page permissions, manage content permissions, manage permissions, manage sub page permissions
Requires at least: 4.3
Tested up to: 4.3
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Restrict users with certain User Roles from accessing certain pieces of content and sub-content, both publicly and within the WordPress Dashboard (WP Admin).

== Description ==

If you have a WordPress website with multiple users and several User Roles defined, and you wish to prevent certain User Roles from accessing certain pieces of content (and sub-content) both publicly and within `wp-admin`, then this plugin is for you.

The plugin provides the following functionality:

* A meta box to allow you to set user role permissions for content and its sub-content
* Choose if a restricted role can also view or edit that content in wp-admin
* An option to allow content, content and sub-content or just sub content to be restricted
* An option to allow a custom redirect URL (overriding default redirect URL settings)
* The ability to override parent content restrictions on sub-content (including the ability to make it public)
* An options page, with the following options:
 * Choose post types that the meta box should appear on
 * Choose the roles that will appear in the meta box to be restricted
 * Define a login screen error message (if no custom redirect URL is set)
 * Set a custom redirect URL

If you are using this plugin in your project [we would love to hear about it](mailto:hello@makedo.in).

== Installation ==

1. Backup your WordPress install
2. Upload the plugin folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure the plugin via the 'Restrict Content by Role' options page under the WordPress 'Settings' Menu

== Screenshots ==

1. Meta Box to set permissions on hierarchical content
2. Meta Box on sub-content, showing override options
3. Meta Box on sub-content, overriding parent permissions
4. Options page (more options available)
5. Meta box to set admin permissions on hierarchical content
6. After admin permission set, an editor can no longer see the page in the admin panel

== Changelog ==

= 2.0.0 =
* The plugin can now restrict access to content on within wp-admin
* You can now select which roles are shown in the custom meta box

= 1.2.0 =
* Updated for submission to WordPress plugin repository

= 1.1.0 =
* Reviewed and refactored code

= 1.0.0 =
* First stable release
