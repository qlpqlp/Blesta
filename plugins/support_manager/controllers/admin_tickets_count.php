<?php
/**
 * Live Chat plugin controller
 *
 * @package blesta
 * @subpackage blesta.plugins.live_chat
 * @copyright Copyright (c) 1998, Infoscan - Informática, Lda.
 * @link http://www.weblx.net/ WebLX
 */
class AdminTicketsCount extends SupportManagerController {

	public function index() {
	        $this->requireLogin();
	  		Language::loadLang("support_manager_plugin", null, PLUGINDIR . "support_manager" . DS . "language" . DS);

            $this->staff_id = $this->Session->read("blesta_staff_id");
            $this->uses(array("SupportManager.SupportManagerTickets"));

        	// Set the number of tickets of each type
    		$status_count = array(
	  	    	'open' => $this->SupportManagerTickets->getStatusCount("open", $this->staff_id)
            );

    $this->set("status_count", $status_count);

	}
}
?>