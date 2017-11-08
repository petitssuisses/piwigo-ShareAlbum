<?php
defined('SHAREALBUM_PATH') or die('Hacking attempt!');

/*
 * Button on album pages
 */
function sharealbum_add_button()
{
	global $template,$page,$user;

	$template->assign('SHAREALBUM_PATH', SHAREALBUM_PATH);
	
	switch($user['theme'])
	{
		case 'bootstrapdefault':
			$template->set_filename('sharealbum_button', realpath(SHAREALBUM_PATH.'template/sharealbum_button_bootstrapdefault.tpl'));
			break;
		case 'bootstrap_darkroom':
				$template->set_filename('sharealbum_button', realpath(SHAREALBUM_PATH.'template/sharealbum_button_bootstrap_darkroom.tpl'));
				break;
		default:
			$template->set_filename('sharealbum_button', realpath(SHAREALBUM_PATH.'template/sharealbum_button_default.tpl'));
			break;
	}
	
	$button = $template->parse('sharealbum_button', true);
	// Only add button on index pages and categories which are not public
	if ((script_basename()=='index') and (isset($page['category']['id'])))
	{
		if ( isset($page['category']['status']) and ($page['category']['status'] == 'private') and is_admin())
		{
			$template->add_index_button($button, BUTTONS_RANK_NEUTRAL);
		}
	}
}

/**
 * 
 */
function sharealbum_loc_end_page()
{
	global $tokens, $page, $conf, $template, $sharealbum_static_conf;

	$sharealbum_user = '';
	$sharealbum_pass = '';
	
	// Template used values
	//SHAREALBUM_CAT				Link target album category id
	//SHAREALBUM_LINK_IS_ACTIVE 	Link is active (1) or not (0)
	//SHAREALBUM_USER_MESSAGE		Popup message to user : 
	//								- link_created
	//								- link_renewed
	// 								- link_cancelled
	
	if (isset($page['section']) and $page['section'] == 'categories' and isset($page['category']))
	{	
		$template->assign('SHAREALBUM_LINK_IS_ACTIVE', 0);
		$template->assign('SHAREALBUM_CAT', $page['category']['id']);
		
		// Check message
		if (isset($_GET[SHAREALBUM_URL_MESSAGE])) {
			switch ($_GET[SHAREALBUM_URL_MESSAGE]) 
			{
				case SHAREALBUM_URL_MESSAGE_SHARED:
					$template->assign('SHAREALBUM_USER_MESSAGE','link_created');
					break;
				case SHAREALBUM_URL_MESSAGE_RENEWED:
					$template->assign('SHAREALBUM_USER_MESSAGE','link_renewed');
					break;
				case SHAREALBUM_URL_MESSAGE_CANCELLED:
					$template->assign('SHAREALBUM_USER_MESSAGE','link_cancelled');
					break;
			}
		}
		
		// Check if category is already shared using ShareAlbum plugin
		$result = pwg_query("
				SELECT `id`,`code`
				FROM `".SHAREALBUM_TABLE."`
				WHERE
					`cat` = ".$page['category']['id']
		);
		if (pwg_db_num_rows($result)) 
		{
			// Existing code found for this album
			$row = pwg_db_fetch_assoc($result);
			$template->assign('SHAREALBUM_LINK_IS_ACTIVE', 1);
			$template->assign('SHAREALBUM_CODE', get_absolute_root_url()."?".SHAREALBUM_URL_AUTH."=".$row['code']);
			$template->assign('SHAREALBUM_LINK_CANCEL',get_absolute_root_url()."?".SHAREALBUM_URL_ACTION."=".SHAREALBUM_URL_ACTION_CANCEL."&".SHAREALBUM_URL_CATEGORY."=".$page['category']['id']);
			$template->assign('SHAREALBUM_LINK_RENEW',get_absolute_root_url()."?".SHAREALBUM_URL_ACTION."=".SHAREALBUM_URL_ACTION_RENEW."&".SHAREALBUM_URL_CATEGORY."=".$page['category']['id']);
				
		} else {
			// No shareing detected
			$template->assign('SHAREALBUM_LINK_CREATE',get_absolute_root_url()."?".SHAREALBUM_URL_ACTION."=".SHAREALBUM_URL_ACTION_CREATE."&".SHAREALBUM_URL_CATEGORY."=".$page['category']['id']);
		}
	}
}
