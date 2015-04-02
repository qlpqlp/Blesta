<?php
/**
 * Support Managerpro Client Tickets controller
 * 
 * @package blesta
 * @subpackage blesta.plugins.support_managerpro
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class ClientTickets extends SupportManagerproController {

	/**
	 * Setup
	 */
	public function preAction() {
		parent::preAction();
		
		// Do not require login for the following methods
		if ($this->action != "add" && $this->action != "reply" && $this->action != "close" &&
			$this->action != "departments" && $this->action != "getpriorities" && $this->action != "getattachment")
			$this->requireLogin();
		
		$this->uses(array("SupportManagerpro.SupportManagerproTickets", "SupportManagerpro.SupportManagerproDepartments"));
		
		// Restore structure view location of the client portal
		$this->structure->setDefaultView(APPDIR);
		$this->structure->setView(null, $this->orig_structure_view);
		
		$this->client_id = $this->Session->read("blesta_client_id");

		// Fetch contact that is logged in, if any
		if (!isset($this->Contacts))
			$this->uses(array("Contacts"));
		$this->contact = $this->Contacts->getByUserId($this->Session->read("blesta_id"), $this->client_id);


		$this->set("string", $this->DataStructure->create("string"));
	}
	
	/**
	 * Builds a hash mapping default support ticket priorities to class names
	 *
	 * @return array A key/value array of priority => class name
	 */
	private function getPriorityClasses() {
		return array('low' => "success", 'medium' => "medium", 'high' => "warning", 'critical' => "danger", 'emergency' => "emergency");
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
			'open' => $this->SupportManagerproTickets->getStatusCount("not_closed", null, $this->client_id),
			'closed' => $this->SupportManagerproTickets->getStatusCount("closed", null, $this->client_id)
		);
		
		$tickets = $this->SupportManagerproTickets->getList($status, null, $this->client_id, $page, array($sort => $order), false);
		$total_results = $this->SupportManagerproTickets->getListCount($status, null, $this->client_id);
		
		// Overwrite default pagination settings
		$settings = array_merge(Configure::get("Blesta.pagination_client"), array(
				'total_results' => $total_results,
				'uri'=>$this->base_uri . "plugin/support_managerpro/client_tickets/index/" . $status . "/[p]/",
				'params'=>array('sort'=>$sort,'order'=>$order)
			)
		);
		$this->helpers(array("Pagination"=>array($this->get, $settings)));
		$this->Pagination->setSettings(Configure::get("Blesta.pagination_ajax"));
		
		// Set the last reply time
		foreach ($tickets as &$ticket)
			$ticket->last_reply_time = $this->timeSince($ticket->last_reply_date);
		
		$this->set("tickets", $tickets);
		$this->set("status_count", $status_count);
		$this->set("priorities", $this->SupportManagerproTickets->getPriorities());
		$this->set("statuses", $this->SupportManagerproTickets->getStatuses());
		$this->set("priority_classes", $this->getPriorityClasses());
		
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
		$departments = $this->SupportManagerproDepartments->getAll($this->company_id, "visible", (!$logged_in ? false : null));
		
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
		if (!isset($this->get[0]) || !($department = $this->SupportManagerproDepartments->get($this->get[0])) ||
			($department->company_id != $this->company_id) || (!$logged_in && $department->clients_only == "1") ||
			$department->status == "hidden")
			$this->redirect($this->base_uri . "plugin/support_managerpro/client_tickets/departments/");
		
		if (!empty($this->post)) {
			// Set fields
			$data = $this->post;
			$data['status'] = "open";
			$data['type'] = "reply";
			$data['department_id'] = $department->id;
			
			// Refuse impersonations
			unset($data['staff_id'], $data['client_id']);

			// Set client iff logged in
			if ($logged_in) {
				$data['client_id'] = $this->client_id;

				// Set contact that is replying
				if ($this->contact)
					$data['contact_id'] = $this->contact->id;
			}

			// Create a transaction
			$this->SupportManagerproTickets->begin();
			
			// Create the ticket
			$ticket_id = $this->SupportManagerproTickets->add($data, !$logged_in);
			$ticket_errors = $this->SupportManagerproTickets->errors();
			$reply_errors = array();
			
			// Create the initial reply
			if (!$ticket_errors) {
				$reply_id = $this->SupportManagerproTickets->addReply($ticket_id, $data, $this->files, true);
				$reply_errors = $this->SupportManagerproTickets->errors();
			}
			
			$errors = array_merge(($ticket_errors ? $ticket_errors : array()), ($reply_errors ? $reply_errors : array()));
			
			if ($errors) {
				// Error, reset vars
				$this->SupportManagerproTickets->rollBack();
				
				$vars = (object)$this->post;
				$this->setMessage("error", $errors, false, null, false);
			}
			else {
				// Success, commit the transaction
				$this->SupportManagerproTickets->commit();
				
				// Send the email associated with this ticket
				$this->SupportManagerproTickets->sendEmail($reply_id);

				$ticket = $this->SupportManagerproTickets->get($ticket_id);
				$this->flashMessage("message", Language::_("ClientTickets.!success.ticket_created", true, $ticket->code), null, false);
				$redirect_url = $this->base_uri . "plugin/support_managerpro/client_tickets/";
				if (!$logged_in)
					$redirect_url .= "departments/";
				$this->redirect($redirect_url);
			}
		}

		// Set default department priority
		if (!isset($vars))
			$vars = (object)array('priority' => $department->default_priority);
		
		$please_select = array('' => Language::_("AppController.select.please", true));
		
		$this->set("vars", $vars);
		$this->set("priorities", ($please_select + $this->SupportManagerproTickets->getPriorities()));
		$this->set("logged_in", $logged_in);
	}
	
	/**
	 * Checks whether access can be granted to a client, whether logged-in or not
	 *
	 * @param int $ticket_id The ID of the ticket
	 * @param string $redirect_to The URL to redirect the user to on failure (optional, default null redirect to the client listing)
	 * @return array A set of key/value pairs including:
	 * 	- allow_reply_by_url boolean true if access is granted for non-logged-in clients, false otherwise
	 * 	- ticket mixed An stdClass object representing the ticket, or false if one does not exist
	 * 	- sid mixed The hash code, if any
	 */
	private function requireAccess($ticket_id, $redirect_to = null) {
		// Fetch the ticket
		$redirect = false;
		$allow_reply_by_url = false;
		$ticket = $this->SupportManagerproTickets->get($ticket_id, true, array("reply", "log"));
		$sid = (isset($this->get['sid']) ? $this->get['sid'] : (isset($this->post['sid']) ? $this->post['sid'] : null));
		
		if ($ticket) {
			// Login required for clients and closed tickets
			if ($ticket->client_id !== null) {
				$this->requireLogin();
				
				// Ticket must belong to this client
				if ($ticket->client_id != $this->client_id)
					$redirect = true;
			}
			// Not-logged-in clients either may not reply to this department, or did not provide the required hash
			elseif (!$sid || !($department = $this->SupportManagerproDepartments->get($ticket->department_id)) ||
				($department->company_id != $this->company_id) || ($department->clients_only == "1")) {
				$redirect = true;
			}
			else {
				// Validate hash in URL to allow replies to this ticket without logging in
				$params = array();
				$temp = explode("|", $this->SupportManagerproTickets->systemDecrypt($sid));
				
				if (count($temp) > 1) {
					foreach ($temp as $field) {
						$field = explode("=", $field, 2);
						if (count($field) >= 2)
							$params[$field[0]] = $field[1];
					}
				}
				
				// Confirm whether the hash matches
				if (!isset($params['h']) || !isset($params['k']) ||
					$params['h'] != substr($this->SupportManagerproTickets->generateReplyHash($ticket->id, $params['k']), -16))
					$redirect = true;
				else
					$allow_reply_by_url = true;
			}
		}
		else
			$redirect = true;
		
		// Redirect
		if ($redirect)
			$this->redirect(($redirect_to ? $redirect_to : $this->base_uri . "plugin/support_managerpro/client_tickets/"));
		
		return array(
			'allow_reply_by_url' => $allow_reply_by_url,
			'ticket' => $ticket,
			'sid' => $sid
		);
	}
	
	/**
	 * Reply to a ticket
	 */
	public function reply() {
		// Ensure a valid ticket was given
		$redirect_url = $this->base_uri . "plugin/support_managerpro/client_tickets/";
		if (!isset($this->get[0]))
			$this->redirect($redirect_url);
		
		// Require valid credentials be given
		$access = $this->requireAccess($this->get[0], $redirect_url);
		$ticket = $access['ticket'];
		
		$this->uses(array("SupportManagerpro.SupportManagerproStaff"));
		
		// Reply to the ticket
		if (!empty($this->post)) {
			$data = $this->post;
			$data['type'] = "reply";
			$data['staff_id'] = null;
            $data['contact_id'] = ($this->contact ? $this->contact->id : null);            
			
			// Remove ability to change ticket options
			unset($data['department_id'], $data['summary'], $data['priority'], $data['status'], $data['ticket_staff_id']);
			
			// If the ticket was previously awaiting this client's reply, or it was closed, change it back to open
			switch ($ticket->status) {
				case "closed":
				case "awaiting_reply":
					$data['status'] = "open";
					break;
			}
			
			// Check whether the client is closing the ticket
			$close = false;
			if (!empty($data['action_type']) && $data['action_type'] == "close") {
				$data['status'] = "closed";
				$close = true;
			}
			
			// Create a transaction
			$this->SupportManagerproTickets->begin();
			
			// Add the reply
			$reply_id = $this->SupportManagerproTickets->addReply($ticket->id, $data, $this->files);
			
			if (($errors = $this->SupportManagerproTickets->errors())) {
				// Error, reset vars
				$this->SupportManagerproTickets->rollBack();
				
				// Close the ticket if necessary
				if ($close && ($ticket = $this->SupportManagerproTickets->get($ticket->id)) && $ticket->status != "closed") {
					$this->SupportManagerproTickets->close($ticket->id);
					$this->flashMessage("message", Language::_("ClientTickets.!success.ticket_closed", true, $ticket->code), null, false);
					$this->redirect($this->base_uri . "plugin/support_managerpro/client_tickets/" . ($access['allow_reply_by_url'] ? "reply/" . $ticket->id . "/?sid=" . rawurlencode($access['sid']) : ""));
				}
				
				$vars = (object)$this->post;
				$this->setMessage("error", $errors, false, null, false);
			}
			else {
				// Success, commit
				$this->SupportManagerproTickets->commit();
				
				// Send the email associated with this ticket
				$this->SupportManagerproTickets->sendEmail($reply_id);
				
				// Check whether the ticket was just closed and set the appropriate message
				if ($close && ($ticket = $this->SupportManagerproTickets->get($ticket->id)) && $ticket->status == "closed")
					$this->flashMessage("message", Language::_("ClientTickets.!success.ticket_closed", true, $ticket->code), null, false);
				else
					$this->flashMessage("message", Language::_("ClientTickets.!success.ticket_updated", true, $ticket->code), null, false);
				
				$this->redirect($this->base_uri . "plugin/support_managerpro/client_tickets/" . ($access['allow_reply_by_url'] ? "reply/" . $ticket->id . "/?sid=" . rawurlencode($access['sid']) : ""));
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
						$staff_settings[$reply->staff_id] = $this->SupportManagerproStaff->getSettings($reply->staff_id, $this->company_id);
					}
				}
			}
		}
		$this->set("staff_settings", $staff_settings);
		
		$this->set("ticket", $ticket);
		$this->set("sid", $access['sid']);
		$this->set("vars", $vars);
		$this->set("statuses", $this->SupportManagerproTickets->getStatuses());
		$this->set("priorities", $this->SupportManagerproTickets->getPriorities());
		$this->set("priority_classes", $this->getPriorityClasses());
	}
	
	/**
	 * Closes the given ticket
	 */
	public function close() {
		// Ensure a valid ticket was given
		$redirect_url = $this->base_uri . "plugin/support_managerpro/client_tickets/";
		if (empty($this->post['id']))
			$this->redirect($redirect_url);
		
		// Require valid credentials be given
		$access = $this->requireAccess($this->post['id'], $redirect_url);
		$ticket = $access['ticket'];
		
		// Close ticket if not done already
		if ($ticket && $ticket->status != "closed") {
			$this->SupportManagerproTickets->close($ticket->id);
			$this->flashMessage("message", Language::_("ClientTickets.!success.ticket_closed", true, $ticket->code), null, false);
		}
		$this->redirect($redirect_url . ($access['allow_reply_by_url'] ? "reply/" . $ticket->id . "/?sid=" . rawurlencode($access['sid']) : ""));
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
		if (!$this->isAjax() || !($department = $this->SupportManagerproDepartments->get($this->get[0])) ||
			$department->company_id != $this->company_id || $department->status != "visible") {
			header($this->server_protocol . " 401 Unauthorized");
			exit();
		}
		
		// Set priorities
		$vars['default_priority'] = $department->default_priority;
		$vars['priorities'] = $please_select + $this->SupportManagerproTickets->getPriorities();
		
		$this->components(array("Json"));
		$this->outputAsJson($vars);
		return false;
	}
	
	/**
	 * Streams an attachment to view
	 */
	public function getAttachment() {
		// Ensure a valid attachment was given
		if (!isset($this->get[0]) || !($attachment = $this->SupportManagerproTickets->getAttachment($this->get[0])) ||
			!isset($attachment->ticket_id))
			$this->redirect($this->base_uri . "plugin/support_managerpro/client_tickets/");
		
		// Require valid credentials be given
		$this->requireAccess($attachment->ticket_id, null);
		
		$this->components(array("Download"));
		
		$this->Download->downloadFile($attachment->file_name, $attachment->name);
		return false;
	}
}
?>