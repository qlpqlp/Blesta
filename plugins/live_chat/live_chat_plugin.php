<?php
/**
 * Live Chat plugin handler
 *
 * @package blesta
 * @subpackage blesta.plugins.live_chat
 * @copyright Copyright (c) 1998, Infoscan - Informática, Lda.
 * @link http://www.weblx.net/ WebLX
 */
class LiveChatPlugin extends Plugin {

	public function __construct() {
		Language::loadLang("live_chat_plugin", null, dirname(__FILE__) . DS . "language" . DS);
		$this->loadConfig(dirname(__FILE__) . DS . "config.json");
	}
	

	/**
	 * Performs any necessary bootstraping actions
	 *
	 * @param int $plugin_id The ID of the plugin being installed
	 */
	public function install($plugin_id) {
		if (!isset($this->Record))
			Loader::loadComponents($this, array("Record"));

		Configure::load("live_chat", dirname(__FILE__) . DS . "config" . DS);

        // get blesta dabase info
        $db_info = Configure::get("Blesta.database_info");
        $dbhost = $db_info['host'];
        $dbuser = $db_info['user'];
        $dbpass = $db_info['pass'];
        $dbname = $db_info['database'];
        $dbport = 3306;

        //duplicate settings file
        copy(PLUGINDIR . DS . "live_chat" . DS . "vendors" . DS . "blc" . DS . "settings" . DS . "_settings.ini.php",PLUGINDIR . DS . "live_chat" . DS . "vendors" . DS . "blc" . DS . "settings" . DS . "settings.ini.php");

		// find and replace the {{domain}} tag for the real domain
        $findcode = '{{domain}}';
        $putbchatcode = $_SERVER['HTTP_HOST'];
        $path_to_file = PLUGINDIR . DS . "live_chat" . DS . "vendors" . DS . "blc" . DS . "settings" . DS . "BlestaLiveChat.sql";
        $putchat = file_put_contents($path_to_file, str_replace($findcode, $putbchatcode, file_get_contents($path_to_file)));

		// find and replace the {{mysqlhost}} tag for the real blesta mysqlserver
        $findcode = '{{mysqlhost}}';
        $putbchatcode = $dbhost;
        $path_to_file = PLUGINDIR . DS . "live_chat" . DS . "vendors" . DS . "blc" . DS . "settings" . DS . "settings.ini.php";
        $putchat = file_put_contents($path_to_file, str_replace($findcode, $putbchatcode, file_get_contents($path_to_file)));

		// find and replace the {{user}} tag for the real blesta mysql username
        $findcode = '{{user}}';
        $putbchatcode = $dbuser;
        $path_to_file = PLUGINDIR . DS . "live_chat" . DS . "vendors" . DS . "blc" . DS . "settings" . DS . "settings.ini.php";
        $putchat = file_put_contents($path_to_file, str_replace($findcode, $putbchatcode, file_get_contents($path_to_file)));

		// find and replace the {{pass}} tag for the real blesta mysql password
        $findcode = '{{pass}}';
        $putbchatcode = $dbpass;
        $path_to_file = PLUGINDIR . DS . "live_chat" . DS . "vendors" . DS . "blc" . DS . "settings" . DS . "settings.ini.php";
        $putchat = file_put_contents($path_to_file, str_replace($findcode, $putbchatcode, file_get_contents($path_to_file)));

		// find and replace the {{db}} tag for the real blesta mysql DB name
        $findcode = '{{db}}';
        $putbchatcode = $dbname;
        $path_to_file = PLUGINDIR . DS . "live_chat" . DS . "vendors" . DS . "blc" . DS . "settings" . DS . "settings.ini.php";
        $putchat = file_put_contents($path_to_file, str_replace($findcode, $putbchatcode, file_get_contents($path_to_file)));

		// find and replace the {{port}} tag for the real blesta mysql port
        $findcode = '{{port}}';
        $putbchatcode = $dbport;
        $path_to_file = PLUGINDIR . DS . "live_chat" . DS . "vendors" . DS . "blc" . DS . "settings" . DS . "settings.ini.php";
        $putchat = file_put_contents($path_to_file, str_replace($findcode, $putbchatcode, file_get_contents($path_to_file)));

   		// Add all support tables, *IFF* not already added
        $sql_load = file_get_contents(PLUGINDIR . DS . "live_chat" . DS . "vendors" . DS . "blc" . DS . "settings" . DS . "BlestaLiveChat.sql");
        $this->Record->query($sql_load);

        //because we cannot get the ID from a field we will add the code to make the magic
        $findcode = '</body>';
        $putbchatcode = '<?include(PLUGINDIR . DS . "live_chat" . DS . "views" . DS . "default" . DS . "admin_live_chat_count_include.pdt");?></body>';
        $path_to_file = VIEWDIR . "admin" . DS . "default" . DS . "structure.pdt";
        $putchat = file_put_contents($path_to_file, str_replace($findcode, $putbchatcode, file_get_contents($path_to_file)));

        //because we cannot get the ID from a field we will add the code to make the magic
        $findcode = '</body>';
        $putbchatcode = '<?include(PLUGINDIR . DS . "live_chat" . DS . "views" . DS . "default" . DS . "client_live_chat_include.pdt");?></body>';
        $path_to_file = VIEWDIR . "client" . DS . "default" . DS . "structure.pdt";
        $putchat = file_put_contents($path_to_file, str_replace($findcode, $putbchatcode, file_get_contents($path_to_file)));


	}

