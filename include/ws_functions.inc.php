<?php
/**
 * Web services for ShareAlbum plugin
 */

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

/**
 * Register web service methods
 */
function sharealbum_ws_register_methods($arr)
{
  $service = &$arr[0];
  
  // Get list of all shared albums
  $service->addMethod(
    'sharealbum.getList',
    'sharealbum_ws_get_list',
    array(
      'sort_field' => array('default' => 'creation_date', 'info' => 'Field to sort by: creation_date, album_name, visits, last_visit'),
      'sort_order' => array('default' => 'DESC', 'info' => 'Sort order: ASC or DESC'),
    ),
    'Returns the list of all shared albums.'
  );
  
  // Get information about a specific shared album
  $service->addMethod(
    'sharealbum.getInfo',
    'sharealbum_ws_get_info',
    array(
      'category_id' => array('type' => WS_TYPE_ID, 'info' => 'Category ID of the shared album'),
    ),
    'Returns information about a specific shared album.'
  );
  
  // Create a new share for an album
  $service->addMethod(
    'sharealbum.create',
    'sharealbum_ws_create',
    array(
      'category_id' => array('type' => WS_TYPE_ID, 'info' => 'Category ID of the album to share'),
    ),
    'Creates a new share for an album. Returns the share URL and code.'
  );
  
  // Cancel a share
  $service->addMethod(
    'sharealbum.cancel',
    'sharealbum_ws_cancel',
    array(
      'category_id' => array('type' => WS_TYPE_ID, 'info' => 'Category ID of the shared album to cancel'),
    ),
    'Cancels a share for an album.'
  );
  
  // Renew a share code
  $service->addMethod(
    'sharealbum.renew',
    'sharealbum_ws_renew',
    array(
      'category_id' => array('type' => WS_TYPE_ID, 'info' => 'Category ID of the shared album to renew'),
    ),
    'Renews the share code for an album. Returns the new share URL and code.'
  );
  
  // Get visit logs for a shared album
  $service->addMethod(
    'sharealbum.getLogs',
    'sharealbum_ws_get_logs',
    array(
      'category_id' => array('type' => WS_TYPE_ID, 'info' => 'Category ID of the shared album'),
      'per_page' => array('type' => WS_TYPE_INT, 'default' => 100, 'info' => 'Number of log entries per page'),
      'page' => array('type' => WS_TYPE_INT, 'default' => 0, 'info' => 'Page number (0-based)'),
    ),
    'Returns visit logs for a shared album.'
  );
  
  // Get list of albums that can be shared
  $service->addMethod(
    'sharealbum.getShareableAlbums',
    'sharealbum_ws_get_shareable_albums',
    array(),
    'Returns the list of private albums that are not yet shared and can be shared.'
  );
}

/**
 * Web service: Get list of all shared albums
 */
function sharealbum_ws_get_list($params, &$service)
{
  global $user, $prefixeTable;
  
  // Check permissions
  if (!sharealbum_is_poweruser($user['id'])) {
    return new PwgError(403, 'Access denied');
  }
  
  $sort_field = isset($params['sort_field']) ? $params['sort_field'] : 'creation_date';
  $sort_order = isset($params['sort_order']) ? $params['sort_order'] : 'DESC';
  
  // Map sort field names to database columns
  $sort_field_map = array(
    'creation_date' => 's.creation_date',
    'album_name' => 'c.name',
    'visits' => 'visits',
    'last_visit' => 'l.visit_d',
  );
  
  $db_sort_field = isset($sort_field_map[$sort_field]) ? $sort_field_map[$sort_field] : 's.creation_date';
  $db_sort_order = (strtoupper($sort_order) == 'ASC') ? 'ASC' : 'DESC';
  
  // Ensure sort field is safe (whitelist approach)
  if (!isset($sort_field_map[$sort_field])) {
    $db_sort_field = 's.creation_date';
  }
  
  $query = "SELECT s.id, s.cat as 'category_id', c.name as `album_name`, c.uppercats as `uppercats`, 
            s.user_id, u.username as `username`, s.code as 'code', s.creation_date as `creation_date`, 
            count(l.id) as `visits`, max(l.visit_d) as `last_visit`, s.created_by, uc.username as `created_by_username`
            FROM ".SHAREALBUM_TABLE." s
            LEFT JOIN ".SHAREALBUM_TABLE_LOG." l ON s.cat = l.cat_id 
            LEFT JOIN ".$prefixeTable."categories c ON c.id = s.cat
            LEFT JOIN ".$prefixeTable."users u ON u.id = s.user_id
            LEFT JOIN ".$prefixeTable."users uc ON s.created_by = uc.id
            GROUP BY s.id
            ORDER BY ".$db_sort_field." ".$db_sort_order;
  
  $shared_albums = query2array($query);
  
  // Process results
  foreach ($shared_albums as &$album) {
    $album['share_url'] = sharealbum_get_shareable_url($album['code']);
    $album['album_path'] = sharealbum_getname_with_uppercats($album['album_name'], $album['uppercats']);
    // Remove sensitive information
    unset($album['code']);
    unset($album['user_id']);
    unset($album['username']);
  }
  
  return array(
    'shared_albums' => $shared_albums,
    'count' => count($shared_albums),
  );
}

