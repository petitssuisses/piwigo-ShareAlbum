Share Album is a piwigo plugin to enable simple per album share function.
For any private album, you can generate and share a unique URL that you can share with the users you trust in enough to browse an album.
# Usage
Use the new Share function available on private albums (for administrator users only).
From this function, you can : 
* Create a new share
* Copy share URL to clipboad
* Regenerate the unique URL
* Cancel an active share

# Versions history
* Version 0.4 Early release
			  Compatible with the Piwigo 2.9
			  Implemented Issue #5 Option - Remove breadcrumbs and display only album name
			  Implemented Issue #6 Option - Hide menus albums accessed though unique URL
			  Fixed translations
* Version 0.3 Early release, please consider as a beta version and do not use it for production
			  Implemented #1 Event handler on user deletion #1 https://github.com/petitssuisses/piwigo-ShareAlbum/issues/1
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