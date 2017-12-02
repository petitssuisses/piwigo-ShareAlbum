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
$log_category = 0;
$shared_albums_logs=array();
if (isset($_GET['log'])) {
	$shared_albums_logs_query = "SELECT s.cat_id as 'category', c.name as `album`, s.visit_d as 'visit_date', s.ip as 'ip'
			FROM ".SHAREALBUM_TABLE_LOG." s
			LEFT JOIN ".$prefixeTable."categories c
				ON c.id = s.cat_id
			WHERE s.cat_id=".$_GET['log']."
			ORDER BY s.visit_d DESC";
	$shared_albums_logs = query2array($shared_albums_logs_query);
	$log_category = $_GET['log'];
}

// send config to template
$template->assign(array(
		'sharealbum' => $conf['sharealbum'],
		'shared_albums' => $shared_albums,
		'shared_albums_logs' => $shared_albums_logs,
		'log_category' => $log_category,
		'shared_root_path' => PHPWG_ROOT_PATH,
		'shared_album_logs' => preg_replace("/&log=(\d)*/","",$_SERVER['REQUEST_URI']).'&log=',
));

// define template file
$template->set_filename('sharealbum_content', realpath(SHAREALBUM_PATH . 'admin/template/albums.tpl'));