/**
 * Web service: Get information about a specific shared album
 */
function sharealbum_ws_get_info($params, &$service)
{
  global $user, $prefixeTable;
  
  // Check permissions
  if (!sharealbum_is_poweruser($user['id'])) {
    return new PwgError(403, 'Access denied');
  }
  
  $category_id = (int)$params['category_id'];
  
  // Verify category exists
  $category_query = "SELECT id, name, status FROM ".CATEGORIES_TABLE." WHERE id = ".$category_id;
  $category_result = query2array($category_query);
  if (empty($category_result)) {
    return new PwgError(404, 'Category not found');
  }
  
  // Get share information
  $query = "SELECT s.id, s.cat as 'category_id', c.name as `album_name`, c.uppercats as `uppercats`, 
            s.user_id, u.username as `username`, s.code as 'code', s.creation_date as `creation_date`, 
            count(l.id) as `visits`, max(l.visit_d) as `last_visit`, s.created_by, uc.username as `created_by_username`
            FROM ".SHAREALBUM_TABLE." s
            LEFT JOIN ".SHAREALBUM_TABLE_LOG." l ON s.cat = l.cat_id 
            LEFT JOIN ".$prefixeTable."categories c ON c.id = s.cat
            LEFT JOIN ".$prefixeTable."users u ON u.id = s.user_id
            LEFT JOIN ".$prefixeTable."users uc ON s.created_by = uc.id
            WHERE s.cat = ".$category_id."
            GROUP BY s.id";
  
  $result = query2array($query);
  
  if (empty($result)) {
    return new PwgError(404, 'Album is not shared');
  }
  
  $album = $result[0];
  $album['share_url'] = sharealbum_get_shareable_url($album['code']);
  $album['album_path'] = sharealbum_getname_with_uppercats($album['album_name'], $album['uppercats']);
  
  // Remove sensitive information
  unset($album['code']);
  unset($album['user_id']);
  unset($album['username']);
  
  return array('shared_album' => $album);
}

/**
 * Web service: Create a new share for an album
 */
function sharealbum_ws_create($params, &$service)
{
  global $user;
  
  // Check permissions
  if (!sharealbum_is_poweruser($user['id'])) {
    return new PwgError(403, 'Access denied');
  }
  
  $category_id = (int)$params['category_id'];
  
  // Verify category exists and is private
  $category_query = "SELECT id, name, status FROM ".CATEGORIES_TABLE." WHERE id = ".$category_id;
  $category_result = query2array($category_query);
  if (empty($category_result)) {
    return new PwgError(404, 'Category not found');
  }
  $category = $category_result[0];
  
  if ($category['status'] != 'private') {
    return new PwgError(400, 'Only private albums can be shared');
  }
  
  // Check if already shared
  $existing_code = sharealbum_get_share_code($category_id);
  if ($existing_code !== NULL) {
    return new PwgError(409, 'Album is already shared');
  }
  
  // Create the share
  sharealbum_create($category_id);
  
  // Get the created share information
  $code = sharealbum_get_share_code($category_id);
  if ($code === NULL) {
    return new PwgError(500, 'Failed to create share');
  }
  
  return array(
    'category_id' => $category_id,
    'share_code' => $code,
    'share_url' => sharealbum_get_shareable_url($code),
    'message' => 'Share created successfully',
  );
}

/**
 * Web service: Cancel a share
 */
function sharealbum_ws_cancel($params, &$service)
{
  global $user;
  
  // Check permissions
  if (!sharealbum_is_poweruser($user['id'])) {
    return new PwgError(403, 'Access denied');
  }
  
  $category_id = (int)$params['category_id'];
  
  // Verify category exists
  $category_query = "SELECT id, name, status FROM ".CATEGORIES_TABLE." WHERE id = ".$category_id;
  $category_result = query2array($category_query);
  if (empty($category_result)) {
    return new PwgError(404, 'Category not found');
  }
  
  // Check if shared
  $existing_code = sharealbum_get_share_code($category_id);
  if ($existing_code === NULL) {
    return new PwgError(404, 'Album is not shared');
  }
  
  // Cancel the share
  sharealbum_cancel_share($category_id);
  
  return array(
    'category_id' => $category_id,
    'message' => 'Share cancelled successfully',
  );
}

/**
 * Web service: Renew a share code
 */
