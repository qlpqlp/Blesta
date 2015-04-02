<?php
/**
 * Support Manager Admin Tickets controller
 *
 * @package blesta
 * @subpackage blesta.plugins.support_managerpro
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class ClientTicketsCount extends SupportManagerproController {

	public function index() {
	        $this->requireLogin();
	  		Language::loadLang("support_managerpro_plugin", null, PLUGINDIR . "support_managerpro" . DS . "language" . DS);

            $this->client_id = $this->Session->read("blesta_client_id");
            $this->uses(array("SupportManagerpro.SupportManagerproTickets"));

        	// Set the number of tickets of each type
    		$status_count = array(
	  	    	'open' => $this->SupportManagerproTickets->getStatusCount("awaiting_reply", null, $this->client_id)
            );

    $this->set("status_count", $status_count);

	}
}
?>