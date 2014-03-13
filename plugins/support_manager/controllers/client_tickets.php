<?php
/**
 * Support Manager Client Tickets controller
 * 
 * @package blesta
 * @subpackage blesta.plugins.support_manager
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class ClientTickets extends SupportManagerController {
	
	/**
	 * Setup
	 */
	public function preAction() {
		parent::preAction();
		
		// Do not require login for open ticket page or getPriorities
		if ($this->action != "add" && $this->action != "departments" && $this->action != "getpriorities")
			$this->requireLogin();
		
		$this->uses(array("SupportManager.SupportManagerTickets", "SupportManager.SupportManagerDepartments"));
		
		// Restore structure view location of the client portal
		$this->structure->setDefaultView(APPDIR);
		$this->structure->setView(null, $this->orig_structure_view);
		
		$this->client_id = $this->Session->read("blesta_client_id");
	}
	
	/**
	 * View tickets
	 */
	public function index() {
		$status = (isset($this->get[0]) ? $this->get[0] : "not_closed");
		$page = (isset($this->get[1]) ? (int)$this->get[1] : 1);
		$sort = (isset($this->get['sort']) ? $this->get['sort'] : "last_reply_date");
		$order = (isset($this->get['order']) ? $this->get['order'] : "desc");
		
		$this->set("status", $status);
		$this->set("sort", $sort);
		$this->set("order", $order);
		$this->set("negate_order", ($order == "asc" ? "desc" : "asc"));
		
		// Set the number of clients of each type
		$status_count = array(
			'open' => $this->SupportManagerTickets->getStatusCount("not_closed", null, $this->client_id),
			'closed' => $this->SupportManagerTickets->getStatusCount("closed", null, $this->client_id)
		);
		
		$tickets = $this->SupportManagerTickets->getList($status, null, $this->client_id, $page, array($sort => $order), false);
		$total_results = $this->SupportManagerTickets->getListCount($status, null, $this->client_id);
		
		// Set pagination parameters, set group if available
		$params = array('sort'=>$sort,'order'=>$order);
		
		// Overwrite default pagination settings
		$settings = array_merge(Configure::get("Blesta.pagination"), array(
				'total_results' => $total_results,
				'uri'=>$this->base_uri . "plugin/support_manager/client_tickets/index/" . $status . "/[p]/",
				'params'=>$params
			)
		);
		$this->helpers(array("Pagination"=>array($this->get, $settings)));
		$this->Pagination->setSettings(Configure::get("Blesta.pagination_ajax"));
		
		// Set the last reply time
		foreach ($tickets as &$ticket)
			$ticket->last_reply_time = $this->timeSince($ticket->last_reply_date);
		
		$this->set("tickets", $tickets);
		$this->set("status_count", $status_count);
		$this->set("priorities", $this->SupportManagerTickets->getPriorities());
		$this->set("statuses", $this->SupportManagerTickets->getStatuses());
		
		// Render the request if ajax
		return $this->renderAjaxWidgetIfAsync(isset($this->get[1]) || isset($this->get['sort']));
	}
	
	/**
	 * 1st step of adding a ticket -- select the department
	 */
	public function departments() {
		// Check whether client is logged in
		$logged_in = false;
		if ($this->isLoggedIn())
			$logged_in = true;
		
		// Get all departments visible to clients
		$departments = $this->SupportManagerDepartments->getAll($this->company_id, "visible", (!$logged_in ? false : null));
		
		// Must be logged in if there are no departments
		if (!$logged_in && empty($departments))
			$this->requireLogin();
		
		// Include the TextParser
		$this->helpers(array("TextParser"));
		
		$this->set("departments", $departments);
	}
	
	/**
	 * 2nd step of adding a ticket -- actually creating it
	 */
	public function add() {
		// Check whether client is logged in
		$logged_in = false;
		if ($this->isLoggedIn())
			$logged_in = true;
		
		// Ensure a valid department was given
		if (!isset($this->get[0]) || !($department = $this->SupportManagerDepartments->get($this->get[0])) ||
			($department->company_id != $this->company_id) || (!$logged_in && $department->clients_only == "1") ||
			$department->status == "hidden")
			$this->redirect($this->base_uri . "plugin/support_manager/client_tickets/departments/");
		
		if (!empty($this->post)) {
			// Set fields
			$data = $this->post;
			$data['status'] = "open";
			$data['type'] = "reply";
			$data['department_id'] = $department->id;
			
			// Refuse impersonations
			unset($data['staff_id'], $data['client_id']);
			
			// Set client iff logged in
			if ($logged_in)
				$data['client_id'] = $this->client_id;
			
			// Create a transaction
			$this->SupportManagerTickets->begin();
			
			// Create the ticket
			$ticket_id = $this->SupportManagerTickets->add($data, !$logged_in);
			$ticket_errors = $this->SupportManagerTickets->errors();
			$reply_errors = array();
			
			// Create the initial reply
			if (!$ticket_errors) {
				$reply_id = $this->SupportManagerTickets->addReply($ticket_id, $data, $this->files, true);
				$reply_errors = $this->SupportManagerTickets->errors();
			}
			
			$errors = array_merge(($ticket_errors ? $ticket_errors : array()), ($reply_errors ? $reply_errors : array()));
			
			if ($errors) {
				// Error, reset vars
				$this->SupportManagerTickets->rollBack();
				
				$vars = (object)$this->post;
				$this->setMessage("error", $errors, false, null, false);
			}
			else {
				// Success, commit the transaction
				$this->SupportManagerTickets->commit();
				
				// Send the email associated with this ticket
				$this->SupportManagerTickets->sendEmail($reply_id);
				
				$ticket = $this->SupportManagerTickets->get($ticket_id);
				$this->flashMessage("message", Language::_("ClientTickets.!success.ticket_created", true, $ticket->code), null, false);
				$this->redirect($this->base_uri . "plugin/support_manager/client_tickets/");
			}
		}
		
		// Set default department priority
		if (!isset($vars))
			$vars = (object)array('priority' => $department->default_priority);
		
		$please_select = array('' => Language::_("AppController.select.please", true));
		
		$this->set("vars", $vars);
		$this->set("priorities", ($please_select + $this->SupportManagerTickets->getPriorities()));
		$this->set("logged_in", $logged_in);
	}
	
	/**
	 * Reply to a ticket
	 */
	public function reply() {
		// Ensure the ticket was given
		if (!isset($this->get[0]) || !($ticket = $this->SupportManagerTickets->get($this->get[0], true, array("reply", "log"))) ||
			$ticket->client_id != $this->client_id)
			$this->redirect($this->base_uri . "plugin/support_manager/client_tickets/");
		
		$this->uses(array("SupportManager.SupportManagerStaff"));
		
		// Reply to the ticket
		if (!empty($this->post)) {
			$data = $this->post;
			$data['type'] = "reply";
			$data['staff_id'] = null;
			
			// Remove ability to change ticket options
			unset($data['department_id'], $data['summary'], $data['priority'], $data['status'], $data['ticket_staff_id']);
			
			// If the ticket was previously awaiting this client's reply, or it was closed, change it back to open
			switch ($ticket->status) {
				case "closed":
				case "awaiting_reply":
					$data['status'] = "open";
					break;
			}
			
			// Create a transaction
			$this->SupportManagerTickets->begin();
			
			// Add the reply
			$reply_id = $this->SupportManagerTickets->addReply($ticket->id, $data, $this->files);
			
			if (($errors = $this->SupportManagerTickets->errors())) {
				// Error, reset vars
				$this->SupportManagerTickets->rollBack();
				
				$vars = (object)$this->post;
				$this->setMessage("error", $errors, false, null, false);
			}
			else {
				// Success, commit
				$this->SupportManagerTickets->commit();
				
				// Send the email associated with this ticket
				$this->SupportManagerTickets->sendEmail($reply_id);
				
				$this->flashMessage("message", Language::_("ClientTickets.!success.ticket_updated", true, $ticket->code), null, false);
				$this->redirect($this->base_uri . "plugin/support_manager/client_tickets/");
			}
		}
		
		// Load the Text Parser
		$this->helpers(array("TextParser"));
		
		// Set vars if not set
		if (!isset($vars))
			$vars = $ticket;
		
		// Make staff settings available for those staff that have replied to this ticket
		$staff_settings = array();
		if (!empty($ticket->replies)) {
			foreach ($ticket->replies as $reply) {
				if ($reply->staff_id) {
					if (!array_key_exists($reply->staff_id, $staff_settings)) {
						$staff_settings[$reply->staff_id] = $this->SupportManagerStaff->getSettings($reply->staff_id, $this->company_id);
					}
				}
			}
		}
		$this->set("staff_settings", $staff_settings);
		
		$this->set("ticket", $ticket);
		$this->set("vars", $vars);
		$this->set("statuses", $this->SupportManagerTickets->getStatuses());
		$this->set("priorities", $this->SupportManagerTickets->getPriorities());
	}
	
	/**
	 * Closes the given ticket
	 */
	public function close() {
		// Ensure a valid ticket was given
		if (!isset($this->get[0]) || !($ticket = $this->SupportManagerTickets->get($this->get[0])) ||
			$ticket->client_id != $this->client_id)
			$this->redirect($this->base_uri . "plugin/support_manager/client_tickets/");
		
		$this->SupportManagerTickets->close($ticket->id);
		$this->flashMessage("message", Language::_("ClientTickets.!success.ticket_closed", true, $ticket->code), null, false);
		$this->redirect($this->base_uri . "plugin/support_manager/client_tickets/");
	}
	
	/**
	 * AJAX Fetches a list of department priorities and the default priority
	 */
	public function getPriorities() {
		$this->components(array("Json"));
		$please_select = array('' => Language::_("AppController.select.please", true));
		$vars = array(
			'default_priority' => '',
			'priorities' => $please_select
		);
		
		// Return nothing if the department not given
		if (!isset($this->get[0])) {
			$this->outputAsJson($vars);
			return false;
		}
		
		// Ensure a valid department was given
		if (!$this->isAjax() || !($department = $this->SupportManagerDepartments->get($this->get[0])) ||
			$department->company_id != $this->company_id || $department->status != "visible") {
			header($this->server_protocol . " 401 Unauthorized");
			exit();
		}
		
		// Set priorities
		$vars['default_priority'] = $department->default_priority;
		$vars['priorities'] = $please_select + $this->SupportManagerTickets->getPriorities();
		
		$this->components(array("Json"));
		$this->outputAsJson($vars);
		return false;
	}
	
	/**
	 * Streams an attachment to view
	 */
	public function getAttachment() {
		// Ensure a valid attachment was given
		if (!isset($this->get[0]) || !($attachment = $this->SupportManagerTickets->getAttachment($this->get[0])) ||
			$attachment->client_id != $this->client_id)
			exit();
		
		$this->components(array("Download"));
		
		$this->Download->downloadFile($attachment->file_name, $attachment->name);
		return false;
	}
}
?>