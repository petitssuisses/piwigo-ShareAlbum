Share Album is a piwigo plugin to enable simple per album share function.
For any private album, you can generate and share a unique URL that you can share with the users you trust in enough to browse an album.
# Usage
Use the new Share function available on private albums (for administrator users only).
From this function, you can : 
* Create a new share
* Copy share URL to clipboard
* Regenerate the unique URL
* Cancel an active share

Administrative functions : 
* Optionnaly remove breadcrumbs on shared albums
* Optionnaly hide menus on shared albums (user name not shown, no albums navigation but current album)

# Versions history
* Version 1.2.2
  * Fixed #7 Interaction with CSS in LocalFiles Editor
  * Fixed #23 Translation missing for french : Connection log for selected album
* Version 1.2.1
  * Fixed #22 Fatal error: Class 'Share_Album_maintain' not found
* Version 1.2
  * Implemented #19 List visits / ip of visitors of a shared album when clicking on the number of visits 
  * Implemented #14 Add option to "remember me" users logged in via shared link
  * Fixed #18 Click on Connect within identification menu should trigger warning
* Version 1.1 
  * Fixed #17 Warning: [mysql error 1146] Table 'xxx.piwigo_sharealbum' doesn't exist
* Version 1.0 
  * Implemented #15 Add number of visits and last visit using the shared link enhancement 
  * Implemented #13 Provide a way to logout guest user when browsing in shared link mode enhancement
  * Implemented #2 Administration / List of the currently shared albums
  * Fixed #12 When browsing in guest mode, access to identification.php produces undifined index category errors
  * Fixed #10 Prevent codes smaller or longer than expected
  * Fixed #9 Share button shows on categories (with subcategories) page
* Version 0.5 Beta rele∏ase
  * Implemented Issue #8 (bigs38) Return to the album view enhancement : Album name now links to the category page
* Version 0.4 Early release
  * Compatible with the Piwigo 2.9
  * Implemented Issue #5 Option - Remove breadcrumbs and display only album name
  * Implemented Issue #6 Option - Hide menus albums accessed though unique URL
  * Fixed translations
* Version 0.3 Early release, please consider as a beta version and do not use it for production
  * Implemented #1 Event handler on user deletion #1 https://github.com/petitssuisses/piwigo-ShareAlbum/issues/1
* Version 0.2 Early release, please consider as a beta version and do not use it for production. Fixed package
* Version 0.1 Early release, please consider as a beta version and do not use it for production
  Compatible with the Piwigo 2.9
  Tested with the following themes : Bootstrap Darkroom, Bootstrap default, clear, dark, elegant
  Not tested with a lot of other plugins activated, especially those interacting with URL arguments
			  
# How does the plugin work
From each private album, there's a new "Share" sub menu displayed on the category (album) page.
From this menu, you can share this album.
One shared, you are provided with a unique http(s) link giving access to this album.

Behind the scene :
* A unique Piwigo user is created with a random password assigned. By default, user type is set to 'generic' 
* This user is granted to browse the selected (and only) category album. When logging with the specific link, the user is logged in and redirected

Requirements to share an album : 
- The share function can only be managed by Administrator users
- The share function is only available for private albums

# Todo list / Upcoming features
See issues list on GitHub : https://github.com/petitssuisses/piwigo-ShareAlbum/issues

# Author 
Arnaud (bonhommedeneige) http://piwigo.org/forum/profile.php?id=19052