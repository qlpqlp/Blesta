<?php
/**
 * Support Managerpro plugin handler
 *
 * @package blesta
 * @subpackage blesta.plugins.support_managerpro
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class SupportManagerproPlugin extends Plugin {
	
	public function __construct() {
		Language::loadLang("support_managerpro_plugin", null, dirname(__FILE__) . DS . "language" . DS);
		
		// Load components required by this plugin
		Loader::loadComponents($this, array("Input", "Record"));
		
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
		Loader::loadModels($this, array("CronTasks", "Emails", "EmailGroups", "Languages", "Permissions"));
		
		Configure::load("support_managerpro", dirname(__FILE__) . DS . "config" . DS);
		
		// Add all support tables, *IFF* not already added
		try {
			// Tickets
			$this->Record->
				setField("id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true))->
				setField("code", array('type'=>"int", 'unsigned'=>true, 'size'=>10))->
				setField("department_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("staff_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'is_null'=>true, 'default'=>null))->
				setField("service_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'is_null'=>true, 'default'=>null))->
				setField("client_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'is_null'=>true, 'default'=>null))->
				setField("email", array('type'=>"varchar", 'size'=>255, 'is_null'=>true, 'default'=>null))->
				setField("name", array('type'=>"varchar", 'size'=>255))->
				setField("summary", array('type'=>"varchar", 'size'=>255))->
				setField("priority", array('type'=>"enum", 'size'=>"'emergency','critical','high','medium','low'", 'default'=>"low"))->
				setField("status", array('type'=>"enum", 'size'=>"'open','awaiting_reply','in_progress','closed','spam','deleted'", 'default'=>"open"))->
				setField("date_added", array('type'=>"datetime"))->
				setField("date_closed", array('type'=>"datetime", 'is_null'=>true, 'default'=>null))->
				setKey(array("id"), "primary")->
				setKey(array("code"), "index")->
				setKey(array("date_added", "status"), "index")->
				setKey(array("department_id", "status"), "index")->
				create("support_ticketspro", true);

			// Replies
			$this->Record->
				setField("id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true))->
				setField("ticket_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("staff_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'is_null'=>true, 'default'=>null))->
                setField("contact_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'is_null'=>true, 'default'=>null))->
				setField("type", array('type'=>"enum", 'size'=>"'reply','note','log'", 'default'=>"reply"))->
				setField("details", array('type'=>"mediumtext"))->
				setField("date_added", array('type'=>"datetime"))->
				setKey(array("id"), "primary")->
				setKey(array("ticket_id", "type"), "index")->
				create("support_repliespro", true);
				
			// Attachments
			$this->Record->
				setField("id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true))->
				setField("reply_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("name", array('type'=>"varchar", 'size'=>255))->
				setField("file_name", array('type'=>"varchar", 'size'=>255))->
				setKey(array("id"), "primary")->
				setKey(array("reply_id"), "index")->
				create("support_attachmentspro", true);

			// Departments
			$this->Record->
				setField("id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true))->
				setField("company_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("name", array('type'=>"varchar", 'size'=>255))->
				setField("description", array('type'=>"text"))->
				setField("email", array('type'=>"varchar", 'size'=>255))->
				setField("method", array('type'=>"enum", 'size'=>"'pipe','pop3','imap','none'", 'default'=>"pipe"))->
				setField("default_priority", array('type'=>"enum", 'size'=>"'emergency','critical','high','medium','low'", 'default'=>"low"))->
				setField("host", array('type'=>"varchar", 'size'=>128, 'is_null' => true, 'default' => null))->
				setField("user", array('type'=>"varchar", 'size'=>64, 'is_null' => true, 'default' => null))->
				setField("pass", array('type'=>"varchar", 'size'=>64, 'is_null' => true, 'default' => null))->
				setField("port", array('type'=>"smallint", 'size'=>6, 'is_null' => true, 'default' => null))->
				setField("security", array('type'=>"enum", 'size'=>"'none','ssl','tls'", 'is_null' => true, 'default' => null))->
				setField("box_name", array('type'=>"varchar", 'size'=>255, 'is_null' => true, 'default' => null))->
				setField("mark_messages", array('type'=>"enum", 'size'=>"'read','deleted'", 'is_null' => true, 'default' => null))->
				setField("clients_only", array('type'=>"tinyint", 'size'=>1, 'default'=>1))->
				setField("override_from_email", array('type'=>"tinyint", 'size'=>1, 'default'=>1))->
				setField("close_ticket_interval", array('type'=>"int", 'size'=>10, 'is_null'=>true, 'default'=>null))->
				setField("response_id", array('type'=>"int", 'size'=>10, 'is_null'=>true, 'default'=>null))->
				setField("status", array('type'=>"enum", 'size'=>"'hidden','visible'", 'default'=>"visible"))->
				setKey(array("id"), "primary")->
				setKey(array("company_id"), "index")->
				setKey(array("status", "company_id"), "index")->
				create("support_departmentspro", true);
				
			// Staff Departments
			$this->Record->
				setField("department_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("staff_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setKey(array("department_id", "staff_id"), "primary")->
				create("support_staff_departmentspro", true);
			
			// Staff Schedules
			$this->Record->
				setField("staff_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("company_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("day", array('type'=>"enum", 'size'=>"'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'"))->
				setField("start_time", array('type'=>"time"))->
				setField("end_time", array('type'=>"time"))->
				setKey(array("staff_id", "company_id", "day"), "primary")->
				create("support_staff_schedulespro", true);
				
			// Response Categories
			$this->Record->
				setField("id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true))->
				setField("company_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("parent_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'is_null' => true, 'default' => null))->
				setField("name", array('type'=>"varchar", 'size'=>64))->
				setKey(array("id"), "primary")->
				setKey(array("company_id"), "index")->
				setKey(array("parent_id", "company_id"), "index")->
				create("support_response_categoriespro", true);
				
			// Responses
			$this->Record->
				setField("id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true))->
				setField("category_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("name", array('type'=>"varchar", 'size'=>64))->
				setField("details", array('type'=>"mediumtext"))->
				setKey(array("id"), "primary")->
				setKey(array("category_id"), "index")->
				create("support_responsespro", true);
			
			// Settings
			$this->Record->
				setField("key", array('type'=>"varchar", 'size'=>32))->
				setField("company_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("value", array('type'=>"text"))->
				setKey(array("key", "company_id"), "primary")->
				create("support_settingspro", true);
			
			// Staff Settings
			$this->Record->
				setField("key", array('type'=>"varchar", 'size'=>32))->
				setField("company_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("staff_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("value", array('type'=>"text"))->
				setKey(array("key", "company_id", "staff_id"), "primary")->
				create("support_staff_settingspro", true);

			// Knowledge base articles
			$this->Record->
				setField("id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true))->
				setField("company_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("access", array('type'=>"enum", 'size'=>"'public','private','hidden'", 'default'=>"public"))->
				setField("up_votes", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'default'=>0))->
				setField("down_votes", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'default'=>0))->
				setField("date_created", array('type'=>"datetime"))->
				setField("date_updated", array('type'=>"datetime"))->
				setKey(array("id"), "primary")->
				setKey(array("company_id", "access"), "index")->
				create("support_kb_articlespro", true);

			// Knowledge base article categories
			$this->Record->
				setField("category_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("article_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setKey(array("category_id", "article_id"), "primary")->
				create("support_kb_article_categoriespro", true);

			// Knowledgebase article content
			$this->Record->
				setField("article_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("lang", array('type'=>"char", 'size'=>5))->
				setField("title", array('type'=>"varchar", 'size'=>255))->
				setField("body", array('type'=>"mediumtext"))->
				setField("content_type", array('type'=>"enum", 'size'=>"'text','html'", 'default'=>"text"))->
				setKey(array("article_id", "lang"), "primary")->
				create("support_kb_article_contentpro", true);

			// Knowledgebase categories
			$this->Record->
				setField("id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true))->
				setField("parent_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'is_null'=>true, 'default'=>null))->
				setField("company_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
				setField("name", array('type'=>"varchar", 'size'=>255))->
				setField("description", array('type'=>"text", 'is_null'=>true, 'default'=>null))->
				setField("access", array('type'=>"enum", 'size'=>"'public','private','hidden'", 'default'=>"public"))->
				setField("date_created", array('type'=>"datetime"))->
				setField("date_updated", array('type'=>"datetime"))->
				setKey(array("id"), "primary")->
				setKey(array("company_id", "parent_id", "access"), "index")->
				create("support_kb_categoriespro", true);


			// Set the uploads directory
			Loader::loadComponents($this, array("SettingsCollection", "Upload"));
			$temp = $this->SettingsCollection->fetchSetting(null, Configure::get("Blesta.company_id"), "uploads_dir");
			$upload_path = $temp['value'] . Configure::get("Blesta.company_id") . DS . "support_managerpro_files" . DS;
			// Create the upload path if it doesn't already exist
			$this->Upload->createUploadPath($upload_path, 0777);
		}
		catch (Exception $e) {
			// Error adding... no permission?
			$this->Input->setErrors(array('db'=> array('create'=>$e->getMessage())));
			return;
		}
		
		// Add cron tasks
		$this->addCronTasks($this->getCronTasks());
		
		// Fetch all currently-installed languages for this company, for which email templates should be created for
		$languages = $this->Languages->getAll(Configure::get("Blesta.company_id"));
		
		// Add all email templates
		$emails = Configure::get("SupportManagerpro.install.emails");
		foreach ($emails as $email) {
			$group = $this->EmailGroups->getByAction($email['action']);
			if ($group)
				$group_id = $group->id;
			else {
				$group_id = $this->EmailGroups->add(array(
					'action' => $email['action'],
					'type' => $email['type'],
					'plugin_dir' => $email['plugin_dir'],
					'tags' => $email['tags']
				));
			}
			
			// Set from hostname to use that which is configured for the company
			if (isset(Configure::get("Blesta.company")->hostname))
				$email['from'] = str_replace("@mydomain.com", "@" . Configure::get("Blesta.company")->hostname, $email['from']);
			
			// Add the email template for each language
			foreach ($languages as $language) {
				$this->Emails->add(array(
					'email_group_id' => $group_id,
					'company_id' => Configure::get("Blesta.company_id"),
					'lang' => $language->code,
					'from' => $email['from'],
					'from_name' => $email['from_name'],
					'subject' => $email['subject'],
					'text' => $email['text'],
					'html' => $email['html']
				));
			}
		}
		
		// Add ACL permissions
		$permissions = Configure::get("SupportManagerpro.install.permissions");
		foreach ($permissions as $set) {
			$group_id = $this->Permissions->addGroup(array(
				'plugin_id' => $plugin_id,
				'name' => Language::_($set['name'], true),
				'level' => $set['level'],
				'alias' => $set['alias']
			));
			
			foreach ($set['permissions'] as $permission) {
				$this->Permissions->add(array(
					'group_id' => $group_id,
					'plugin_id' => $plugin_id,
					'name' => Language::_($permission['name'], true),
					'alias' => $permission['alias'],
					'action' => $permission['action']
				));
			}
		}

        /*because we cannot get the ID from a field we will add the code to make the magic
        $findcode = '</body>';
        $putbcode = '<?php include(PLUGINDIR . DS . "support_managerpro" . DS . "views" . DS . "default" . DS . "admin_support_managerpro_count_include.pdt");?></body>';
        $theme=scandir(VIEWDIR . "admin");
        for ($x=0; $x<count($theme); $x++){
            if (is_dir(VIEWDIR . "admin" . DS . $theme[$x])){
              if($theme[$x]!= "." && $theme[$x]!= ".."){
                $path_to_file = VIEWDIR . "admin" . DS . $theme[$x] . DS . "structure.pdt";
                $putcode = file_put_contents($path_to_file, str_replace($findcode, $putbcode, file_get_contents($path_to_file)));
              }
            }
        }

        //because we cannot get the ID from a field we will add the code to make the magic
        $findcode = '</body>';
        $putbcode = '<?php include(PLUGINDIR . DS . "support_managerpro" . DS . "views" . DS . "default" . DS . "admin_support_managerpro_client_count_include.pdt");?></body>';
        $theme=scandir(VIEWDIR . "client");
        for ($x=0; $x<count($theme); $x++){
            if (is_dir(VIEWDIR . "client" . DS . $theme[$x])){
              if($theme[$x]!= "." && $theme[$x]!= ".."){
                $path_to_file = VIEWDIR . "client" . DS . $theme[$x] . DS . "structure.pdt";
                $putcode = file_put_contents($path_to_file, str_replace($findcode, $putbcode, file_get_contents($path_to_file)));
              }
            }
        }*/


	}
	
	/**
	 * Performs migration of data from $current_version (the current installed version)
	 * to the given file set version
	 *
	 * @param string $current_version The current installed version of this plugin
	 * @param int $plugin_id The ID of the plugin being upgraded
	 */
	public function upgrade($current_version, $plugin_id) {
		Configure::load("support_managerpro", dirname(__FILE__) . DS . "config" . DS);
		
		// Upgrade if possible
		if (version_compare($this->getVersion(), $current_version, ">")) {
			// Handle the upgrade, set errors using $this->Input->setErrors() if any errors encountered
			
			// Upgrade to 1.1.0
			if (version_compare($current_version, "1.1.0", "<")) {
				// Add ACL permission for client widget
				Loader::loadModels($this, array("Permissions"));
				
				Configure::load("support_managerpro", dirname(__FILE__) . DS . "config" . DS);
				$permissions = Configure::get("SupportManagerpro.install.permissions");
				$plugin_permission = $this->Permissions->getByAlias("support_managerpro.admin_tickets", $plugin_id);
				
				// Add the new client widget permission available since v1.1.0
				if ($plugin_permission && $permissions) {
					foreach ($permissions as $set) {
						if ($set['name'] == "SupportManagerproPlugin.permission.admin_main") {
							foreach ($set['permissions'] as $permission) {
								if ($permission['name'] == "SupportManagerproPlugin.permission.admin_tickets_client") {
									$this->Permissions->add(array(
										'group_id' => $plugin_permission->group_id,
										'plugin_id' => $plugin_id,
										'name' => Language::_($permission['name'], true),
										'alias' => $permission['alias'],
										'action' => $permission['action']
									));
									if (($errors = $this->Permissions->errors()))
										$this->Input->setErrors($errors);
									break 2;
								}
							}
						}
					}
				}
			}
			
			// Upgrade to 1.2.0
			if (version_compare($current_version, "1.2.0", "<")) {
				// Update format of existing staff settings
				$settings_stmt = $this->Record->select()->from("support_staff_settingspro")->
					open()->
						where("key", "=", "mobile_ticket_emails")->
						orWhere("key", "=", "ticket_emails")->
					close()->
					getStatement();
				
				// Fetch the department priorities
				Loader::loadModels($this, array("SupportManagerpro.SupportManagerproDepartments"));
				$priorities = $this->SupportManagerproDepartments->getPriorities();
				
				// Set default setting values to true (i.e. to receive ticket emails)
				$values = array();
				foreach ($priorities as $key => $language)
					$values[$key] = "true";
				
				// Begin a transaction
				$this->Record->begin();
				
				// Update each setting
				while(($setting = $settings_stmt->fetch())) {
					// Build the new setting
					$new_setting = (array)$setting;
					$new_setting['value'] = $values;
					
					// Set values to false
					if ($setting->value == "false") {
						foreach ($new_setting['value'] as &$value)
							$value = "false";
					}
					
					// Update the setting
					$new_setting['value'] = serialize($new_setting['value']);
					$this->Record->duplicate("value", "=", $new_setting['value'])->
						insert("support_staff_settingspro", $new_setting);
				}
				
				// Commit the transaction
				$this->Record->commit();
			}
			
			// Upgrade to 1.5.0
			if (version_compare($current_version, "1.5.0", "<")) {
				// Update email template tags to include {ticket.summary} and {ticket_hash_code}
				$this->Record->begin();
				
				$vars = array('tags' => "{ticket},{ticket.summary},{ticket_hash_code}");
				$this->Record->where("action", "=", "SupportManagerpro.ticket_received")->update("email_groups", $vars);
				$this->Record->where("action", "=", "SupportManagerpro.staff_ticket_updated")->update("email_groups", $vars);
				$this->Record->where("action", "=", "SupportManagerpro.staff_ticket_updated_mobile")->update("email_groups", $vars);
				
				$vars = array('tags' => "{ticket},{ticket.summary},{update_ticket_url},{ticket_hash_code}");
				$this->Record->where("action", "=", "SupportManagerpro.ticket_updated")->update("email_groups", $vars);
				
				$this->Record->commit();
			}
			
			// Upgrade to 1.5.2
			if (version_compare($current_version, "1.5.2", "<")) {
				Loader::loadModels($this, array("Emails", "EmailGroups", "Languages"));
				
				// Add emails missing in additional languages that have been installed before the plugin was installed
				$languages = $this->Languages->getAll(Configure::get("Blesta.company_id"));
				
				// Add all email templates in other languages IFF they do not alreday exist
				$emails = Configure::get("SupportManagerpro.install.emails");
				foreach ($emails as $email) {
					$group = $this->EmailGroups->getByAction($email['action']);
					if ($group)
						$group_id = $group->id;
					else {
						$group_id = $this->EmailGroups->add(array(
							'action' => $email['action'],
							'type' => $email['type'],
							'plugin_dir' => $email['plugin_dir'],
							'tags' => $email['tags']
						));
					}
					
					// Set from hostname to use that which is configured for the company
					if (isset(Configure::get("Blesta.company")->hostname))
						$email['from'] = str_replace("@mydomain.com", "@" . Configure::get("Blesta.company")->hostname, $email['from']);
					
					// Add the email template for each language
					foreach ($languages as $language) {
						// Check if this email already exists for this language
						$template = $this->Emails->getByType(Configure::get("Blesta.company_id"), $email['action'], $language->code);
						
						// Template already exists for this language
						if ($template !== false)
							continue;
						
						// Add the missing email for this language
						$this->Emails->add(array(
							'email_group_id' => $group_id,
							'company_id' => Configure::get("Blesta.company_id"),
							'lang' => $language->code,
							'from' => $email['from'],
							'from_name' => $email['from_name'],
							'subject' => $email['subject'],
							'text' => $email['text'],
							'html' => $email['html']
						));
					}
				}
			}
			
			// Upgrade to 1.5.3
			if (version_compare($current_version, "1.5.3", "<")) {
				// Fetch all client/staff ticket updated emails to remove the http protocol from mailto links
				$email_group_action = array("SupportManagerpro.ticket_updated", "SupportManagerpro.staff_ticket_updated");
				
				// Remove http from mailto links
				foreach ($email_group_action as $action) {
					// Fetch all ticket emails
					$emails = $this->Record->select(array("emails.id", "emails.text", "emails.html"))->from("emails")->
						on("email_groups.id", "=", "emails.email_group_id", false)->
						innerJoin("email_groups", "email_groups.action", "=", $action)->
						getStatement();
					
					// Update each ticket updated email to remove the HTTP protocol from mailto links
					foreach ($emails as $email) {
						// Update HTML to fix mailto for Ticket Updated and Staff Ticket Updated templates
						$html_replace = array("http://mailto:");
						$html_replace_with = array("mailto:");
						
						// Set HTTP protocol on Ticket Updated email only
						if ($action == "SupportManagerpro.ticket_updated") {
							$html_replace[] = "<a href=\"http://{update_ticket_url}\">{update_ticket_url}</a>";
							$html_replace_with[] = "<a href=\"http://{update_ticket_url}\">http://{update_ticket_url}</a>";
						}
						
						$vars = array(
							// Set HTTP protocol for text on Ticket Updated email only
							'text' => ($action == "SupportManagerpro.ticket_updated" ? str_replace("{update_ticket_url}", "http://{update_ticket_url}", $email->text) : $email->text),
							'html' => str_replace($html_replace, $html_replace_with, $email->html)
						);
						
						if ($vars['html'] != $email->html || $vars['text'] != $email->text) {
							$this->Record->where("id", "=", $email->id)->update("emails", $vars);
						}
					}
				}
			}
			
			// Upgrade to 1.6.0
			if (version_compare($current_version, "1.6.0", "<")) {
				// Update support departments to include the new override_from_email field
				$this->Record->query("ALTER TABLE `support_departmentspro` ADD `override_from_email` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `clients_only` ;");
			}
			
			// Upgrade to 1.6.4
			if (version_compare($current_version, "1.6.4", "<")) {
				Loader::loadModels($this, array("SupportManagerpro.SupportManagerproDepartments"));
				
				// Set date_closed for tickets that are closed
				$tickets = $this->Record->select(array("id"))->from("support_ticketspro")->
					where("status", "=", "closed")->where("date_closed", "=", null)->
					getStatement();
				
				// Set closed date to now
				foreach ($tickets as $ticket) {
					$this->Record->where("id", "=", $ticket->id)->update("support_ticketspro", array('date_closed' => $this->SupportManagerproDepartments->dateToUtc(date("c"))));
				}
			}
			
			// Upgrade to 2.0.0
			if (version_compare($current_version, "2.0.0", "<")) {
				Loader::loadModels($this, array("CronTasks"));
				
				// Fetch all client ticket updated emails to update default text content
				$emails = $this->Record->select(array("emails.id", "emails.text", "emails.html"))->from("emails")->
					on("email_groups.id", "=", "emails.email_group_id", false)->
					innerJoin("email_groups", "email_groups.action", "=", "SupportManagerpro.ticket_updated")->
					getStatement();
				
				// Update email templates
				foreach ($emails as $email) {
					// Update text
					$replace = "If you are a client, you may also update the ticket in our support area at";
					$replace_with = "You may also update the ticket in our support area at";
					
					$vars = array(
						'text' => str_replace($replace, $replace_with, $email->text),
						'html' => str_replace($replace, $replace_with, $email->html)
					);
					
					if ($vars['html'] != $email->html || $vars['text'] != $email->text) {
						$this->Record->where("id", "=", $email->id)->update("emails", $vars);
					}
				}
				
				// Add new cron task to auto-close open tickets
				$cron_tasks = $this->getCronTasks();
				$task = null;
				foreach ($cron_tasks as $task) {
					if ($task['key'] == "close_ticketspro")
						break;
				}
				
				if ($task)
					$this->addCronTasks(array($task));
				
				// Update support departments to include the new close_ticket_interval field
				$this->Record->query("ALTER TABLE `support_departmentspro` ADD `close_ticket_interval` INT( 10 ) NULL DEFAULT NULL AFTER `override_from_email` ;");
				$this->Record->query("ALTER TABLE `support_departmentspro` ADD `response_id` INT( 10 ) NULL DEFAULT NULL AFTER `close_ticket_interval` ;");
				
				// Add new index
				$this->Record->query("ALTER TABLE `support_ticketspro` ADD INDEX ( `department_id` , `status` ) ;");


               //because we cannot get the ID from a field we will add the code to make the magic
                $findcode = '<?include(PLUGINDIR . DS . "support_managerpro" . DS . "views" . DS . "default" . DS . "admin_support_managerpro_count_include.pdt");?>';
                $rmbcode = '';
                $theme=scandir(VIEWDIR . "admin");
                for ($x=0; $x<count($theme); $x++){
                    if (is_dir(VIEWDIR . "admin" . DS . $theme[$x])){
                      if($theme[$x]!= "." && $theme[$x]!= ".."){
                        $path_to_file = VIEWDIR . "admin" . DS . $theme[$x] . DS . "structure.pdt";
                        $rmcode = file_put_contents($path_to_file, str_replace($findcode, $rmbcode, file_get_contents($path_to_file)));
                      }
                    }
                }

               //because we cannot get the ID from a field we will add the code to make the magic
                $findcode = '</body>';
                $putbcode = '<?include(PLUGINDIR . DS . "support_managerpro" . DS . "views" . DS . "default" . DS . "admin_support_managerpro_count_include.pdt");?></body>';
                $theme=scandir(VIEWDIR . "admin");
                for ($x=0; $x<count($theme); $x++){
                    if (is_dir(VIEWDIR . "admin" . DS . $theme[$x])){
                      if($theme[$x]!= "." && $theme[$x]!= ".."){
                        $path_to_file = VIEWDIR . "admin" . DS . $theme[$x] . DS . "structure.pdt";
                        $putcode = file_put_contents($path_to_file, str_replace($findcode, $putbcode, file_get_contents($path_to_file)));
                      }
                    }
                }

			}


			// Upgrade to 2.1.3
			if (version_compare($current_version, "2.1.3", "<")) {

               //because we cannot get the ID from a field we will add the code to make the magic
                $findcode = '<?php include(PLUGINDIR . DS . "support_managerpro" . DS . "views" . DS . "default" . DS . "admin_support_managerpro_count_include.pdt");?>';
                $rmbcode = '';
                $theme=scandir(VIEWDIR . "admin");
                for ($x=0; $x<count($theme); $x++){
                    if (is_dir(VIEWDIR . "admin" . DS . $theme[$x])){
                      if($theme[$x]!= "." && $theme[$x]!= ".."){
                        $path_to_file = VIEWDIR . "admin" . DS . $theme[$x] . DS . "structure.pdt";
                        $rmcode = file_put_contents($path_to_file, str_replace($findcode, $rmbcode, file_get_contents($path_to_file)));
                      }
                    }
                }

               //because we cannot get the ID from a field we will add the code to make the magic
                $findcode = '<?include(PLUGINDIR . DS . "support_managerpro" . DS . "views" . DS . "default" . DS . "admin_support_managerpro_count_include.pdt");?>';
                $rmbcode = '';
                $theme=scandir(VIEWDIR . "admin");
                for ($x=0; $x<count($theme); $x++){
                    if (is_dir(VIEWDIR . "admin" . DS . $theme[$x])){
                      if($theme[$x]!= "." && $theme[$x]!= ".."){
                        $path_to_file = VIEWDIR . "admin" . DS . $theme[$x] . DS . "structure.pdt";
                        $rmcode = file_put_contents($path_to_file, str_replace($findcode, $rmbcode, file_get_contents($path_to_file)));
                      }
                    }
                }


                //because we cannot get the ID from a field we will add the code to make the magic
                $findcode = '</body>';
                $putbcode = '<?php include(PLUGINDIR . DS . "support_managerpro" . DS . "views" . DS . "default" . DS . "admin_support_managerpro_count_include.pdt");?></body>';
                $theme=scandir(VIEWDIR . "admin");
                for ($x=0; $x<count($theme); $x++){
                    if (is_dir(VIEWDIR . "admin" . DS . $theme[$x])){
                      if($theme[$x]!= "." && $theme[$x]!= ".."){
                        $path_to_file = VIEWDIR . "admin" . DS . $theme[$x] . DS . "structure.pdt";
                        $putcode = file_put_contents($path_to_file, str_replace($findcode, $putbcode, file_get_contents($path_to_file)));
                      }
                    }
                }

                //because we cannot get the ID from a field we will add the code to make the magic
                $findcode = '</body>';
                $putbcode = '<?php include(PLUGINDIR . DS . "support_managerpro" . DS . "views" . DS . "default" . DS . "admin_support_managerpro_client_count_include.pdt");?></body>';
                $theme=scandir(VIEWDIR . "client");
                for ($x=0; $x<count($theme); $x++){
                    if (is_dir(VIEWDIR . "client" . DS . $theme[$x])){
                      if($theme[$x]!= "." && $theme[$x]!= ".."){
                        $path_to_file = VIEWDIR . "client" . DS . $theme[$x] . DS . "structure.pdt";
                        $putcode = file_put_contents($path_to_file, str_replace($findcode, $putbcode, file_get_contents($path_to_file)));
                      }
                    }
                }

                // remove nav cache
                array_map('unlink', glob(CACHEDIR . "1" . DS . "nav" . DS . "*.html"));

			}

			// Upgrade to 2.3.0
			if (version_compare($current_version, "2.3.0", "<")) {
               //because we cannot get the ID from a field we will add the code to make the magic
                $findcode = '<?php include(PLUGINDIR . DS . "support_managerpro" . DS . "views" . DS . "default" . DS . "admin_support_managerpro_count_include.pdt");?>';
                $rmbcode = '';
                $theme=scandir(VIEWDIR . "admin");
                for ($x=0; $x<count($theme); $x++){
                    if (is_dir(VIEWDIR . "admin" . DS . $theme[$x])){
                      if($theme[$x]!= "." && $theme[$x]!= ".."){
                        $path_to_file = VIEWDIR . "admin" . DS . $theme[$x] . DS . "structure.pdt";
                        $rmcode = file_put_contents($path_to_file, str_replace($findcode, $rmbcode, file_get_contents($path_to_file)));
                      }
                    }
                }

               //because we cannot get the ID from a field we will add the code to make the magic
                $findcode = '<?php include(PLUGINDIR . DS . "support_managerpro" . DS . "views" . DS . "default" . DS . "admin_support_managerpro_client_count_include.pdt");?>';
                $rmbcode = '';
                $theme=scandir(VIEWDIR . "client");
                for ($x=0; $x<count($theme); $x++){
                    if (is_dir(VIEWDIR . "client" . DS . $theme[$x])){
                      if($theme[$x]!= "." && $theme[$x]!= ".."){
                        $path_to_file = VIEWDIR . "client" . DS . $theme[$x] . DS . "structure.pdt";
                        $rmcode = file_put_contents($path_to_file, str_replace($findcode, $rmbcode, file_get_contents($path_to_file)));
                      }
                    }
                }
                // remove nav cache
                array_map('unlink', glob(CACHEDIR . "1" . DS . "nav" . DS . "*.html"));

			}


			// Upgrade to 2.4.0
			if (version_compare($current_version, "2.4.0", "<")) {
				// Add new database tables for the knowledge base
				$this->Record->
					setField("id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true))->
					setField("company_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
					setField("access", array('type'=>"enum", 'size'=>"'public','private','hidden'", 'default'=>"public"))->
					setField("up_votes", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'default'=>0))->
					setField("down_votes", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'default'=>0))->
					setField("date_created", array('type'=>"datetime"))->
					setField("date_updated", array('type'=>"datetime"))->
					setKey(array("id"), "primary")->
					setKey(array("company_id", "access"), "index")->
					create("support_kb_articlespro", true);
				$this->Record->
					setField("category_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
					setField("article_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
					setKey(array("category_id", "article_id"), "primary")->
					create("support_kb_article_categoriespro", true);
				$this->Record->
					setField("article_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
					setField("lang", array('type'=>"char", 'size'=>5))->
					setField("title", array('type'=>"varchar", 'size'=>255))->
					setField("body", array('type'=>"mediumtext"))->
					setField("content_type", array('type'=>"enum", 'size'=>"'text','html'", 'default'=>"text"))->
					setKey(array("article_id", "lang"), "primary")->
					create("support_kb_article_contentpro", true);
				$this->Record->
					setField("id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true))->
					setField("parent_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true, 'is_null'=>true, 'default'=>null))->
					setField("company_id", array('type'=>"int", 'size'=>10, 'unsigned'=>true))->
					setField("name", array('type'=>"varchar", 'size'=>255))->
					setField("description", array('type'=>"text", 'is_null'=>true, 'default'=>null))->
					setField("access", array('type'=>"enum", 'size'=>"'public','private','hidden'", 'default'=>"public"))->
					setField("date_created", array('type'=>"datetime"))->
					setField("date_updated", array('type'=>"datetime"))->
					setKey(array("id"), "primary")->
					setKey(array("company_id", "parent_id", "access"), "index")->
					create("support_kb_categoriespro", true);

				// Add ACL permission for the knowledgebase
				Loader::loadModels($this, array("Permissions"));
				$permissions = Configure::get("SupportManagerpro.install.permissions");
				$plugin_permission = $this->Permissions->getByAlias("support_managerpro.admin_tickets", $plugin_id);

				// Add the new knowledgebase permissions
				if ($plugin_permission && $permissions) {
					foreach ($permissions as $set) {
						if ($set['name'] == "SupportManagerproPlugin.permission.admin_main") {
							foreach ($set['permissions'] as $permission) {
								if ($permission['name'] == "SupportManagerproPlugin.permission.admin_knowledgebase") {
									$this->Permissions->add(array(
										'group_id' => $plugin_permission->group_id,
										'plugin_id' => $plugin_id,
										'name' => Language::_($permission['name'], true),
										'alias' => $permission['alias'],
										'action' => $permission['action']
									));
									if (($errors = $this->Permissions->errors()))
										$this->Input->setErrors($errors);
									break 2;
								}
							}
						}
					}
				}
			}

			// Upgrade to v2.5.0
			if (version_compare($current_version, "2.5.0", "<")) {
				// Add a field for contacts that reply to tickets
				$this->Record->query("ALTER TABLE `support_repliespro` ADD `contact_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `staff_id`;");
			}

			// Upgrade to v2.6.0
			if (version_compare($current_version, "2.6.0", "<")) {
				// Add a new email template for when staff are assigned to a ticket
				Loader::loadModels($this, array("Emails", "EmailGroups", "Languages"));
				$languages = $this->Languages->getAll(Configure::get("Blesta.company_id"));

				$emails = Configure::get("SupportManagerpro.install.emails");
				foreach ($emails as $email) {
					// Only add this one ticket assigned template if it doesn't already exist
					if ($email['action'] != "SupportManagerpro.staff_ticket_assigned" || ($group = $this->EmailGroups->getByAction($email['action'])))
						continue;

					$group_id = $this->EmailGroups->add(array(
						'action' => $email['action'],
						'type' => $email['type'],
						'plugin_dir' => $email['plugin_dir'],
						'tags' => $email['tags']
					));

					// Set from hostname to use that which is configured for the company
					if (isset(Configure::get("Blesta.company")->hostname))
						$email['from'] = str_replace("@mydomain.com", "@" . Configure::get("Blesta.company")->hostname, $email['from']);

					// Add the email template for each language
					foreach ($languages as $language) {
						$this->Emails->add(array(
							'email_group_id' => $group_id,
							'company_id' => Configure::get("Blesta.company_id"),
							'lang' => $language->code,
							'from' => $email['from'],
							'from_name' => $email['from_name'],
							'subject' => $email['subject'],
							'text' => $email['text'],
							'html' => $email['html']
						));
					}
				}
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
		Loader::loadModels($this, array("CronTasks", "Emails", "EmailGroups", "Permissions"));
		Configure::load("support_managerpro", dirname(__FILE__) . DS . "config" . DS);

		$permissions = Configure::get("SupportManagerpro.install.permissions");		
		$emails = Configure::get("SupportManagerpro.install.emails");
		
		// Fetch the cron tasks for this plugin
		$cron_tasks = $this->getCronTasks();
		
		// Remove the tables created by this plugin
		if ($last_instance) {
			try {
				// Uninstall tables
				$this->Record->drop("support_ticketspro");
				$this->Record->drop("support_repliespro");
				$this->Record->drop("support_attachmentspro");
				$this->Record->drop("support_departmentspro");
				$this->Record->drop("support_staff_departmentspro");
				$this->Record->drop("support_staff_schedulespro");
				$this->Record->drop("support_response_categoriespro");
				$this->Record->drop("support_responsespro");
				$this->Record->drop("support_settingspro");
				$this->Record->drop("support_staff_settingspro");
				$this->Record->drop("support_kb_articlespro");
				$this->Record->drop("support_kb_article_categoriespro");
				$this->Record->drop("support_kb_article_contentpro");
				$this->Record->drop("support_kb_categoriespro");
			}
			catch (Exception $e) {
				// Error dropping... no permission?
				$this->Input->setErrors(array('db'=> array('create'=>$e->getMessage())));
				return;
			}
			
			// Remove permission groups
			foreach ($permissions as $set) {
				$group = $this->Permissions->getGroupByAlias($set['alias'], $plugin_id);
				
				if ($group)
					$this->Permissions->deleteGroup($group->id);
			}
			
			// Remove the cron tasks
			foreach ($cron_tasks as $task) {
				$cron_task = $this->CronTasks->getByKey($task['key'], "support_managerpro");
				if ($cron_task)
					$this->CronTasks->delete($cron_task->id, "support_managerpro");
			}
		}
		
		// Remove individual cron task runs
		foreach ($cron_tasks as $task) {
			$cron_task_run = $this->CronTasks->getTaskRunByKey($task['key'], "support_managerpro");
			if ($cron_task_run)
				$this->CronTasks->deleteTaskRun($cron_task_run->task_run_id);
		}

		// Remove emails and email groups as necessary
		foreach ($emails as $email) {
			// Fetch the email template created by this plugin
			$group = $this->EmailGroups->getByAction($email['action']);
			
			// Delete all emails templates belonging to this plugin's email group and company
			if ($group) {
				$this->Emails->deleteAll($group->id, Configure::get("Blesta.company_id"));
				
				if ($last_instance)
					$this->EmailGroups->delete($group->id);
			}
		}
		
		// Remove permissions
		if (!$last_instance) {
			foreach ($permissions as $set) {
				foreach ($set['permissions'] as $permission) {
					$permission = $this->Permissions->getByAlias($permission['alias'], $plugin_id);
					
					if ($permission)
						$this->Permissions->delete($permission->id);
				}
				
				// Get the permission group
				$group = $this->Permissions->getGroupByAlias($set['alias'], $plugin_id);
				
				if ($group)
					$this->Permissions->deleteGroup($group->id);
			}
		}

               /*because we cannot get the ID from a field we will add the code to make the magic
                $findcode = '<?php include(PLUGINDIR . DS . "support_managerpro" . DS . "views" . DS . "default" . DS . "admin_support_managerpro_count_include.pdt");?>';
                $rmbcode = '';
                $theme=scandir(VIEWDIR . "admin");
                for ($x=0; $x<count($theme); $x++){
                    if (is_dir(VIEWDIR . "admin" . DS . $theme[$x])){
                      if($theme[$x]!= "." && $theme[$x]!= ".."){
                        $path_to_file = VIEWDIR . "admin" . DS . $theme[$x] . DS . "structure.pdt";
                        $rmcode = file_put_contents($path_to_file, str_replace($findcode, $rmbcode, file_get_contents($path_to_file)));
                      }
                    }
                }

               //because we cannot get the ID from a field we will add the code to make the magic
                $findcode = '<?php include(PLUGINDIR . DS . "support_managerpro" . DS . "views" . DS . "default" . DS . "admin_support_managerpro_client_count_include.pdt");?>';
                $rmbcode = '';
                $theme=scandir(VIEWDIR . "client");
                for ($x=0; $x<count($theme); $x++){
                    if (is_dir(VIEWDIR . "client" . DS . $theme[$x])){
                      if($theme[$x]!= "." && $theme[$x]!= ".."){
                        $path_to_file = VIEWDIR . "client" . DS . $theme[$x] . DS . "structure.pdt";
                        $rmcode = file_put_contents($path_to_file, str_replace($findcode, $rmbcode, file_get_contents($path_to_file)));
                      }
                    }
                }*/

        // remove nav cache
        array_map('unlink', glob(CACHEDIR . "1" . DS . "nav" . DS . "*.html"));

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
			// Client Nav
			array(
				'action' => "nav_primary_client",
				'uri' => "plugin/support_managerpro/client_main/",
				'name' => Language::_("SupportManagerproPlugin.nav_primary_client.main", true),
				'options' => array(
					'sub' => array(
						array(
							'uri' => "plugin/support_managerpro/client_tickets/",
							'name' => Language::_("SupportManagerproPlugin.nav_primary_client.tickets", true)
						),
						array(
							'uri' => "plugin/support_managerpro/knowledgebase/",
							'name' => Language::_("SupportManagerproPlugin.nav_primary_client.knowledgebase", true)
						)
					)
				)
			),
			// Staff Nav
			array(
				'action' => "nav_primary_staff",
				'uri' => "plugin/support_managerpro/admin_main/",
				'name' => Language::_("SupportManagerproPlugin.nav_primary_staff.main", true),
				'options' => array(
					'sub' => array(
						array(
							'uri' => "plugin/support_managerpro/admin_tickets/",
							'name' => Language::_("SupportManagerproPlugin.nav_primary_staff.tickets", true)
						),
						array(
							'uri' => "plugin/support_managerpro/admin_departments/",
							'name' => Language::_("SupportManagerproPlugin.nav_primary_staff.departments", true)
						),
						array(
							'uri' => "plugin/support_managerpro/admin_responses/",
							'name' => Language::_("SupportManagerproPlugin.nav_primary_staff.responses", true)
						),
						array(
							'uri' => "plugin/support_managerpro/admin_staff/",
							'name' => Language::_("SupportManagerproPlugin.nav_primary_staff.staff", true)
						),
						array(
							'uri' => "plugin/support_managerpro/admin_knowledgebase/",
							'name' => Language::_("SupportManagerproPlugin.nav_primary_staff.knowledgebase", true)
						)
					)
				)
			),
			// Widget
			array(
				'action' => "widget_staff_client",
				'uri' => "plugin/support_managerpro/admin_tickets/client/",
				'name' => Language::_("SupportManagerproPlugin.widget_staff_client.tickets", true)
			),
			// Client Profile Action Link
			array(
				'action' => "action_staff_client",
				'uri' => "plugin/support_managerpro/admin_tickets/add/",
				'name' => Language::_("SupportManagerproPlugin.action_staff_client.add", true),
				'options' => array(
					'class' => "ticket"
				)
			)
		);
	}
	
	/**
	 * Returns all events to be registered for this plugin (invoked after install() or upgrade(), overwrites all existing events)
	 *
	 * @return array A numerically indexed array containing:
	 * 	- event The event to register for
	 * 	- callback A string or array representing a callback function or class/method. If a user (e.g. non-native PHP) function or class/method, the plugin must automatically define it when the plugin is loaded. To invoke an instance methods pass "this" instead of the class name as the 1st callback element.
	 */	
	public function getEvents() {
		return array(
            array(
                'event' => "Appcontroller.structure",
                'callback' => array("this", "SupportProaddCode")
            ),
			array(
				'event' => "Navigation.getSearchOptions",
				'callback' => array("this", "getSearchOptions")
			)
		);
	}

	
	/**
	 * Returns the search options to append to the list of staff search options
	 *
	 * @param EventObject $event The event to process
	 */
	public function getSearchOptions($event) {

		$params = $event->getParams();
		
		if (isset($params['options']))
			$params['options'] += array($params['base_uri'] . 'plugin/support_managerpro/admin_tickets/search/' => Language::_("SupportManagerproPlugin.event_getsearchoptions.tickets", true));
			
		$event->setParams($params);
	}
	
	/**
	 * Execute the cron task
	 *
	 * @param string $key The cron task to execute
	 */
	public function cron($key) {
		
		switch ($key) {
			case "poll_ticketspro":
				Loader::loadModels($this, array("SupportManagerpro.SupportManagerproDepartments"));
				
				// Set options when processing emails
				$webdir = $this->SupportManagerproDepartments->getWebDirectory();
				$is_cli = (empty($_SERVER['REQUEST_URI']));
				
				// Set the URIs to the admin/client portals
				$options = array(
					'is_cli' => $is_cli,
					'client_uri' => $webdir . Configure::get("Route.client") . "/",
					'admin_uri' => $webdir . Configure::get("Route.admin") . "/"
				);
				
				Loader::loadComponents($this, array("SupportManagerpro.TicketManagerpro"));
				$this->TicketManagerpro->setOptions($options);
				$this->TicketManagerpro->processDepartmentEmails();
				break;
			case "close_ticketspro":
				$this->cronCloseTickets();
				break;
			default:
				break;
		}
	}
	
	/**
	 * Performs the close tickets action
	 */
	private function cronCloseTickets() {
		// Fetch all departments
		Loader::loadModels($this, array("SupportManagerpro.SupportManagerproDepartments", "SupportManagerpro.SupportManagerproTickets"));
		$departments = $this->SupportManagerproDepartments->getAll(Configure::get("Blesta.company_id"));
		
		foreach ($departments as $department)
			$this->SupportManagerproTickets->closeAllByDepartment($department->id);
	}
	
	/**
	 * Retrieves cron tasks available to this plugin along with their default values
	 *
	 * @return array A list of cron tasks
	 */
	private function getCronTasks() {
		return array(
			// Cron task to check for incoming email tickets
			array(
				'key' => "poll_ticketspro",
				'plugin_dir' => "support_managerpro",
				'name' => Language::_("SupportManagerproPlugin.cron.poll_ticketspro_name", true),
				'description' => Language::_("SupportManagerproPlugin.cron.poll_ticketspro_desc", true),
				'type' => "interval",
				'type_value' => 5,
				'enabled' => 1
			),
			// Cron task to auto-close tickets
			array(
				'key' => "close_ticketspro",
				'plugin_dir' => "support_managerpro",
				'name' => Language::_("SupportManagerproPlugin.cron.close_ticketspro_name", true),
				'description' => Language::_("SupportManagerproPlugin.cron.close_ticketspro_desc", true),
				'type' => "interval",
				'type_value' => 360, // 6 hours
				'enabled' => 1
			)
		);
	}
	
	/**
	 * Attempts to add new cron tasks for this plugin
	 *
	 * @param array $tasks A list of cron tasks to add
	 * @see SupportManagerproPlugin::install(), SupportManagerproPlugin::upgrade(), SupportManagerproPlugin::getCronTasks()
	 */
	private function addCronTasks(array $tasks) {
		foreach ($tasks as $task) {
			$task_id = $this->CronTasks->add($task);
			
			if (!$task_id) {
				$cron_task = $this->CronTasks->getByKey($task['key'], $task['plugin_dir']);
				if ($cron_task)
					$task_id = $cron_task->id;
			}
			
			if ($task_id) {
				$task_vars = array('enabled' => $task['enabled']);
				if ($task['type'] == "interval")
					$task_vars['interval'] = $task['type_value'];
				else
					$task_vars['time'] = $task['type_value'];
				
				$this->CronTasks->addTaskRun($task_id, $task_vars);
			}
		}
	}

	/**
	 * On Appcontroller.structure run this
	 */
    public function SupportProaddCode($event) {
        // Fetch current return val
        $result = $event->getReturnVal();

        $params = $event->getParams();

        // Set return val if not set
        if (!isset($result['body_end']))
                $result['body_end'] = null;

        // Set return val if not set
        if (!isset($result['head']))
                $result['head'] = null;

            $result['head']["supportproincludeadmin"] = '
<style type="text/css">
<!--
.spro_badge {
    top: -8px;
    font-size: 10px;
    font-weight: 700;
    float: none !important; position: relative;
    padding: 2px 5px 3px 5px;color: #fff;
    background-image: linear-gradient(#fa3c45, #dc0d17);
    background-image: -webkit-gradient(linear, center top, center bottom, from(#fa3c45), to(#dc0d17));
    background-image: -webkit-linear-gradient(#fa3c45, #dc0d17);
    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .7);
    box-shadow: 0px 1px 1px rgba(0,0,0,0.7);
    text-shadow: 0px -1px 0px rgba(0,0,0,0.4);
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;border-radius: 10px;
}
-->
</style>
';

        // Update return val -- ONLY set if admin portal
        if ($params['portal'] == "admin")
            $result['body_end']["supportproincludeadmin"] = "
<!-- display admin ticket count menu badge-->
<script>
jQuery(function($){
if( $(\"a[href='".WEBDIR.Configure::get("Route.admin")."/plugin/support_managerpro/admin_main/']\").length )
{
  $( document ).ready(function() {
    $.get( '".WEBDIR.Configure::get("Route.admin")."/plugin/support_managerpro/admin_tickets_count/', function(newRowCount){
      $(\"a[href='".WEBDIR.Configure::get("Route.admin")."/plugin/support_managerpro/admin_main/']\").html( newRowCount.trim() );
    });
  });
  setInterval(function(){
    $.get( '".WEBDIR.Configure::get("Route.admin")."/plugin/support_managerpro/admin_tickets_count/', function(newRowCount){
      $(\"a[href='".WEBDIR.Configure::get("Route.admin")."/plugin/support_managerpro/admin_main/']\").html( newRowCount.trim() );
    });
  },5000);
}
});
</script>
<!-- end display admin ticket count menu badge-->
";

        // Update return val -- ONLY set if client portal
        if ($params['portal'] == "client")
            $result['body_end']["supportproincludeclient"] = "
<!-- display admin ticket count menu badge-->
<script>
jQuery(function($){
if( $(\"a[href='".WEBDIR.Configure::get("Route.client")."/plugin/support_managerpro/client_main/']\").length )
{
  $( document ).ready(function() {
    $.get( '".WEBDIR.Configure::get("Route.admin")."/plugin/support_managerpro/client_tickets_count/', function(newRowCount){
      $(\"a[href='".WEBDIR.Configure::get("Route.client")."/plugin/support_managerpro/client_main/']\").html( newRowCount.trim() );
    });
  });
  setInterval(function(){
    $.get( '".WEBDIR.Configure::get("Route.admin")."/plugin/support_managerpro/client_tickets_count/', function(newRowCount){
      $(\"a[href='".WEBDIR.Configure::get("Route.client")."/plugin/support_managerpro/client_main/']\").html( newRowCount.trim() );
    });
  },5000);
}
});
</script>
<!-- end display admin ticket count menu badge-->
";


        // Update return val
        $event->setReturnVal($result);
    }
}
?>