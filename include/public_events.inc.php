<?php
defined('SHAREALBUM_PATH') or die('Hacking attempt!');

/*
 * Button on album pages
 */
function sharealbum_add_button()
{
	global $template,$page,$user;

	$template->assign('SHAREALBUM_PATH', SHAREALBUM_PATH);
	$template->assign(
		array(
			'T_SHAREALBUM_ALBUM_SHARE' => l10n('Share'),
			'T_SHAREALBUM_ALBUM_SHARED' => l10n('This album is shared via a public link'),
			'T_SHAREALBUM_COPY_TO_CLIPBOARD' => l10n('Copy to clipboard'),
			'T_SHAREALBUM_LINK_COPIED_SUCCESS' => l10n('Link was successfully copied to clipboard. You can now use system paste functionnality to share it !'),
			'T_SHAREALBUM_LINK_COPIED_FAILURE' => l10n('lease select the link and use the Edit > Copy function from your browser.'),
			'T_SHAREALBUM_RENEW_WARNING' => l10n('You are going to renew the shared link for this album. Previously communicated link will no more be active. Do you confirm ?'),
			'T_SHAREALBUM_RENEW' => l10n('Renew link'),
			'T_SHAREALBUM_CANCEL' => l10n('Cancel sharing'),
			'T_SHAREALBUM_CANCEL_WARNING' => l10n('Are you sure you wish to cancel this album sharing ?'),
			'T_SHAREALBUM_SHARE' => l10n('Share this album'),
			'T_SHAREALBUM_LINK_CREATED' => l10n('Share link was successfully created. Click the share button to display it.'),
			'T_SHAREALBUM_LINK_RENEWED' => l10n('Link was successfully renewed. Click the share button to display it.'),
			'T_SHAREALBUM_LINK_CANCELLED' => l10n('Link was successfully deleted. Album is no longer publicly shared.')
		)
	);

	switch($user['theme'])
	{
		case 'bootstrapdefault':
			$template->set_filename('sharealbum_button', realpath(SHAREALBUM_PATH.'template/sharealbum_button_bootstrapdefault.tpl'));
			break;
		case 'bootstrap_darkroom':
				$template->set_filename('sharealbum_button', realpath(SHAREALBUM_PATH.'template/sharealbum_button_bootstrap_darkroom.tpl'));
				break;
		case 'modus':
				$template->set_filename('sharealbum_button', realpath(SHAREALBUM_PATH.'template/sharealbum_button_modus.tpl'));
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
			$template->assign('SHAREALBUM_LINK_CANCEL',get_root_url()."?".SHAREALBUM_URL_ACTION."=".SHAREALBUM_URL_ACTION_CANCEL."&".SHAREALBUM_URL_CATEGORY."=".$page['category']['id']);
			$template->assign('SHAREALBUM_LINK_RENEW',get_root_url()."?".SHAREALBUM_URL_ACTION."=".SHAREALBUM_URL_ACTION_RENEW."&".SHAREALBUM_URL_CATEGORY."=".$page['category']['id']);
				
		} else {
			// No sharing detected
			
			// Check if album can be shared (is private and contains at least 1 picture) - Implemented #56
			$result_chk = pwg_query("SELECT COUNT(ic.image_id) as nb
				FROM ".CATEGORIES_TABLE." c, ".IMAGE_CATEGORY_TABLE." ic 
				WHERE c.id = ".$page['category']['id']." 
				AND c.status = 'private'
				AND c.id = ic.category_id");
			if (pwg_db_num_rows($result_chk)) 
			{
				$row_chk = pwg_db_fetch_assoc($result_chk);
				if ($row_chk['nb'] > 0) {
					$template->assign('SHAREALBUM_LINK_CREATE',get_root_url()."?".SHAREALBUM_URL_ACTION."=".SHAREALBUM_URL_ACTION_CREATE."&".SHAREALBUM_URL_CATEGORY."=".$page['category']['id']);
				}
			}
		}
	}
}
