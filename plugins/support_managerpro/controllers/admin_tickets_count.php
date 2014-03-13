<?php
/**
 * Live Chat plugin controller
 *
 * @package blesta
 * @subpackage blesta.plugins.live_chat
 * @copyright Copyright (c) 1998, Infoscan - Informática, Lda.
 * @link http://www.weblx.net/ WebLX
 */
class AdminTicketsCount extends SupportManagerproController {

	public function index() {
	        $this->requireLogin();
	  		Language::loadLang("support_managerpro_plugin", null, PLUGINDIR . "support_managerpro" . DS . "language" . DS);

            $this->staff_id = $this->Session->read("blesta_staff_id");
            $this->uses(array("SupportManagerpro.SupportManagerproTickets"));

        	// Set the number of tickets of each type
    		$status_count = array(
	  	    	'open' => $this->SupportManagerproTickets->getStatusCount("open", $this->staff_id)
            );

    $this->set("status_count", $status_count);

	}
}
?>