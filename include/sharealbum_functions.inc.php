<?php
/**
 * Gives user permission to access a private category
 * @param unknown $user_id
 * @param unknown $cat_id
 * @return true success
 */
function sharealbum_grant_private_category($user_id,$cat_id) {
	if (pwg_query("
  		INSERT INTO `".USER_ACCESS_TABLE."` (`user_id`,`cat_id`)
  		VALUES (".$user_id.",".$cat_id.")
  	")) {
		return true;	
	} else {
		return false;
	}
}

/**
 * Creates a new user for being used with an album share
 * @param string $username Username
 * @param int $password_length Password length
 * @return int user_id in case of success | NULL in case of error
 */
function sharealbum_register_user($username,$password_length) {
	global $page;
	$new_user_id = -1;
	$new_user_id = register_user($username,sharealbum_generate_code($password_length, false,true),null,0,$page['errors'],false);
	if (pwg_query("
	      UPDATE `".USER_INFOS_TABLE."`
	      SET `status` = 'generic'
	      WHERE `user_id` = ".$new_user_id.";
	")) {
		return $new_user_id;
	} else {
		return NULL;
	}
}

/**
 * Get the share code for a specified album (category)
 * Returns the album code or null if not found
 * @param unknown $cat
 * @return Ambigous <NULL, unknown>
 */
function sharealbum_get_share_code($cat)
{
	$code = NULL;
	// Check if category is already shared using ShareAlbum plugin
	$result = pwg_query("
				SELECT `id`,`code`
				FROM `".SHAREALBUM_TABLE."`
				WHERE
					`cat` = ".$cat
	);
	if (pwg_db_num_rows($result))
	{
		// Existing code found for this album
		$row = pwg_db_fetch_assoc($result);
		$code = $row['code'];
	}
	return $code;
}

/**
 * Generates a random code of the desired length.
 * @param int $len Target length of the desired code
 * @param boolean $lower true to lowercase the generated code
 * @param boolean $use_special_chars true to use special characters
 * @return string
 */
function sharealbum_generate_code($len,$lower,$use_special_chars) {
	$chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ023456789";
	$special_chars = "<>!+%&/()=?`!$.,;-";
	if ($use_special_chars)
	{
		$chars = $chars.$special_chars;
	}
	srand((double)microtime()*1000000);
	$i = 0;
	$code = '' ;

	while ($i < $len) {
		$num = rand() % 33;
		$tmp = substr($chars, $num, 1);
		$code = $code . $tmp;
		$i++;
	}
	if ($lower) {
		$code = strtolower($code);
	}
	return $code;
}
?>