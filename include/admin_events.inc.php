<?php
defined('SHAREALBUM_PATH') or die('Hacking attempt!');

/**
 * admin plugins menu link
 * 
 * Deprecated, replaced with the Has Setting label in main.inc.php 
 * Since Piwigo 11
 */
function sharealbum_admin_plugin_menu_links($menu)
{
	$menu[] = array(
			'NAME' => l10n('Share Album'),
			'URL' => SHAREALBUM_ADMIN,
	);
	return $menu;
}
