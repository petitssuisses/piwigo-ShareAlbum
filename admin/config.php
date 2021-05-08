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
    'option_pics_per_page' => $_POST['option_pics_per_page'],
    'option_enable_powerusers'=>isset($_POST['option_enable_powerusers']),
    'option_recursive_shares'=>isset($_POST['option_recursive_shares'])
    );
  conf_update_param('sharealbum', $conf['sharealbum']);
  sharealbum_set_nb_image_page($_POST['option_pics_per_page']);
  $page['infos'][] = l10n('Information data registered in database');
}

// Possible number of images per page for shared albums (same as in Piwigo core)
$nb_image_page_values = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,35,40,45,50,60,70,80,90,100,200,300,500,999);
// send config to template
$template->assign(array(
  'sharealbum' => $conf['sharealbum'],
  'nb_image_page_values' => $nb_image_page_values,
  'INTRO_CONTENT' => load_language('intro.html', SHAREALBUM_PATH, array('return'=>true)),
  ));

// define template file
$template->set_filename('sharealbum_content', realpath(SHAREALBUM_PATH . 'admin/template/config.tpl'));
