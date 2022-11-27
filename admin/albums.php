<?php
defined('SHAREALBUM_PATH') or die('Hacking attempt!');

// +-----------------------------------------------------------------------+
// | Shared Albums tab                                                     |
// +-----------------------------------------------------------------------+

global $prefixeTable, $conf;

$filter_sort_field = "s.creation_date";
$filter_sort_order = "DESC";
$filter_show_links = "no";


if (isset($_POST['apply_filter'])) {
	if (isset($_POST['sort_order'])) {
		$filter_sort_order = $_POST['sort_order'];
	}
	if (isset($_POST['sort_field'])) {
		switch ($_POST['sort_field']) {
			case "sort_creation_date":
				$filter_sort_field = "s.creation_date";
				break;
			case "sort_album_name":
				$filter_sort_field = "c.name";
				break;
			case "sort_expiration_date":
				$filter_sort_field = "s.expiration_d";
				break;
			case "sort_visits":
				$filter_sort_field = "visits";
				break;
			case "sort_last_visit":
			    $filter_sort_field = "l.visit_d";
		}
	}
	if (isset($_POST['show_link']) && $_POST['show_link']=="yes") {
		$filter_show_links = "yes";
	}
}

// Create a new share action
if (isset($_POST['create']) && isset ($_POST['new_share_cat']) && ($_POST['new_share_cat']!="")) {
	sharealbum_create($_POST['new_share_cat']);
}

// Actions on existing shares
if (isset($_POST['p_sharedalbums_action']) && isset($_POST['sa_cat']) && !empty($_POST['sa_cat'])) {
	if ($_POST['p_sharedalbums_action'] == "renew") {
	    foreach($_POST['sa_cat'] as $p_cat) {
	       sharealbum_renew_share($p_cat);
	    }
	} elseif ($_POST['p_sharedalbums_action'] == "cancel") {
	    foreach($_POST['sa_cat'] as $p_cat) {
	        sharealbum_cancel_share($p_cat);
	    }
	}		
}

// Private albums which are not already shared
// Displays only albums containing images
$private_albums_query = "
	SELECT c.*, COUNT(ic.image_id) as nb_images
	FROM ".IMAGE_CATEGORY_TABLE." ic, ".CATEGORIES_TABLE." c
	WHERE ic.category_id IN
		(
			SELECT c.id
			FROM ".CATEGORIES_TABLE." c
			LEFT JOIN ".SHAREALBUM_TABLE." s
			ON c.id = s.cat
			WHERE s.cat IS NULL
			AND c.status = 'private'
		)
	AND c.id = ic.category_id
	GROUP BY ic.category_id";
// if option_recursive_shares is active, do not limit to single albums with photos but show all private non-shared albums
if ($conf['sharealbum']['option_recursive_shares']) {
    $private_albums_query = "
        SELECT c.*
        FROM  ".CATEGORIES_TABLE." c 
        LEFT JOIN ".SHAREALBUM_TABLE." s 
        ON c.id = s.cat
        WHERE s.cat IS NULL 
        AND c.status = 'private'
        ORDER BY global_rank
        ";
}

$shareable_albums = query2array($private_albums_query);
// replace album name with full path to album (with uppercats)
foreach ($shareable_albums as &$album) {
	$name_with_uppercats = sharealbum_getname_with_uppercats($album['name'],$album['uppercats']);
	$album['name'] = $name_with_uppercats;
}
// Sorts the output (sort by album name-with uppercats)
$columns = array_column($shareable_albums,'name');
array_multisort($columns, SORT_ASC, $shareable_albums);

// 
$shared_albums_query = "SELECT s.id, s.cat as 'category', c.name as `album`, c.uppercats as `uppercats`, s.user_id, u.username as `user`, s.code as 'code', s.creation_date as `creation_date`, count(l.id) as `visits`, max(l.visit_d) as `last_visit`, s.created_by, uc.username as `shared_by`
	FROM ".SHAREALBUM_TABLE." s
	LEFT JOIN ".SHAREALBUM_TABLE_LOG." l
		ON s.cat = l.cat_id 
	LEFT JOIN ".$prefixeTable."categories c
		ON c.id = s.cat
	LEFT JOIN ".$prefixeTable."users u
		ON u.id = s.user_id
    LEFT JOIN ".$prefixeTable."users uc
    	ON s.created_by = uc.id
	GROUP BY s.id
	ORDER BY ".$filter_sort_field." ".$filter_sort_order;

$shared_albums = query2array($shared_albums_query);
// replace code with the absolute URL to access the shared album
// replace album name with full path to album (with uppercats)
foreach ($shared_albums as &$shared_album) {
	$code=sharealbum_get_shareable_url($shared_album['code']);
	$shared_album['code']=$code;
	$name_with_uppercats = sharealbum_getname_with_uppercats($shared_album['album'],$shared_album['uppercats']);
	$shared_album['album']=$name_with_uppercats;
	if (strlen($shared_album['album'])>70) {
	    $shared_album['album_short']=substr($shared_album['album'],0,70)."...";
	} else {
	    $shared_album['album_short']=$shared_album['album'];
	}
}

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
		'shareable_albums' => $shareable_albums,
		'shared_albums' => $shared_albums,
		'shared_albums_logs' => $shared_albums_logs,
		'log_category' => $log_category,
		'shared_root_path' => PHPWG_ROOT_PATH,
		'shared_album_logs' => preg_replace("/&log=(\d)*/","",$_SERVER['REQUEST_URI']).'&log=',
));

// define template file
$template->set_filename('sharealbum_content', realpath(SHAREALBUM_PATH . 'admin/template/albums.tpl'));