<?php
/**
 * SupportManagerproTickets manage
 *
 * @package blesta
 * @subpackage blesta.plugins.support_managerpro
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
		Configure::load("support_managerpro", PLUGINDIR . "support_managerpro" . DS . "config" . DS);

  		Language::loadLang("support_managerpro_manage_plugin", null, PLUGINDIR . "support_managerpro" . DS . "language" . DS);

		// Set the page title
		$this->parent->structure->set("page_title", Language::_("SupportManagerproManagePlugin." . Loader::fromCamelCase($this->action ? $this->action : "index") . ".page_title", true));

		// Set the view to render for all actions under this controller
		$this->view->setView(null, "SupportManagerpro.default");
	}

	/**
	 * Returns the view to be rendered when managing this plugin
	 */
	public function index() {
		$this->init();

		if (!empty($this->post)){
		// Load config
		Configure::load("support_managerpro", PLUGINDIR . "support_managerpro" . DS . "config" . DS);

        //define SupportManagerpro.auto_refresh
        $findcode = '("SupportManagerpro.auto_refresh", "'.Configure::get("SupportManagerpro.auto_refresh").'")';
        $replacecode = '("SupportManagerpro.auto_refresh", "'.$this->post['SupportManagerpro_auto_refresh'].'")';
        $path_to_file = PLUGINDIR . "support_managerpro" . DS . "config" . DS . "support_managerpro.php";
        $rcode = file_put_contents($path_to_file, str_replace($findcode, $replacecode, file_get_contents($path_to_file)));

        //define SupportManagerpro.time_format
        $findcode = '("SupportManagerpro.time_format", "'.Configure::get("SupportManagerpro.time_format").'")';
        $replacecode = '("SupportManagerpro.time_format", "'.$this->post['SupportManagerpro_time_format'].'")';
        $path_to_file = PLUGINDIR . "support_managerpro" . DS . "config" . DS . "support_managerpro.php";
        $rcode = file_put_contents($path_to_file, str_replace($findcode, $replacecode, file_get_contents($path_to_file)));

        //define SupportManagerpro.reply_date_format
        $findcode = '("SupportManagerpro.reply_date_format", "'.Configure::get("SupportManagerpro.reply_date_format").'")';
        $replacecode = '("SupportManagerpro.reply_date_format", "'.$this->post['SupportManagerpro_reply_date_format'].'")';
        $path_to_file = PLUGINDIR . "support_managerpro" . DS . "config" . DS . "support_managerpro.php";
        $rcode = file_put_contents($path_to_file, str_replace($findcode, $replacecode, file_get_contents($path_to_file)));

        //define SupportManagerpro.summary_truncate_length
        $findcode = '("SupportManagerpro.summary_truncate_length", "'.Configure::get("SupportManagerpro.summary_truncate_length").'")';
        $replacecode = '("SupportManagerpro.summary_truncate_length", "'.$this->post['SupportManagerpro_summary_truncate_length'].'")';
        $path_to_file = PLUGINDIR . "support_managerpro" . DS . "config" . DS . "support_managerpro.php";
        $rcode = file_put_contents($path_to_file, str_replace($findcode, $replacecode, file_get_contents($path_to_file)));

        //define SupportManagerpro.summary_default
        $findcode = '("SupportManagerpro.summary_default", "'.Configure::get("SupportManagerpro.summary_default").'")';
        $replacecode = '("SupportManagerpro.summary_default", "'.$this->post['SupportManagerpro_summary_default'].'")';
        $path_to_file = PLUGINDIR . "support_managerpro" . DS . "config" . DS . "support_managerpro.php";
        $rcode = file_put_contents($path_to_file, str_replace($findcode, $replacecode, file_get_contents($path_to_file)));

        //define SupportManagerpro.ticket_code_length
        $findcode = '("SupportManagerpro.ticket_code_length", "'.Configure::get("SupportManagerpro.ticket_code_length").'")';
        $replacecode = '("SupportManagerpro.ticket_code_length", "'.$this->post['SupportManagerpro_ticket_code_length'].'")';
        $path_to_file = PLUGINDIR . "support_managerpro" . DS . "config" . DS . "support_managerpro.php";
        $rcode = file_put_contents($path_to_file, str_replace($findcode, $replacecode, file_get_contents($path_to_file)));


		$vars = (object)array(
			'SupportManagerpro_auto_refresh' => $this->post['SupportManagerpro_auto_refresh'],
			'SupportManagerpro_time_format' => $this->post['SupportManagerpro_time_format'],
			'SupportManagerpro_reply_date_format' => $this->post['SupportManagerpro_reply_date_format'],
			'SupportManagerpro_summary_truncate_length' => $this->post['SupportManagerpro_summary_truncate_length'],
			'SupportManagerpro_summary_default' => $this->post['SupportManagerpro_summary_default'],
			'SupportManagerpro_ticket_code_length' => $this->post['SupportManagerpro_ticket_code_length']
		);

        }else{
        $vars = (object)array(
	    	'SupportManagerpro_auto_refresh' => Configure::get("SupportManagerpro.auto_refresh"),
			'SupportManagerpro_time_format' => Configure::get("SupportManagerpro.time_format"),
			'SupportManagerpro_reply_date_format' => Configure::get("SupportManagerpro.reply_date_format"),
			'SupportManagerpro_summary_truncate_length' => Configure::get("SupportManagerpro.summary_truncate_length"),
			'SupportManagerpro_summary_default' => Configure::get("SupportManagerpro.summary_default"),
			'SupportManagerpro_ticket_code_length' => Configure::get("SupportManagerpro.ticket_code_length"),
		);
        }


		// Set the view to render
		return $this->partial("admin_manage_plugin", compact("vars"));
	}

}
?>