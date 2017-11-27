<?php
/**
 * This is the main administration page, if you have only one admin page you can put
 * directly its code here or using the tabsheet system like bellow
 */

defined('SHAREALBUM_PATH') or die('Hacking attempt!');

global $template, $page, $conf;

// get current tab
$page['tab'] = isset($_GET['tab']) ? $_GET['tab'] : $page['tab'] = 'config';

// plugin tabsheet is not present on photo page
if ($page['tab'] != 'photo')
{
  // tabsheet
  include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
  $tabsheet = new tabsheet();
  $tabsheet->set_id('ShareAlbum');

  $tabsheet->add('config', l10n('Configuration'), SHAREALBUM_ADMIN . '-config');
  $tabsheet->add('albums', l10n('Shared Albums'), SHAREALBUM_ADMIN . '-albums');
 
  $tabsheet->select($page['tab']);
  $tabsheet->assign();
}

// include page
include(SHAREALBUM_PATH . 'admin/' . $page['tab'] . '.php');

// template vars
$template->assign(array(
  'SHAREALBUM_PATH'=> SHAREALBUM_PATH, // used for images, scripts, ... access
  'SHAREALBUM_ABS_PATH'=> realpath(SHAREALBUM_PATH), // used for template inclusion (Smarty needs a real path)
  'SHAREALBUM_ADMIN' => SHAREALBUM_ADMIN,
  ));

// send page content
$template->assign_var_from_handle('ADMIN_CONTENT', 'sharealbum_content');
