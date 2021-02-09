<?php
/*
Plugin Name: ShareAlbum
Version: 11.1
Description: Plugin enabling a simple share feature for albums
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=865
Author: petitssuisses
Author URI: http://piwigo.org/forum/profile.php?id=19052
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

if (basename(dirname(__FILE__)) != 'ShareAlbum')
{
  add_event_handler('init', 'sharealbum_error');
  function sharealbum_error()
  {
    global $page;
    $page['errors'][] = 'Share Album plugin folder name is incorrect, uninstall the plugin and rename it to "ShareAlbum"';
  }
  return;
}

// +-----------------------------------------------------------------------+
// | Define plugin constants                                               |
// +-----------------------------------------------------------------------+
global $prefixeTable;

define('SHAREALBUM_ID',      basename(dirname(__FILE__)));
define('SHAREALBUM_PATH' ,   PHPWG_PLUGINS_PATH . SHAREALBUM_ID . '/');
define('SHAREALBUM_TABLE',   $prefixeTable . 'sharealbum');
define('SHAREALBUM_TABLE_LOG',   $prefixeTable . 'sharealbum_log');
define('SHAREALBUM_ADMIN',   get_root_url() . 'admin.php?page=plugin-' . SHAREALBUM_ID);
define('SHAREALBUM_PUBLIC',  get_absolute_root_url() . make_index_url(array('section' => 'sharealbum')) . '/');
define('SHAREALBUM_DIR',     PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'Share_Album/');

define('SHAREALBUM_URL_AUTH', 'xauth'); 				// Shared album code identifier, used to trigger auto login feature for the album using this sharing key
define('SHAREALBUM_KEY_LENGTH', 12);					// Length of the authentication key

define('SHAREALBUM_URL_ACTION','xact');					// URL attribute for actions handling
define('SHAREALBUM_URL_ACTION_CREATE','activate');		// Action value for creating a new album share
define('SHAREALBUM_URL_ACTION_CANCEL','cancel');		// Action value for cancelling an album share
define('SHAREALBUM_URL_ACTION_RENEW','renew');			// Action value for renewing an album sharing key
define('SHAREALBUM_URL_ACTION_LOGIN','login');			// Action value for login (logout from guest)
define('SHAREALBUM_URL_CATEGORY','cat');				// URL attribute for categories handling
define('SHAREALBUM_URL_MESSAGE','msg');					// URL attribute for passing feedback messages to user interface (such as 'creation successfull, renew done, ...')
define('SHAREALBUM_URL_MESSAGE_SHARED','shared');		// Pass message to user that album was shared
define('SHAREALBUM_URL_MESSAGE_CANCELLED','deleted');	// Pass message to user that album was cancelled
define('SHAREALBUM_URL_MESSAGE_RENEWED','renewed');		// Pass message to user that album link was renewed

define('SHAREALBUM_USER_PREFIX','share_');				// Prefix to prepend to a guest user
define('SHAREALBUM_USER_CODE_SUFFIX_LENGTH',8);			// Length of the random suffix to apply to auto created users
define('SHAREALBUM_USER_PASSWORD_LENGTH',16);			// Length of the random password for auto created users

define('SHAREALBUM_SESSION_VAR','sharealbum_guest');	// Session variable, used to identify user is browsing as a (URL identified) guest
define('SHAREALBUM_GROUP','sharealbum');			// Group name of the guest users

// load functions
include_once(SHAREALBUM_PATH.'include/sharealbum_functions.inc.php');

// +-----------------------------------------------------------------------+
// | Add event handlers                                                    |
// +-----------------------------------------------------------------------+
// init the plugin
add_event_handler('init', 'sharealbum_init');

// catch users deletion events
add_event_handler('delete_user', 'sharealbum_on_delete_user');
// register new identification block
add_event_handler('blockmanager_register_blocks', 'sharealbum_identification_menu_register');
// hide menus for users using a sharealbum link
//add_event_handler('blockmanager_apply', 'sharealbum_manage_menus');
add_event_handler('blockmanager_prepare_display', 'sharealbum_manage_menus');
add_event_handler('loc_end_index', 'sharealbum_replace_breadcrumb');
add_event_handler('loc_end_picture','sharealbum_replace_breadcrumb');

/*
 * this is the common way to define event functions: create a new function for each event you want to handle
 */