function sharealbum_ws_renew($params, &$service)
{
  global $user;
  
  // Check permissions
  if (!sharealbum_is_poweruser($user['id'])) {
    return new PwgError(403, 'Access denied');
  }
  
  $category_id = (int)$params['category_id'];
  
  // Verify category exists
  $category_query = "SELECT id, name, status FROM ".CATEGORIES_TABLE." WHERE id = ".$category_id;
  $category_result = query2array($category_query);
  if (empty($category_result)) {
    return new PwgError(404, 'Category not found');
  }
  
  // Check if shared
  $existing_code = sharealbum_get_share_code($category_id);
  if ($existing_code === NULL) {
    return new PwgError(404, 'Album is not shared');
  }
  
  // Renew the share
  sharealbum_renew_share($category_id);
  
  // Get the new code
  $new_code = sharealbum_get_share_code($category_id);
  if ($new_code === NULL) {
    return new PwgError(500, 'Failed to renew share');
  }
  
  return array(
    'category_id' => $category_id,
    'share_code' => $new_code,
    'share_url' => sharealbum_get_shareable_url($new_code),
    'message' => 'Share renewed successfully',
  );
}

/**
 * Web service: Get visit logs for a shared album
 */
function sharealbum_ws_get_logs($params, &$service)
{
  global $user, $prefixeTable;
  
  // Check permissions
  if (!sharealbum_is_poweruser($user['id'])) {
    return new PwgError(403, 'Access denied');
  }
  
  $category_id = (int)$params['category_id'];
  $per_page = isset($params['per_page']) ? (int)$params['per_page'] : 100;
  $page = isset($params['page']) ? (int)$params['page'] : 0;
  
  // Validate pagination parameters
  if ($per_page < 1 || $per_page > 1000) {
    $per_page = 100;
  }
  if ($page < 0) {
    $page = 0;
  }
  
  // Verify category exists
  $category_query = "SELECT id, name, status FROM ".CATEGORIES_TABLE." WHERE id = ".$category_id;
  $category_result = query2array($category_query);
  if (empty($category_result)) {
    return new PwgError(404, 'Category not found');
  }
  
  // Check if shared
  $existing_code = sharealbum_get_share_code($category_id);
  if ($existing_code === NULL) {
    return new PwgError(404, 'Album is not shared');
  }
  
  // Get total count
  $count_query = "SELECT COUNT(*) as total FROM ".SHAREALBUM_TABLE_LOG." WHERE cat_id = ".$category_id;
  $count_result = query2array($count_query);
  $total = $count_result[0]['total'];
  
  // Get logs with pagination
  $offset = $page * $per_page;
  $query = "SELECT s.cat_id as 'category_id', c.name as `album_name`, s.visit_d as 'visit_date', s.ip as 'ip_address'
            FROM ".SHAREALBUM_TABLE_LOG." s
            LEFT JOIN ".$prefixeTable."categories c ON c.id = s.cat_id
            WHERE s.cat_id = ".$category_id."
            ORDER BY s.visit_d DESC
            LIMIT ".$per_page." OFFSET ".$offset;
  
  $logs = query2array($query);
  
  return array(
    'category_id' => $category_id,
    'logs' => $logs,
    'paging' => array(
      'page' => $page,
      'per_page' => $per_page,
      'count' => count($logs),
      'total_count' => $total,
    ),
  );
}

/**
 * Web service: Get list of albums that can be shared
 */
function sharealbum_ws_get_shareable_albums($params, &$service)
{
  global $user, $conf;
  
  // Check permissions
  if (!sharealbum_is_poweruser($user['id'])) {
    return new PwgError(403, 'Access denied');
  }
  
  // Private albums which are not already shared
  if ($conf['sharealbum']['option_recursive_shares']) {
    // Show all private non-shared albums
    $query = "SELECT c.*
              FROM ".CATEGORIES_TABLE." c 
              LEFT JOIN ".SHAREALBUM_TABLE." s ON c.id = s.cat
              WHERE s.cat IS NULL 
              AND c.status = 'private'
              ORDER BY global_rank";
  } else {
    // Only albums containing images
    $query = "SELECT c.*, COUNT(ic.image_id) as nb_images
              FROM ".IMAGE_CATEGORY_TABLE." ic, ".CATEGORIES_TABLE." c
              WHERE ic.category_id IN (
                SELECT c.id
                FROM ".CATEGORIES_TABLE." c
                LEFT JOIN ".SHAREALBUM_TABLE." s ON c.id = s.cat
                WHERE s.cat IS NULL
                AND c.status = 'private'
              )
              AND c.id = ic.category_id
              GROUP BY ic.category_id
              ORDER BY c.global_rank";
  }
  
  $shareable_albums = query2array($query);
  
  // Process results
  foreach ($shareable_albums as &$album) {
    $album['album_path'] = sharealbum_getname_with_uppercats($album['name'], $album['uppercats']);
  }
  
  // Sort by album path
  $columns = array_column($shareable_albums, 'album_path');
  array_multisort($columns, SORT_ASC, $shareable_albums);
  
  return array(
    'shareable_albums' => $shareable_albums,
    'count' => count($shareable_albums),
  );
}

