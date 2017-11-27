<?php
defined('SHAREALBUM_PATH') or die('Hacking attempt!');

// +-----------------------------------------------------------------------+
// | Shared Albums tab                                                     |
// +-----------------------------------------------------------------------+

global $prefixeTable;

// 
$shared_albums_query = "SELECT s.id, s.cat as 'category', c.name as `album`, s.user_id, u.username as `user`, s.code as 'code', s.creation_date as `creation_date`, count(l.id) as `visits`, max(l.visit_d) as `last_visit`
	FROM ".SHAREALBUM_TABLE." s
	LEFT JOIN ".SHAREALBUM_TABLE_LOG." l
		ON s.cat = l.cat_id 
	LEFT JOIN ".$prefixeTable."categories c
		ON c.id = s.cat
	LEFT JOIN ".$prefixeTable."users u
		ON u.id = s.user_id
	GROUP BY s.id";
$shared_albums = query2array($shared_albums_query);

// send config to template
$template->assign(array(
		'sharealbum' => $conf['sharealbum'],
		'shared_albums' => $shared_albums,
		'shared_root_path' => PHPWG_ROOT_PATH,
		//'INTRO_CONTENT' => load_language('intro.html', SHAREALBUM_PATH, array('return'=>true)),
));

// define template file
$template->set_filename('sharealbum_content', realpath(SHAREALBUM_PATH . 'admin/template/albums.tpl'));