if (defined('IN_ADMIN'))
{
  // file containing all admin handlers functions
  $admin_file = SHAREALBUM_PATH . 'include/admin_events.inc.php';

  // admin plugins menu link
  add_event_handler('get_admin_plugin_menu_links', 'sharealbum_admin_plugin_menu_links',
   EVENT_HANDLER_PRIORITY_NEUTRAL, $admin_file);
}
else
{
  // file containing all public handlers functions
  $public_file = SHAREALBUM_PATH . 'include/public_events.inc.php';

  add_event_handler('init', 'sharealbum_init');

  // Add Share button on album pages
  add_event_handler('loc_end_index', 'sharealbum_add_button',
    EVENT_HANDLER_PRIORITY_NEUTRAL, $public_file);
  
  add_event_handler('loc_end_section_init', 'sharealbum_loc_end_page',
  EVENT_HANDLER_PRIORITY_NEUTRAL, $public_file);
}

/**
 * plugin initialization
 *   - check for upgrades
 *   - unserialize configuration
 *   - load language
 */
function sharealbum_init()
{
  global $conf;

  // load plugin language file
  load_language('plugin.lang', SHAREALBUM_PATH);

  // prepare plugin configuration
  $conf['sharealbum'] = safe_unserialize($conf['sharealbum']);
  
  // Shared mode detection
  if (isset($_GET[SHAREALBUM_URL_AUTH]))
  {
  	// First handle security check on code
  	if (strlen($_GET[SHAREALBUM_URL_AUTH]) != SHAREALBUM_KEY_LENGTH) {
  		die("Hacking attempt");	// TODO enhance, redirect to index page without diying
  	}
  	
  	if (!is_a_guest()) {
  		logout_user();
  		redirect(PHPWG_ROOT_PATH.'index.php?'.SHAREALBUM_URL_AUTH.'='.$_GET[SHAREALBUM_URL_AUTH]);
  	} else {
	  	$result = pwg_query("
					SELECT `cat`,`user_id`
					FROM `".SHAREALBUM_TABLE."`
					WHERE
						`code` = '".$_GET[SHAREALBUM_URL_AUTH]."'"
	  	);
	  	if (pwg_db_num_rows($result))
	  	{
	  		$row = pwg_db_fetch_assoc($result);
	  		$auto_login = false;
	  		if ($conf['sharealbum']['option_remember_me']) {
	  			$auto_login = true;
	  		}
			log_user($row['user_id'],$auto_login);
			// log visit
			pwg_query("INSERT INTO `".SHAREALBUM_TABLE_LOG."` (`cat_id`,`ip`,`visit_d`)
  					VALUES (".$row['cat'].", '".$_SERVER['REMOTE_ADDR']."', '".date("Y-m-d H:i:s")."')");
			pwg_set_session_var(SHAREALBUM_SESSION_VAR, true);
			redirect(PHPWG_ROOT_PATH.'index.php?/category/'.$row['cat']);
	  	}
  	}
  }
  
  // An administrative action is detected
  if (isset($_GET[SHAREALBUM_URL_ACTION])) 
  {
  	if ($_GET[SHAREALBUM_URL_ACTION] == SHAREALBUM_URL_ACTION_LOGIN) {
  		logout_user(); // First logout current user
  		redirect(PHPWG_ROOT_PATH.'identification.php');
  	} else if (isset($_GET[SHAREALBUM_URL_CATEGORY]))
  	{
  		$sharealbum_cat = $_GET[SHAREALBUM_URL_CATEGORY];
  		
  		//TODO : Check cat is a numeric and exists
  		switch ($_GET[SHAREALBUM_URL_ACTION])
  		{
  			case SHAREALBUM_URL_ACTION_CREATE:
  				sharealbum_create($sharealbum_cat);
				redirect(PHPWG_ROOT_PATH.'index.php?/category/'.$sharealbum_cat.'&'.SHAREALBUM_URL_MESSAGE.'='.SHAREALBUM_URL_MESSAGE_SHARED);
  				break;
  			case SHAREALBUM_URL_ACTION_CANCEL:
  				sharealbum_cancel_share($sharealbum_cat);
  				redirect(PHPWG_ROOT_PATH.'index.php?/category/'.$sharealbum_cat.'&'.SHAREALBUM_URL_MESSAGE.'='.SHAREALBUM_URL_MESSAGE_CANCELLED);
  				break;
  			case SHAREALBUM_URL_ACTION_RENEW:
  				sharealbum_renew_share($sharealbum_cat);
  				// TODO Do not die, return error
  				redirect(PHPWG_ROOT_PATH.'index.php?/category/'.$sharealbum_cat.'&'.SHAREALBUM_URL_MESSAGE.'='.SHAREALBUM_URL_MESSAGE_RENEWED);
  				break;
  		}
  	}
  }
}

/**
 * Cleans sharealbum table when a user is manually deleted by an administrator (through the interface)
 * @param unknown $user_id
 */
function sharealbum_on_delete_user($user_id) {
	$res = pwg_query("
  		DELETE
  		FROM `".SHAREALBUM_TABLE."`
  		WHERE `user_id`=".$user_id
	);
}

/**
 * Removes menus for visitors using the link (identified through the session) when option
 * option_hide_menus is turned on
 * @param unknown $menublock
 */
function sharealbum_manage_menus($menublock) {
	global $conf,$template,$user;
  
	if ((pwg_get_session_var(SHAREALBUM_SESSION_VAR) and ($conf['sharealbum']['option_hide_menus'])))
  	{
  		$blocks = $menublock[0]->get_registered_blocks();
  		foreach($blocks as $block) {
  			if ($block->get_id() != 'mbShareConnect') {
  				// Hide any block other than mbShareConnect if enabled
  				$menublock[0]->hide_block($block->get_id());
  			}
  		}
  	}
  	if ((pwg_get_session_var(SHAREALBUM_SESSION_VAR) and ($conf['sharealbum']['option_show_login_menu'])))
  	{
	  	if (($menublock[0]->get_block('mbShareConnect')) != null) {
	  			$template->assign(
	  				array(
	  					'sharealbum_login_link' => PHPWG_ROOT_PATH.'index.php?'.SHAREALBUM_URL_ACTION.'='.SHAREALBUM_URL_ACTION_LOGIN,
	  					'sharealbum_theme' => $user['theme'],
	  				)
	  			);
				$template->set_template_dir(SHAREALBUM_PATH.'template/');
				$menublock[0]->get_block('mbShareConnect')->template = 'menubar_ident.tpl';
		}
  	}
}

/**
 * Replaces the navigation breadcrumbs with the album name
 * Album name is linked to album home page
 */
function sharealbum_replace_breadcrumb() {
	global $conf,$template,$page;
	if ((pwg_get_session_var(SHAREALBUM_SESSION_VAR) and ($conf['sharealbum']['option_replace_breadcrumbs']) and isset($page['category'])))
	{
		$section = $page['title'];
		$section_title = substr($section,strrpos($section,'">',0)+2,strlen($section)-strrpos($section,'">',0));
		$section_title = substr($section_title,0,strrpos($section_title,'</a>',0));
		$breadcrumb = "<a href='".PHPWG_ROOT_PATH.'index.php?/category/'.$page['category']['id']."'>Accueil</a>";
		$breadcrumb = $breadcrumb."&nbsp;/&nbsp;<a href='".PHPWG_ROOT_PATH.'index.php?/category/'.$page['category']['id']."'>".$section_title."</a>";
		$template->assign('TITLE', $breadcrumb);
		$template->assign('SECTION_TITLE',  $breadcrumb." /&nbsp;");
	}
}

/**
 *
 * @param unknown $menu_ref_arr
 */
function sharealbum_identification_menu_register($menu_ref_arr )
{
	$menu = & $menu_ref_arr[0];
	if ($menu->get_id() != 'menubar')
		return;
	$menu->register_block( new RegisteredBlock( 'mbShareConnect', 'mbShareConnect', 'ShareAlbum'));
}