	/**
	 * Performs migration of data from $current_version (the current installed version)
	 * to the given file set version
	 *
	 * @param string $current_version The current installed version of this plugin
	 * @param int $plugin_id The ID of the plugin being upgraded
	 */
	public function upgrade($current_version, $plugin_id) {
		// Upgrade if possible
		if (version_compare($this->getVersion(), $current_version, ">")) {
			// Handle the upgrade, set errors using $this->Input->setErrors() if any errors encountered
			
			if (version_compare("1.0.1", $current_version, "<=")) {
				$this->setDefaultSettings();
			}
		}
	}

	/**
	 * Performs any necessary cleanup actions
	 *
	 * @param int $plugin_id The ID of the plugin being uninstalled
	 * @param boolean $last_instance True if $plugin_id is the last instance across all companies for this plugin, false otherwise
	 */
	public function uninstall($plugin_id, $last_instance) {
		if (!isset($this->Record))
			Loader::loadComponents($this, array("Record"));

		if ($last_instance) {
			// Remove all settings created by this plugin
		    $this->Record->drop("lh_abstract_auto_responder");
			$this->Record->drop("lh_abstract_email_template");
			$this->Record->drop("lh_abstract_proactive_chat_invitation");
			$this->Record->drop("lh_canned_msg");
			$this->Record->drop("lh_chat");
			$this->Record->drop("lh_chatbox");
			$this->Record->drop("lh_chat_accept");
			$this->Record->drop("lh_chat_archive_range");
			$this->Record->drop("lh_chat_blocked_user");
			$this->Record->drop("lh_chat_config");
			$this->Record->drop("lh_chat_file");
			$this->Record->drop("lh_chat_online_user");
			$this->Record->drop("lh_chat_online_user_footprint");
			$this->Record->drop("lh_departament");
			$this->Record->drop("lh_forgotpasswordhash");
			$this->Record->drop("lh_group");
			$this->Record->drop("lh_grouprole");
			$this->Record->drop("lh_groupuser");
			$this->Record->drop("lh_msg");
			$this->Record->drop("lh_role");
			$this->Record->drop("lh_rolefunction");
			$this->Record->drop("lh_transfer");
			$this->Record->drop("lh_userdep");
			$this->Record->drop("lh_users");
			$this->Record->drop("lh_users_remember");
			$this->Record->drop("lh_users_setting");
			$this->Record->drop("lh_users_setting_option");

        //because we cannot get the ID from a field we will add the code to make the magic
        $findcode = '<?include(PLUGINDIR . DS . "live_chat" . DS . "views" . DS . "default" . DS . "admin_live_chat_count_include.pdt");?>';
        $rmbchatcode = '';
        $path_to_file = VIEWDIR . "admin" . DS . "default" . DS . "structure.pdt";
        $rmchat = file_put_contents($path_to_file, str_replace($findcode, $rmbchatcode, file_get_contents($path_to_file)));

        //because we cannot get the ID from a field we will add the code to make the magic
        $findcode = '<?include(PLUGINDIR . DS . "live_chat" . DS . "views" . DS . "default" . DS . "client_live_chat_include.pdt");?>';
        $rmbchatcode = '';
        $path_to_file = VIEWDIR . "client" . DS . "default" . DS . "structure.pdt";
        $rmchat = file_put_contents($path_to_file, str_replace($findcode, $rmbchatcode, file_get_contents($path_to_file)));

        // remove nav cache
        array_map('unlink', glob(CACHEDIR . "1" . DS . "nav" . DS . "*.html"));

        // remove BLC settings file
        unlink(PLUGINDIR . DS . "live_chat" . DS . "vendors" . DS . "blc" . DS . "settings" . DS . "settings.ini.php");
		}
	}


	/**
	 * Returns all actions to be configured for this widget (invoked after install() or upgrade(), overwrites all existing actions)
	 *
	 * @return array A numerically indexed array containing:
	 * 	- action The action to register for
	 * 	- uri The URI to be invoked for the given action
	 * 	- name The name to represent the action (can be language definition)
	 * 	- options An array of key/value pair options for the given action
	 */
	public function getActions() {
		return array(
			// Staff Nav
			array(
				'action' => "nav_primary_staff",
				'uri' => "../plugins/live_chat/vendors/blc/index.php/site_admin/",
				'name' => Language::_("LiveChatPlugin.name", true),
			),

		);
	}
	

}
?>