# Restrict Content by Role

Restrict users with certain User Roles from accessing certain pieces of content and sub-content, both publicly and within the WordPress Dashboard (WP Admin).

## About

If you have a WordPress website with multiple users and several User Roles defined, and you wish to prevent certain User Roles from accessing certain pieces of content (and sub-content) both publicly and within `wp-admin`, then this plugin is for you.

The plugin provides the following functionality:

- A meta box to allow you to set user role permissions for content and its sub-content
- Choose if a restricted role can also view or edit that content in wp-admin
- An option to allow content, content and sub-content or just sub content to be restricted
- An option to allow a custom redirect URL (overriding default redirect URL settings)
- The ability to override parent content restrictions on sub-content (including the ability to make it public)
- An options page, with the following options:
 - Choose post types that the meta box should appear on
 - Choose the roles that will appear in the meta box to be restricted
 - Define a login screen error message (if no custom redirect URL is set)
 - Set a custom redirect URL

## Installation

1. Download this repository and unzip it into the folder `restrict-content-by-role`
2. Upload the `restrict-content-by-role` folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure the plugin via the 'Restrict Content by Role' options page under the WordPress 'Settings' Menu

## Changelog

**2.0.0** - *03.11.2015* - The plugin can now restrict access to content on within wp-admin.  
**2.0.0** - *03.11.2015* - You can now select which roles are shown in the custom meta box.  
**1.2.0** - *01.10.2015* - Updated for submission to WordPress plugin repository.  
**1.1.0** - *01.10.2015* - Reviewed and refactored code.  
**1.0.0** - *29.09.2015* - First stable release.  
