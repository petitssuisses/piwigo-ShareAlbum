<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

/**
 * 
 * @author arnaud
 *
 */
class ShareAlbum_maintain extends PluginMaintain
{
  private $default_conf = array(
    'option_hide_menus' => true,			// option to hide menus for automatically logged in users
    'option_show_login_menu' => true,		// option to show a login menu
    'option_replace_breadcrumbs' => true,	// option to replace navigation breadcrumbs with album name
    'option_remember_me' => true,			// option to indicate remember me option for logged in users
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

    // add config parameter
    if (empty($conf['sharealbum']))
    {
      // conf_update_param well serialize and escape array before database insertion
      // the third parameter indicates to update $conf['sharealbum'] global variable as well
      conf_update_param('sharealbum', $this->default_conf, true);
    }
    else
    {
      $old_conf = safe_unserialize($conf['sharealbum']);
      conf_update_param('sharealbum', $old_conf, true);
    }

    // add the piwigo_sharealbum configuration table
    pwg_query('
CREATE TABLE IF NOT EXISTS `'. $this->table .'` (
  	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  	`cat` smallint(5) unsigned NOT NULL,
    `user_id`mediumint(8) unsigned NOT NULL,
    `code` varchar(32) NOT NULL,
    `creation_date` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
');
    
    pwg_query('
CREATE TABLE IF NOT EXISTS `'. $this->table_log .'` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`cat_id` smallint(5) unsigned NOT NULL,
	`ip` varchar(40) DEFAULT NULL,
	`visit_d` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
');

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
  	global $conf;
  	// Check missing params from successive update 
  	foreach (array_keys($this->default_conf) as $default_conf_key) {
  		$old_conf = safe_unserialize($conf['sharealbum']);
  		if (!array_key_exists($default_conf_key,$old_conf)) {
  			$old_conf += [ $default_conf_key => $this->default_conf[$default_conf_key] ];
  			conf_update_param('sharealbum', $old_conf, true);
  			
		}
		
  	}
  	
    // I (mistic100) chosed to handle install and update in the same method
    // you are free to do otherwize
  	
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

    // delete table
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