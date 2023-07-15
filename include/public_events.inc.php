<?php
defined('SHAREALBUM_PATH') or die('Hacking attempt!');

/*
 * Escape a literal ' apostrophe if it isn't already \' escaped (*after* PHP
 * interpreted it as literal string, so original 'foo\'bar' will be escaped but
 * 'foo\\\'bar' will not.). Such that JavaScript alert('foo\'bar') again can
 * interpret it. Yay for hell of interpreter languages.
 * @param l10n string
 * @return escaped string
 */
function sharealbum_escape_apostrophe($str)
{
	// Yes this looks weird, but.. PHP being an interpreter the [^\\\] sequence
	// becomes [^\] in the pattern literal.
	return preg_replace('/([^\\\])\'/', '$1\\\'', $str);
}

/*
 * Button on album pages
 */
function sharealbum_add_button()
{
	global $template,$page,$user,$conf;

	$template->assign('SHAREALBUM_PATH', SHAREALBUM_PATH);
	$template->assign(
		array(
			'T_SHAREALBUM_ALBUM_SHARE' => sharealbum_escape_apostrophe(l10n('Share')),
			'T_SHAREALBUM_ALBUM_SHARED' => sharealbum_escape_apostrophe(l10n('This album is shared via a public link')),
			'T_SHAREALBUM_COPY_TO_CLIPBOARD' => sharealbum_escape_apostrophe(l10n('Copy to clipboard')),
			'T_SHAREALBUM_LINK_COPIED_SUCCESS' => sharealbum_escape_apostrophe(l10n('Link was successfully copied to clipboard. You can now use system paste functionnality to share it !')),
			'T_SHAREALBUM_LINK_COPIED_FAILURE' => sharealbum_escape_apostrophe(l10n('Please select the link and use the Edit > Copy function from your browser.')),
			'T_SHAREALBUM_RENEW_WARNING' => sharealbum_escape_apostrophe(l10n('You are going to renew the shared link for this album. Previously communicated link will no more be active. Do you confirm ?')),
			'T_SHAREALBUM_RENEW' => sharealbum_escape_apostrophe(l10n('Renew link')),
			'T_SHAREALBUM_CANCEL' => sharealbum_escape_apostrophe(l10n('Cancel sharing')),
			'T_SHAREALBUM_CANCEL_WARNING' => sharealbum_escape_apostrophe(l10n('Are you sure you wish to cancel this album sharing ?')),
			'T_SHAREALBUM_SHARE' => sharealbum_escape_apostrophe(l10n('Share this album')),
			'T_SHAREALBUM_LINK_CREATED' => sharealbum_escape_apostrophe(l10n('Share link was successfully created. Click the share button to display it.')),
			'T_SHAREALBUM_LINK_RENEWED' => sharealbum_escape_apostrophe(l10n('Link was successfully renewed. Click the share button to display it.')),
			'T_SHAREALBUM_LINK_CANCELLED' => sharealbum_escape_apostrophe(l10n('Link was successfully deleted. Album is no longer publicly shared.'))
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
	    // Depending on the configuration setting 'option_recursive_shares', check whether the button should be displayed on single album pages (album with photos in it) or
	    // if a album without any photo (a root dir for example)
	    // if option_recursive_shares is true :    any private category page
	    // else                                    only private categories with at least one photo 
	    
	    if ( isset($page['category']['status']) and ($page['category']['status'] == 'private') and sharealbum_is_poweruser($user['id']))
		{
		    if (($conf['sharealbum']['option_recursive_shares']) or (!($conf['sharealbum']['option_recursive_shares']) && (count($page['items'])>0))) {
		        $template->add_index_button($button, BUTTONS_RANK_NEUTRAL);
		    }
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
	// Init variables
	$template->assign('SHAREALBUM_LINK_IS_ACTIVE',0);
	$template->assign('SHAREALBUM_USER_MESSAGE','');
	$template->assign('SHAREALBUM_LINK_CREATE','');
		
	
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
			$template->assign('SHAREALBUM_LINK_CREATE',get_root_url()."?".SHAREALBUM_URL_ACTION."=".SHAREALBUM_URL_ACTION_CREATE."&".SHAREALBUM_URL_CATEGORY."=".$page['category']['id']);
		}
	}
}
