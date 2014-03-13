<?php
/**
 * SupportManagerTickets manage
 *
 * @package blesta
 * @subpackage blesta.plugins.support_manager
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */

class AdminManagePlugin extends AppController {
	/**
	 * Performs necessary initialization
	 */
	private function init() {
		// Require login
		$this->parent->requireLogin();

		// Load config
		Configure::load("support_manager", PLUGINDIR . "support_manager" . DS . "config" . DS);

  		Language::loadLang("support_manager_manage_plugin", null, PLUGINDIR . "support_manager" . DS . "language" . DS);

		// Set the page title
		$this->parent->structure->set("page_title", Language::_("SupportManagerManagePlugin." . Loader::fromCamelCase($this->action ? $this->action : "index") . ".page_title", true));

		// Set the view to render for all actions under this controller
		$this->view->setView(null, "SupportManager.default");
	}

	/**
	 * Returns the view to be rendered when managing this plugin
	 */
	public function index() {
		$this->init();

		if (!empty($this->post)){
		// Load config
		Configure::load("support_manager", PLUGINDIR . "support_manager" . DS . "config" . DS);

        //define SupportManager.auto_refresh
        $findcode = '("SupportManager.auto_refresh", "'.Configure::get("SupportManager.auto_refresh").'")';
        $replacecode = '("SupportManager.auto_refresh", "'.$this->post['SupportManager_auto_refresh'].'")';
        $path_to_file = PLUGINDIR . "support_manager" . DS . "config" . DS . "support_manager.php";
        $rcode = file_put_contents($path_to_file, str_replace($findcode, $replacecode, file_get_contents($path_to_file)));

        //define SupportManager.time_format
        $findcode = '("SupportManager.time_format", "'.Configure::get("SupportManager.time_format").'")';
        $replacecode = '("SupportManager.time_format", "'.$this->post['SupportManager_time_format'].'")';
        $path_to_file = PLUGINDIR . "support_manager" . DS . "config" . DS . "support_manager.php";
        $rcode = file_put_contents($path_to_file, str_replace($findcode, $replacecode, file_get_contents($path_to_file)));

        //define SupportManager.reply_date_format
        $findcode = '("SupportManager.reply_date_format", "'.Configure::get("SupportManager.reply_date_format").'")';
        $replacecode = '("SupportManager.reply_date_format", "'.$this->post['SupportManager_reply_date_format'].'")';
        $path_to_file = PLUGINDIR . "support_manager" . DS . "config" . DS . "support_manager.php";
        $rcode = file_put_contents($path_to_file, str_replace($findcode, $replacecode, file_get_contents($path_to_file)));

        //define SupportManager.summary_truncate_length
        $findcode = '("SupportManager.summary_truncate_length", "'.Configure::get("SupportManager.summary_truncate_length").'")';
        $replacecode = '("SupportManager.summary_truncate_length", "'.$this->post['SupportManager_summary_truncate_length'].'")';
        $path_to_file = PLUGINDIR . "support_manager" . DS . "config" . DS . "support_manager.php";
        $rcode = file_put_contents($path_to_file, str_replace($findcode, $replacecode, file_get_contents($path_to_file)));

        //define SupportManager.summary_default
        $findcode = '("SupportManager.summary_default", "'.Configure::get("SupportManager.summary_default").'")';
        $replacecode = '("SupportManager.summary_default", "'.$this->post['SupportManager_summary_default'].'")';
        $path_to_file = PLUGINDIR . "support_manager" . DS . "config" . DS . "support_manager.php";
        $rcode = file_put_contents($path_to_file, str_replace($findcode, $replacecode, file_get_contents($path_to_file)));

        //define SupportManager.ticket_code_length
        $findcode = '("SupportManager.ticket_code_length", "'.Configure::get("SupportManager.ticket_code_length").'")';
        $replacecode = '("SupportManager.ticket_code_length", "'.$this->post['SupportManager_ticket_code_length'].'")';
        $path_to_file = PLUGINDIR . "support_manager" . DS . "config" . DS . "support_manager.php";
        $rcode = file_put_contents($path_to_file, str_replace($findcode, $replacecode, file_get_contents($path_to_file)));


		$vars = (object)array(
			'SupportManager_auto_refresh' => $this->post['SupportManager_auto_refresh'],
			'SupportManager_time_format' => $this->post['SupportManager_time_format'],
			'SupportManager_reply_date_format' => $this->post['SupportManager_reply_date_format'],
			'SupportManager_summary_truncate_length' => $this->post['SupportManager_summary_truncate_length'],
			'SupportManager_summary_default' => $this->post['SupportManager_summary_default'],
			'SupportManager_ticket_code_length' => $this->post['SupportManager_ticket_code_length']
		);

        }else{
        $vars = (object)array(
	    	'SupportManager_auto_refresh' => Configure::get("SupportManager.auto_refresh"),
			'SupportManager_time_format' => Configure::get("SupportManager.time_format"),
			'SupportManager_reply_date_format' => Configure::get("SupportManager.reply_date_format"),
			'SupportManager_summary_truncate_length' => Configure::get("SupportManager.summary_truncate_length"),
			'SupportManager_summary_default' => Configure::get("SupportManager.summary_default"),
			'SupportManager_ticket_code_length' => Configure::get("SupportManager.ticket_code_length"),
		);
        }


		// Set the view to render
		return $this->partial("admin_manage_plugin", compact("vars"));
	}

}
?>