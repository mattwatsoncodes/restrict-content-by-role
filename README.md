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
 - Option to hide restricted pages from menu (only works when using `wp_nav_menu()`)
 - Choose the roles that will appear in the meta box to be restricted
 - Define a login screen error message (if no custom redirect URL is set)
 - Set a custom redirect URL
 - Choose wether or not to redirect back to the original URL

## Installation

1. Download this repository and unzip it into the folder `restrict-content-by-role`
2. Upload the `restrict-content-by-role` folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure the plugin via the 'Restrict Content by Role' options page under the WordPress 'Settings' Menu

## Changelog

**1.0.0** - *29.09.2015* - First stable release.  
**1.1.0** - *01.10.2015* - Reviewed and refactored code.  
**1.2.0** - *01.10.2015* - Updated for submission to WordPress plugin repository.  
**2.0.0** - *03.11.2015* - You can now select which roles are shown in the custom meta box.  
**2.0.0** - *03.11.2015* - The plugin can now restrict access to content on within wp-admin.  
**2.1.0** - *12.11.2015* - Now works with the CMS Tree Page View plugin.  
**3.0.0** - *12.11.2015* - Switched the way the meta box checkboxes work, so that they are more intuitive.  
**3.0.1** - *14.01.2016* - Fixed bug where new pages would be locked out.  
**3.1.0** - *14.01.2016* - New option to prevent users adding child pages to restricted pages.  
**3.1.1** - *03.02.2016* - Bug fix - sometimes new pages were automatically locked.  
**3.1.2** - *03.02.2016* - Bug fix - better fix for auto-locking pages.  
**3.2.0** - *22.02.2016* - Added checkbox to allow redirects back to the originally requested URL.  
**3.3.0** - *09.04.2016* - Fixed an issue that prevented Tree Page View from working with the plugin  
**3.3.0** - *09.04.2016* - Added 'Public Access' role, to restrict pages to logged in users  
**3.3.1** - *08.08.2016* - Tested with WordPress 4.5, updated translations  
**3.4.0** - *08.08.2016* - Option to hide restricted pages from menu (only works when using `wp_nav_menu()`)  
**3.4.1** - *08.08.2016* - Added missing Menu Access file  
**3.4.2** - *09.08.2016* - Allow admins to see backend menu items, even if they are restricted on the front end  
**3.5.0** - *22.11.2016* - Added ability to reset permissions for each role  
**3.5.0** - *22.11.2016* - Fixed menu when not hiding  
**3.5.0** - *22.11.2016* - Appended query string to redirect  
