<?php
/**
 * Support Manager Client Main controller
 * 
 * @package blesta
 * @subpackage blesta.plugins.support_manager
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class ClientMain extends SupportManagerController {
	
	/**
	 * Redirect to the ClientTickets controller
	 */
	public function index() {
		$this->redirect($this->base_uri . "plugin/support_manager/client_tickets/");
	}
}
?>