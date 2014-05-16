<?php
/**
 * Support Managerpro ticket pipe controller
 * 
 * @package blesta
 * @subpackage blesta.plugins.support_managerpro
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class TicketPipe extends SupportManagerproController {
	
	/**
	 * Accepts email via STDIN and generates a ticket
	 */
	public function index() {
		if (!$this->is_cli)
			$this->redirect($this->base_uri);

		// Set company ID from path
		if (isset($this->get[0])) {
			$this->company_id = $this->get[0];
			Configure::set("Blesta.company_id", $this->company_id);
		}
		
		Loader::load(PLUGINDIR . "support_managerpro" . DS . "vendors" . DS . "mime_mail_parser" . DS . "MimeMailParser.class.php");
		$this->components(array("SupportManagerpro.TicketManagerpro"));
		
		$email = new MimeMailParser();
		$email->setStream(STDIN);
		
		$options = array(
			'is_cli' => $this->is_cli,
			'client_uri' => $this->client_uri,
			'admin_uri' => $this->admin_uri
		);
		$this->TicketManagerpro->setOptions($options);
		$this->TicketManagerpro->processTicketFromEmail($email);
		return false;
	}
}
?>