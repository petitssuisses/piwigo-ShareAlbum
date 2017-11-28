<?php
defined('SHAREALBUM_PATH') or die('Hacking attempt!');

// +-----------------------------------------------------------------------+
// | Configuration tab                                                     |
// +-----------------------------------------------------------------------+

// save config
if (isset($_POST['save_config']))
{
  $conf['sharealbum'] = array(
    'option_hide_menus' => isset($_POST['option_hide_menus']),
  	'option_replace_breadcrumbs' => isset($_POST['option_replace_breadcrumbs']),
  	'option_show_login_menu' => isset($_POST['option_show_login_menu']),
  	'option_remember_me' => isset($_POST['option_remember_me']),
    );

  conf_update_param('sharealbum', $conf['sharealbum']);
  $page['infos'][] = l10n('Information data registered in database');
}

// send config to template
$template->assign(array(
  'sharealbum' => $conf['sharealbum'],
  'INTRO_CONTENT' => load_language('intro.html', SHAREALBUM_PATH, array('return'=>true)),
  ));

// define template file
$template->set_filename('sharealbum_content', realpath(SHAREALBUM_PATH . 'admin/template/config.tpl'));
