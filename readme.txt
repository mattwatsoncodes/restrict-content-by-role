=== Restrict Content by Role ===
Contributors: mkdo, mwtsn
Donate link:
Tags: restrict, lockdown, manage, content, user
Requires at least: 4.4
Tested up to: 4.5
Stable tag: 3.5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Restrict users with certain User Roles from accessing content and sub-content,
both publicly and the WordPress Dashboard (wp-admin).

== Description ==

If you have a WordPress website with multiple users and several User Roles defined,
and you wish to prevent certain User Roles from accessing certain pieces of content
(and sub-content) both publicly and within `wp-admin`, then this plugin is for you.

__NOTICE:__ We are actively developing 'Restrict content by...' which will eventually
replace this plugin with something that works better, and will do so much more.

The plugin provides the following functionality:

* A meta box to allow you to set user role permissions for content and its sub-content
* Choose if a restricted role can also view or edit that content in wp-admin
* An option to allow content, content and sub-content or just sub content to be restricted
* An option to allow a custom redirect URL (overriding default redirect URL settings)
* The ability to override parent content restrictions on sub-content (including the ability to make it public)
* Works with the CMS Tree Page View plugin
* An options page, with the following options:
 * Choose post types that the meta box should appear on
 * Option to hide restricted pages from menu (only works when using `wp_nav_menu()`)
 * Choose the roles that will appear in the meta box to be restricted
 * Define a login screen error message (if no custom redirect URL is set)
 * Set a custom redirect URL
 * Prevent users from adding child content to restricted content

If you are using this plugin in your project [we would love to hear about it](mailto:hello@makedo.net).

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

= 1.0.0 =
* First stable release

= 1.1.0 =
* Reviewed and refactored code

= 1.2.0 =
* Updated for submission to WordPress plugin repository

= 2.0.0 =
* The plugin can now restrict access to content on within wp-admin
* You can now select which roles are shown in the custom meta box

= 2.1.0 =
* Now works with the CMS Tree Page View plugin

= 3.0.0 =
* Switched the way the meta box checkboxes work, so that they are more intuitive.

= 3.0.1 =
* Fixed bug where new pages would be locked out.

= 3.1.0 =
* New option to prevent users adding child pages to restricted pages.

= 3.1.1 =
* Bug fix - sometimes new pages were automatically locked.

= 3.1.2 =
* Bug fix - better fix for auto-locking pages.

= 3.2.0 =
* Added checkbox to allow redirects back to the originally requested URL

= 3.3.0 =
* Added 'Public Access' role, to restrict pages to logged in users
* Fixed an issue that prevented Tree Page View from working with the plugin

= 3.3.1 =
* Tested with WordPress 4.5, updated translations

= 3.4.0 =
* Option to hide restricted pages from menu (only works when using `wp_nav_menu()`)

= 3.4.1 =
* Added missing Menu Access file

= 3.4.2 =
* Allow admins to see backend menu items, even if they are restricted on the front end

= 3.5.0 =
* Added ability to reset permissions for each role
* Fixed menu when not hiding
* Appended query string to redirect

= 3.5.1 =
* Added new artwork

= 3.5.2 =
* Bug fixes

= 3.5.3 - 3.5.6 =
* Repair botched deploy
