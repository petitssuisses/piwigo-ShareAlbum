<?php
/**
 *
 * @author petitssuisses
 *
 */

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

// load functions

class ShareAlbum_maintain extends PluginMaintain
{
  private $default_conf = array(
    'option_hide_menus' => true,			// option to hide menus for automatically logged in users
    'option_show_login_menu' => true,		// option to show a login menu
    'option_replace_breadcrumbs' => true,	// option to replace navigation breadcrumbs with album name
    'option_remember_me' => true,			// option to indicate remember me option for logged in users
    'option_pics_per_page' => 15,           // option to specify the maximum pictures per page
    'option_enable_powerusers' => false,    // option to enable power to selected non-admin users to share private albums
    'option_recursive_shares' => true,      // option to enable recursive shares (on sub-albums)
    );

  private $table;
  private $table_log;
  private $dir;

  function __construct($plugin_id)
  {
    parent::__construct($plugin_id); // always call parent constructor

    global $prefixeTable;

    // Class members can't be declared with computed values so initialization is done here
    $this->table = $prefixeTable . 'sharealbum';
    $this->table_log = $prefixeTable . 'sharealbum_log';
    $this->dir = PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'ShareAlbum/';
  }

  /**
   * Plugin installation
   *
   * Perform here all needed step for the plugin installation such as create default config,
   * add database tables, add fields to existing tables, create local folders...
   */
  function install($plugin_version, &$errors=array())
  {
    global $conf;

    // Add configuration parameters
    if (empty($conf['sharealbum']))
    {
      // conf_update_param well serialize and escape array before database insertion
      // the third parameter indicates to update $conf['sharealbum'] global variable as well
      conf_update_param('sharealbum', $this->default_conf, true);
    } else {
        // Check missing params from successive update
        foreach (array_keys($this->default_conf) as $default_conf_key) {
            $old_conf = safe_unserialize($conf['sharealbum']);
            if (!array_key_exists($default_conf_key,$old_conf)) {
                $old_conf += [ $default_conf_key => $this->default_conf[$default_conf_key] ];
                conf_update_param('sharealbum', $old_conf, true);
            }
        }
    }

    // Add the piwigo_sharealbum table
    pwg_query('
    CREATE TABLE IF NOT EXISTS `'. $this->table .'` (
      	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      	`cat` smallint(5) unsigned NOT NULL,
        `user_id` mediumint(8) unsigned NOT NULL,
        `code` varchar(32) NOT NULL,
        `creation_date` datetime DEFAULT NULL,
        `created_by` mediumint(8) unsigned DEFAULT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
    ');
    
    // Add the piwigo_sharealbum_logs table
    pwg_query('
    CREATE TABLE IF NOT EXISTS `'. $this->table_log .'` (
    	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    	`cat_id` smallint(5) unsigned NOT NULL,
    	`ip` varchar(40) DEFAULT NULL,
    	`visit_d` datetime DEFAULT NULL,
    	PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
    ');

    // Create the sharealbum group
    $res_group = pwg_query("SELECT `id` FROM `".GROUPS_TABLE."` WHERE `name`='sharealbum'");
    if (!pwg_db_num_rows($res_group)) {
        pwg_query("INSERT INTO `".GROUPS_TABLE."` (`name`) VALUES ('sharealbum')");
        
        // Automatically assign group to previously created sharealbum users
        pwg_query("
  			INSERT IGNORE INTO `".USER_GROUP_TABLE."` (group_id, user_id)
			SELECT g.id, u.id
			FROM `".GROUPS_TABLE."` g, `".USERS_TABLE."` u
			WHERE g.name like 'sharealbum'
			AND u.username like 'share_%'
  		");
    }
    
    // Create the sharealbum_powerusers group - added for version 11.4
    $res_group = pwg_query("SELECT `id` FROM `".GROUPS_TABLE."` WHERE `name`='sharealbum_powerusers'");
    if (!pwg_db_num_rows($res_group)) {
        pwg_query("INSERT INTO `".GROUPS_TABLE."` (`name`) VALUES ('sharealbum_powerusers')");
    }
    
    // Column sharealbum.created_by - added for version 11.4
    $res_col = pwg_query("SHOW COLUMNS FROM `".$this->table."` LIKE 'created_by'");
    if (!pwg_db_num_rows($res_col))
    {
        pwg_query('ALTER TABLE `'.$this->table.'` ADD `created_by` mediumint(8) DEFAULT NULL;');
    }
    
    // create a local directory
    if (!file_exists($this->dir))
    {
      mkdir($this->dir, 0755);
    }    
  }

  /**
   * Plugin activation
   *
   * This function is triggered after installation, by manual activation or after a plugin update
   * for this last case you must manage updates tasks of your plugin in this function
   */
  function activate($plugin_version, &$errors=array())
  {
  }

  /**
   * Plugin deactivation
   *
   * Triggered before uninstallation or by manual deactivation
   */
  function deactivate()
  {
  }

  /**
   * Plugin (auto)update
   *
   * This function is called when Piwigo detects that the registered version of
   * the plugin is older than the version exposed in main.inc.php
   * Thus it's called after a plugin update from admin panel or a manual update by FTP
   */
  function update($old_version, $new_version, &$errors=array())
  {
      $this->install($new_version, $errors);
  }

  /**
   * Plugin uninstallation
   *
   * Perform here all cleaning tasks when the plugin is removed
   * you should revert all changes made in 'install'
   */
  function uninstall()
  {
    // delete configuration
    conf_delete_param('sharealbum');

    // Delete sharealbum users
    $res_sharealbum_users = pwg_query("SELECT user_id FROM ".$this->table);
    while ($row = pwg_db_fetch_assoc($res_sharealbum_users)) {
        delete_user($row['user_id']);
    }
 
    // Delete sharealbum groups (sharealbum, sharealbum_powerusers)
    $res_sharealbum_groups = pwg_query("SELECT id FROM `".GROUPS_TABLE."` WHERE name IN ('sharealbum','sharealbum_powerusers')");
    $sharealbum_groups_ids = array();
    while ($row = pwg_db_fetch_assoc($res_sharealbum_groups)) {
        array_push($sharealbum_groups_ids,$row['id']);
    }
    delete_groups($sharealbum_groups_ids);
    
    // delete sharealbum tables
    pwg_query('DROP TABLE `'. $this->table .'`;');
    pwg_query('DROP TABLE `'. $this->table_log .'`;');
  
    // delete local folder
    // use a recursive function if you plan to have nested directories
    foreach (scandir($this->dir) as $file)
    {
      if ($file == '.' or $file == '..') continue;
      unlink($this->dir.$file);
    }
    rmdir($this->dir);
  }
}