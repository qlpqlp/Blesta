<?php
/**
 * Support Managerpro Admin Tickets controller
 *
 * @package blesta
 * @subpackage blesta.plugins.support_managerpro
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class AdminTickets extends SupportManagerproController {
	
	/**
	 * Setup
	 */
	public function preAction() {
		parent::preAction();

		$this->requireLogin();
		
		$this->uses(array("SupportManagerpro.SupportManagerproStaff", "SupportManagerpro.SupportManagerproTickets"));
		
		// Restore structure view location of the admin portal
		$this->structure->setDefaultView(APPDIR);
		$this->structure->setView(null, $this->orig_structure_view);
		
		$this->staff_id = $this->Session->read("blesta_staff_id");

        $this->set("string", $this->DataStructure->create("string"));
	}
	
	/**
	 * Retrieves a key/value list of actions that can be performed on tickets
	 *
	 * @return array A list of key/value pairs representing valid actions
	 */
	private function getTicketActions() {
		return array(
            'update_status' => Language::_("Global.action.update_status", true),
			'merge' => Language::_("Global.action.merge", true),
            'permanent_delete' => Language::_("Global.action.pdelete", true)
		);
	}
	
	/**
	 * Retrieves a key/value list of actions that can be performed on replies
	 *
	 * @return array A list of key/value pairs representing valid actions
	 */
	private function getReplyActions() {
		return array(
			'quote' => Language::_("Global.action.quote", true),
			'split' => Language::_("Global.action.split", true),
            'permanent_delete' => Language::_("Global.action.pdelete", true)
		);
	}
	
	/**
	 * Sets a message to the view if no staff or departments are set
	 */
	private function setDepartmentStaffNotice() {
		$this->uses(array("SupportManagerpro.SupportManagerproDepartments"));
		
		if ($this->SupportManagerproDepartments->getListCount($this->company_id) == 0 ||
			$this->SupportManagerproStaff->getListCount($this->company_id) == 0) {
			// Set language for the department/staff nav items
			$department = Language::_("SupportManagerproPlugin.nav_primary_staff.departments", true);
			$staff = Language::_("SupportManagerproPlugin.nav_primary_staff.staff", true);
			
			$this->setMessage("notice", Language::_("AdminTickets.!notice.no_departments_staff", true, $department, $staff), false, null, false);
		}
	}
	
	/**
	 * View tickets
	 */
	public function index() {
		$this->uses(array("Clients", "SupportManagerpro.SupportManagerproDepartments"));

		$status = (isset($this->get[0]) ? $this->get[0] : "open");
		$page = (isset($this->get[1]) ? (int)$this->get[1] : 1);
		$sort = (isset($this->get['sort']) ? $this->get['sort'] : "last_reply_date");
		$order = (isset($this->get['order']) ? $this->get['order'] : "desc");
		
		$this->set("status", $status);
		$this->set("sort", $sort);
		$this->set("order", $order);
		$this->set("negate_order", ($order == "asc" ? "desc" : "asc"));
		
		// Set the number of tickets of each type
		$status_count = array(
			'open' => $this->SupportManagerproTickets->getStatusCount("open", $this->staff_id),
			'awaiting_reply' => $this->SupportManagerproTickets->getStatusCount("awaiting_reply", $this->staff_id),
			'in_progress' => $this->SupportManagerproTickets->getStatusCount("in_progress", $this->staff_id),
			'closed' => $this->SupportManagerproTickets->getStatusCount("closed", $this->staff_id),
			'spam' => $this->SupportManagerproTickets->getStatusCount("spam", $this->staff_id),
			'deleted' => $this->SupportManagerproTickets->getStatusCount("deleted", $this->staff_id)
		);
		
		$tickets = $this->SupportManagerproTickets->getList($status, $this->staff_id, null, $page, array($sort => $order), false);
		$total_results = $this->SupportManagerproTickets->getListCount($status, $this->staff_id);
		
		// Set pagination parameters, set group if available
		$params = array('sort'=>$sort,'order'=>$order);
		
		// Overwrite default pagination settings
		$settings = array_merge(Configure::get("Blesta.pagination"), array(
				'total_results' => $total_results,
				'uri'=>$this->base_uri . "plugin/support_managerpro/admin_tickets/index/" . $status . "/[p]/",
				'params'=>$params
			)
		);
		$this->helpers(array("Pagination"=>array($this->get, $settings)));
		$this->Pagination->setSettings(Configure::get("Blesta.pagination_ajax"));

		// Set the time that the ticket was last replied to
		foreach ($tickets as &$ticket){
			$ticket->last_reply_time = $this->timeSince($ticket->last_reply_date);

		if (!empty($ticket->client_id))
            $ticket->client = $this->Clients->get($ticket->client_id, false);
        }

		$this->set("staff_id", $this->staff_id);
		$this->set("tickets", $tickets);
		$this->set("page", $page);
		$this->set("status_count", $status_count);
		$this->set("priorities", $this->SupportManagerproTickets->getPriorities());
		$this->set("ticket_actions", $this->getTicketActions());
        $this->set("ticket_statuses", $this->SupportManagerproTickets->getStatuses());
		// Set the client this ticket belongs to

		// Set a message if staff/departments are not setup
		if (!$this->isAjax()) {
			$this->setDepartmentStaffNotice();
            $this->set("set_ticket_time", true);
		}
		
		// Render the request if ajax
		return $this->renderAjaxWidgetIfAsync(isset($this->get[1]) || isset($this->get['sort']));
	}
	
	/**
	 * View client profile ticket widget
	 */
	public function client() {
		// Ensure a valid client was given
		$this->uses(array("Clients"));
		$client_id = (isset($this->get['client_id']) ? $this->get['client_id'] : (isset($this->get[0]) ? $this->get[0] : null));
		if (empty($client_id) || !($client = $this->Clients->get($client_id))) {
			header($this->server_protocol . " 401 Unauthorized");
			exit();
		}
		
		// Set the number of tickets of each type
		$status_count = array(
			'open' => $this->SupportManagerproTickets->getStatusCount("open", $this->staff_id, $client->id),
			'awaiting_reply' => $this->SupportManagerproTickets->getStatusCount("awaiting_reply", $this->staff_id, $client->id),
			'in_progress' => $this->SupportManagerproTickets->getStatusCount("in_progress", $this->staff_id, $client->id),
			'closed' => $this->SupportManagerproTickets->getStatusCount("closed", $this->staff_id, $client->id)
		);
		
		$status = (isset($this->get[1]) ? $this->get[1] : "open");
		$page = (isset($this->get[2]) ? (int)$this->get[2] : 1);
		$sort = (isset($this->get['sort']) ? $this->get['sort'] : "last_reply_date");
		$order = (isset($this->get['order']) ? $this->get['order'] : "desc");
		
		// Fetch ticktes
		$tickets = $this->SupportManagerproTickets->getList($status, $this->staff_id, $client->id, $page, array($sort => $order));
		
		// Set the time that the ticket was last replied to
		foreach ($tickets as &$ticket)
			$ticket->last_reply_time = $this->timeSince($ticket->last_reply_date);
		
		$this->set("widget_state", isset($this->widgets_state['tickets_client']) ? $this->widgets_state['tickets_client'] : null);
		$this->set("tickets", $tickets);
		$this->set("client", $client);
		$this->set("status", $status);
		$this->set("sort", $sort);
		$this->set("order", $order);
		$this->set("negate_order", ($order == "asc" ? "desc" : "asc"));
		$this->set("staff_id", $this->staff_id);
		$this->set("status_count", $status_count);
		$this->set("priorities", $this->SupportManagerproTickets->getPriorities());
		
		// Overwrite default pagination settings
		$settings = array_merge(Configure::get("Blesta.pagination"), array(
				'total_results' => $this->SupportManagerproTickets->getListCount($status, $this->staff_id, $client->id),
				'uri'=>$this->base_uri . "plugin/support_managerpro/admin_tickets/client/" . $client->id . "/" . $status . "/[p]/",
				'params'=>array('sort'=>$sort,'order'=>$order),
			)
		);
		$this->helpers(array("Pagination"=>array($this->get, $settings)));
		$this->Pagination->setSettings(Configure::get("Blesta.pagination_ajax"));
		
		if ($this->isAjax())
			return $this->renderAjaxWidgetIfAsync(isset($this->get['client_id']) ? null : (isset($this->get[2]) || isset($this->get['sort'])));
	}
	
	/**
	 * Client Ticket count
	 */
	public function clientTicketCount() {
		$client_id = isset($this->get[0]) ? $this->get[0] : null;
		$status = isset($this->get[1]) ? $this->get[1] : "open";
		
		echo $this->SupportManagerproTickets->getStatusCount($status, $this->staff_id, $client_id);
		return false;
	}
	
	/**
	 * Add a ticket
	 */
	public function add() {
		$this->uses(array("Clients", "SupportManagerpro.SupportManagerproDepartments", "SupportManagerpro.SupportManagerproStaff"));

		// Set the client if given
		$client_id = null;
		$client = null;
		if (isset($this->get[0])) {
			$client = $this->Clients->get($this->get[0]);
			$client_id = ($client ? $client->id : $this->get[0]);
		}
		
		$please_select = array('' => Language::_("AppController.select.please", true));
		$department_staff = array('' => Language::_("AdminTickets.text.unassigned", true));
		
		if (!empty($this->post)) {
			$data = $this->post;
			// Set staff ticket is assigned to
			$data['staff_id'] = (isset($data['ticket_staff_id']) ? $data['ticket_staff_id'] : $this->staff_id);
			$data['type'] = "reply";
			
			// Set the client ID if not passed in by POST
			if (!isset($data['client_id']))
				$data['client_id'] = $client_id;
			
			// Create a transaction
			$this->SupportManagerproTickets->begin();
			
			// Open the ticket
			$ticket_id = $this->SupportManagerproTickets->add($data);
			$ticket_errors = $this->SupportManagerproTickets->errors();
			$reply_errors = array();
			
			// Create the initial reply
			if (!$ticket_errors) {
				// Set the staff that replied to this ticket
				$data['staff_id'] = $this->staff_id;
				$reply_id = $this->SupportManagerproTickets->addReply($ticket_id, $data, $this->files, true);
				$reply_errors = $this->SupportManagerproTickets->errors();
			}
			
			$errors = array_merge(($ticket_errors ? $ticket_errors : array()), ($reply_errors ? $reply_errors : array()));
			
			if ($errors) {
				// Error, reset vars
				$this->SupportManagerproTickets->rollBack();
				
				$vars = (object)$this->post;
				$this->setMessage("error", $errors, false, null, false);
				
				// Set the priorities and staff to show
				if (!empty($data['department_id'])) {
					$priorities = $please_select + $this->SupportManagerproTickets->getPriorities();
					$department_staff += $this->Form->collapseObjectArray($this->SupportManagerproStaff->getAll($this->company_id, $data['department_id'], false), array("first_name", "last_name"), "id", " ");
				}
			}
			else {
				// Success
				$this->SupportManagerproTickets->commit();
				
				// Fetch the ticket
				$ticket = $this->SupportManagerproTickets->get($ticket_id);
				
				// Get the company hostname
				$hostname = isset(Configure::get("Blesta.company")->hostname) ? Configure::get("Blesta.company")->hostname : "";
				
				// Send the email associated with this ticket
				$key = mt_rand();
				$hash = $this->SupportManagerproTickets->generateReplyHash($ticket->id, $key);
				$additional_tags = array('SupportManagerpro.ticket_updated' => array('update_ticket_url' => $this->Html->safe($hostname . $this->client_uri . "plugin/support_managerpro/client_tickets/reply/" . $ticket->id . "/?sid=" . rawurlencode($this->SupportManagerproTickets->systemEncrypt('h=' . substr($hash, -16) . "|k=" . $key)))));
				$this->SupportManagerproTickets->sendEmail($reply_id, $additional_tags);

				// Notify staff that a ticket has been assigned to them
				$assign_to_staff_id = (isset($data['ticket_staff_id']) ? $data['ticket_staff_id'] : $this->staff_id);
				if (!empty($assign_to_staff_id) && $this->staff_id != $ticket->staff_id)
					$this->SupportManagerproTickets->sendTicketAssignedEmail($ticket->id);

				$this->flashMessage("message", Language::_("AdminTickets.!success.ticket_created", true, $ticket->code), null, false);
				$this->redirect($this->base_uri . "plugin/support_managerpro/admin_tickets/");
			}
		}
		
		// Set departments, statuses
		$departments = $please_select + $this->Form->collapseObjectArray($this->SupportManagerproDepartments->getAll($this->company_id), "name", "id");
		$statuses = $please_select + $this->SupportManagerproTickets->getStatuses();
		unset($statuses['closed']);
		
		// Set default vars
		if (!isset($vars))
			$vars = (object)array('status' => "open");
		
		$this->set("vars", $vars);
		$this->set("departments", $departments);
		$this->set("priorities", (isset($priorities) ? $priorities : $please_select));
		$this->set("statuses", $statuses);
		$this->set("department_staff", $department_staff);
		$this->set("client", $client);
		$this->set("staff_settings", $this->SupportManagerproStaff->getSettings($this->staff_id, $this->company_id));
	}
	
	/**
	 * Reply to a ticket
	 */
	public function reply() {

		// Ensure a valid ticket is given
		if (!isset($this->get[0]) || !($ticket = $this->SupportManagerproTickets->get($this->get[0], true, null, $this->staff_id)))
			$this->redirect($this->base_uri . "plugin/support_managerpro/admin_tickets/");

		$this->uses(array("Clients", "SupportManagerpro.SupportManagerproDepartments"));
		
		if (!empty($this->post)) {
			$data = $this->post;
			$data['type'] = (isset($this->post['reply_type']) && in_array($this->post['reply_type'], array("reply", "note")) ? $this->post['reply_type'] : null);
			$data['staff_id'] = $this->staff_id;
			
			// Set the details field
			if ($data['type'] == "note")
				$data['details'] = $data['notes'];
			
			// Create a transaction
			$this->SupportManagerproTickets->begin();
			
			// Add the reply
			$reply_id = $this->SupportManagerproTickets->addReply($ticket->id, $data, $this->files);
			
			if (($errors = $this->SupportManagerproTickets->errors())) {
				// Error, reset vars
				$this->SupportManagerproTickets->rollBack();
				
				$vars = (object)$this->post;
				$this->setMessage("error", $errors, false, null, false);
			}
			else {
				// Success, commit
				$this->SupportManagerproTickets->commit();
				
				// Get the company hostname
				$hostname = isset(Configure::get("Blesta.company")->hostname) ? Configure::get("Blesta.company")->hostname : "";
				
				// Send the email associated with this ticket
				$key = mt_rand();
				$hash = $this->SupportManagerproTickets->generateReplyHash($ticket->id, $key);
				$additional_tags = array('SupportManagerpro.ticket_updated' => array('update_ticket_url' => $this->Html->safe($hostname . $this->client_uri . "plugin/support_managerpro/client_tickets/reply/" . $ticket->id . "/?sid=" . rawurlencode($this->SupportManagerproTickets->systemEncrypt('h=' . substr($hash, -16) . "|k=" . $key)))));
				$this->SupportManagerproTickets->sendEmail($reply_id, $additional_tags);

				// Notify staff that a ticket has been assigned to them
				if (!empty($this->post['ticket_staff_id']) && $this->post['ticket_staff_id'] != $ticket->staff_id && $this->staff_id != $this->post['ticket_staff_id'])
					$this->SupportManagerproTickets->sendTicketAssignedEmail($ticket->id);

				$this->flashMessage("message", Language::_("AdminTickets.!success.ticket_updated", true, $ticket->code), null, false);
				$this->redirect($this->base_uri . "plugin/support_managerpro/admin_tickets/");
			}
		}
		
		// Set initial ticket
		if (!isset($vars)) {
			$vars = $ticket;
			$vars->ticket_staff_id = $ticket->staff_id;
		}
		
		// Load the Text Parser
		$this->helpers(array("TextParser"));
		
		$this->set("ticket", $ticket);
		$this->set("vars", $vars);
		$this->set("statuses", $this->SupportManagerproTickets->getStatuses());
		$this->set("priorities", $this->SupportManagerproTickets->getPriorities());
		$this->set("staff_id", $this->staff_id);
		
		// Set the client this ticket belongs to
		if (!empty($ticket->client_id))
			$this->set("client", $this->Clients->get($ticket->client_id, false));

		$please_select = array('' => Language::_("AppController.select.please", true));
		$departments = $please_select + $this->Form->collapseObjectArray($this->SupportManagerproDepartments->getAll($this->company_id), "name", "id");
		
		$department_staff = array('' => Language::_("AdminTickets.text.unassigned", true)) +
			$this->Form->collapseObjectArray($this->SupportManagerproStaff->getAll($this->company_id, $ticket->department_id, false), array("first_name", "last_name"), "id", " ");

		$this->set("departments", $departments);
		$this->set("department_staff", $department_staff);
		
		// Make staff settings available for those staff that have replied to this ticket
		$staff_settings = array($this->staff_id => $this->SupportManagerproStaff->getSettings($this->staff_id, $this->company_id));
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
		$this->set("ticket_actions", $this->getReplyActions());
		
		// Set the page title
		$this->structure->set("page_title", Language::_("AdminTickets.reply.page_title", true, $ticket->code));
	}
	
	/**
	 * AJAX Fetch clients when searching
	 * @see AdminTickets::add()
	 */
	public function getClients() {
		// Ensure there is post data
		if (!$this->isAjax() || empty($this->post['search'])) {
			header($this->server_protocol . " 401 Unauthorized");
			exit();
		}
		
		$this->uses(array("Clients"));
		$search = $this->post['search'];
		$clients = $this->Form->collapseObjectArray($this->Clients->search($search), array("id_code", "first_name", "last_name"), "id", " ");
		
		echo $this->Json->encode(array('clients' => $clients));
		return false;
	}
	
	/**
	 * AJAX Fetch non-closed tickets
	 * @see AdminTickets::add()
	 */
	public function searchByCode() {
		// Ensure there is post data
		if (!$this->isAjax() || empty($this->post['search'])) {
			header($this->server_protocol . " 401 Unauthorized");
			exit();
		}
		
		$this->uses(array("Clients"));
		
		// Find matching tickets
		$search = $this->post['search'];
        $page = 1;
		$tickets = $this->SupportManagerproTickets->searchByCode($search, $this->staff_id, "not_closed", $page, array('code' => "desc"));
		
		// Fetch the client name for each ticket
		$clients = array();
		foreach ($tickets as &$ticket) {
			if (!empty($ticket->client_id) && !isset($clients[$ticket->client_id]))
				$clients[$ticket->client_id] = $this->Clients->get($ticket->client_id, false);
			
			$ticket->display_name = Language::_("AdminTickets.index.ticket_email", true, $ticket->code, $ticket->email);
			if (!empty($clients[$ticket->client_id]))
				$ticket->display_name = Language::_("AdminTickets.index.ticket_name", true, $ticket->code, $clients[$ticket->client_id]->first_name, $clients[$ticket->client_id]->last_name);
		}
        
        $tickets = $this->Form->collapseObjectArray($tickets, array("display_name"), "id", " ");
        
		echo $this->Json->encode(array('tickets' => $tickets));
		return false;
	}
	
	/**
	 * Search tickets
	 */
	public function search() {
		// Get search criteria
		$search = (isset($this->get['search']) ? $this->get['search'] : "");
		if (isset($this->post['search']))
			$search = $this->post['search'];
		
		// Set page title
		$this->structure->set("page_title", Language::_("AdminTickets.search.page_title", true, $search));
		
		$page = (isset($this->get[0]) ? (int)$this->get[0] : 1);
		$sort = (isset($this->get['sort']) ? $this->get['sort'] : "last_reply_date");
		$order = (isset($this->get['order']) ? $this->get['order'] : "desc");
		
		$this->set("sort", $sort);
		$this->set("order", $order);
		$this->set("negate_order", ($order == "asc" ? "desc" : "asc"));
		$this->set("search", $search);
		
		// Search
		$tickets = $this->SupportManagerproTickets->search($search, $this->staff_id, $page, array($sort => $order));
		foreach ($tickets as &$ticket)
			$ticket->last_reply_time = $this->timeSince($ticket->last_reply_date);
		
		$this->set("statuses", $this->SupportManagerproTickets->getStatuses());
		$this->set("priorities", $this->SupportManagerproTickets->getPriorities());
		$this->set("tickets", $tickets);
		
		// Overwrite default pagination settings
		$settings = array_merge(Configure::get("Blesta.pagination"), array(
				'total_results' => $this->SupportManagerproTickets->getSearchCount($search, $this->staff_id),
				'uri'=>$this->base_uri . "/plugin/support_managerpro/admin_tickets/search/",
				'params'=>array('p'=>"[p]", 'search'=>$search)
			)
		);
		$this->helpers(array("Pagination"=>array($this->get, $settings)));
		$this->Pagination->setSettings(Configure::get("Blesta.pagination_ajax"));
		
		if ($this->isAjax())
			return $this->renderAjaxWidgetIfAsync(isset($this->post['search']) ? null : (isset($this->get['search']) || isset($this->get['sort'])));
	}
	
	/**
	 * Performs a given ticket action
	 */
	public function action() {
		// Ensure a valid action was given
		if (empty($this->post['action']) || !in_array($this->post['action'], array_keys($this->getTicketActions())))
			$this->redirect($this->base_uri . "plugin/support_managerpro/admin_tickets/");
		
		switch ($this->post['action']) {
			case "merge":
                if (!empty($this->post['tickets']) && !empty($this->post['tickets'][1])) {
                    $this->SupportManagerproTickets->merge((array)$this->post['tickets']);

                    if (($errors = $this->SupportManagerproTickets->errors())) {
                        // Error
                        $this->flashMessage("error", $errors, null, false);
                    }
                    else {
                        // Success
                        $this->flashMessage("message", Language::_("AdminTickets.!success.ticket_merge", true), null, false);
                    }
                }
				break;
            case "update_status":
                $ticket_statuses = $this->SupportManagerproTickets->getStatuses();

                if (!empty($this->post['tickets']) && !empty($this->post['status']) && array_key_exists($this->post['status'], $ticket_statuses)) {
                    // Update the select tickets to the new status
                    $this->SupportManagerproTickets->editMultiple((array)$this->post['tickets'], array('by_staff_id' => $this->staff_id, 'status' => $this->post['status']));

                    if (($errors = $this->SupportManagerproTickets->errors())) {
                        // Error
                        $this->flashMessage("error", $errors, null, false);
                    }
                    else {
                        // Success
                        $this->flashMessage("message", Language::_("AdminTickets.!success.ticket_update_status", true), null, false);
                    }
                }
                break;
            case "permanent_delete":
                $ticket_statuses = $this->SupportManagerproTickets->getStatuses();

                if (!empty($this->post['tickets'])) {
                    // Permanent Delete the select tickets
                    $this->SupportManagerproTickets->DeleteTickets((array)$this->post['tickets'], array('by_staff_id' => $this->staff_id, 'status' => $this->post['status']));

                    if (($errors = $this->SupportManagerproTickets->errors())) {
                        // Error
                        $this->flashMessage("error", $errors, null, false);
                    }
                    else {
                        // Success
                        $this->flashMessage("message", Language::_("AdminTickets.!success.ticket_deleted", true), null, false);
                    }
                }
                break;
		}
		
		$this->redirect($this->base_uri . "plugin/support_managerpro/admin_tickets/");

	}

	/**
	 * AJAX Fetches the given replies for the given ticket as markdown-quoted text
	 */
	public function getQuotedReplies() {
		if (!$this->isAjax() || !isset($this->get[0]) || empty($this->post['reply_ids']) ||
			!($ticket = $this->SupportManagerproTickets->get($this->get[0], true, array("reply"), $this->staff_id))) {
			header($this->server_protocol . " 401 Unauthorized");
			exit();
		}

		$reply_ids = explode(",", $this->post['reply_ids']);

		$replies = "";
		$i = 0;
		foreach ($ticket->replies as $reply) {
			if (in_array($reply->id, $reply_ids))
				$replies .= ($i++ > 0 ? "\n" : "") . "\n" . preg_replace("/\r\n|\r|\n/", "\n>", ">" . $reply->details);
		}

		$this->outputAsJson($replies);
		return false;

	}
	
	/**
	 * Performs a given action
	 */
	public function replyAction() {
		// Ensure valid ticket was given
		if (!isset($this->get[0]) || !($ticket = $this->SupportManagerproTickets->get($this->get[0], true, null, $this->staff_id)) ||
			empty($this->post['action']) || !in_array($this->post['action'], array_keys($this->getReplyActions())))
			$this->redirect($this->base_uri . "plugin/support_managerpro/admin_tickets/");
		
		// Perform the requested action
		switch ($this->post['action']) {
			case "split":
				// Split the ticket
				$replies = (isset($this->post['replies']) ? $this->post['replies'] : array());
				$new_ticket_id = $this->SupportManagerproTickets->split($ticket->id, $replies);

				if (($errors = $this->SupportManagerproTickets->errors())) {
					// Error
					$this->flashMessage("error", $errors, null, false);
					$this->redirect($this->base_uri . "plugin/support_managerpro/admin_tickets/reply/" . $ticket->id . "/");
				}
				else {
					// Success
					$new_ticket = $this->SupportManagerproTickets->get($new_ticket_id, false);
					$this->flashMessage("message", Language::_("AdminTickets.!success.ticket_split", true, $ticket->code, ($new_ticket ? $new_ticket->code : "")), null, false);
					$this->redirect($this->base_uri . "plugin/support_managerpro/admin_tickets/reply/" . $new_ticket->id . "/");
				}
				break;
			case "permanent_delete":
				// Permanent delete reply
				$replies = (isset($this->post['replies']) ? $this->post['replies'] : array());
				$new_ticket_id = $this->SupportManagerproTickets->DeleteReply($replies);

				if (($errors = $this->SupportManagerproTickets->errors())) {
					// Error
					$this->flashMessage("error", $errors, null, false);
					$this->redirect($this->base_uri . "plugin/support_managerpro/admin_tickets/reply/" . $ticket->id . "/");
				}
				else {
					// Success
					$new_ticket = $this->SupportManagerproTickets->get($new_ticket_id, false);
					$this->flashMessage("message", Language::_("AdminTickets.!success.reply_deleted", true), null, false);
					$this->redirect($this->base_uri . "plugin/support_managerpro/admin_tickets/reply/" . $ticket->id. "/");
				}
				break;
		}
		
		$this->redirect($this->base_uri . "plugin/support_managerpro/admin_tickets/reply/" . $ticket->id . "/");
	}
	
	/**
	 * Streams an attachment to view
	 */
	public function getAttachment() {
		// Ensure a valid attachment was given
		if (!isset($this->get[0]) || !($attachment = $this->SupportManagerproTickets->getAttachment($this->get[0])))
			exit();
		
		// Ensure the staff member can view the attachment
		$staff = $this->SupportManagerproStaff->get($this->staff_id, $this->company_id);
		if (!in_array($attachment->department_id, $this->Form->collapseObjectArray($staff->departments, "id", "id")))
			exit();
		
		$this->components(array("Download"));
		
		$this->Download->downloadFile($attachment->file_name, $attachment->name);
		return false;
	}
	
	/**
	 * AJAX Fetches a list of department priorities and the default priority
	 */
	public function getPriorities() {
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
		$this->uses(array("SupportManagerpro.SupportManagerproDepartments"));
		if (!$this->isAjax() || !($department = $this->SupportManagerproDepartments->get($this->get[0])) ||
			$department->company_id != $this->company_id) {
			header($this->server_protocol . " 401 Unauthorized");
			exit();
		}
		
		// Set priorities
		$vars['default_priority'] = $department->default_priority;
		$vars['priorities'] = $please_select + $this->SupportManagerproTickets->getPriorities();
		
		$this->outputAsJson($vars);
		return false;
	}
	
	/**
	 * AJAX request to fetch all department that belong to a given support department
	 */
	public function getDepartmentStaff() {
		
		$department_staff = array('' => Language::_("AdminTickets.text.unassigned", true));
		
		if (!isset($this->get[0])) {
			$this->outputAsJson($department_staff);
			return false;
		}
		
		// Ensure a valid department was given
		$this->uses(array("SupportManagerpro.SupportManagerproDepartments"));
		if (!$this->isAjax() || !($department = $this->SupportManagerproDepartments->get($this->get[0])) ||
			$department->company_id != $this->company_id) {
			header($this->server_protocol . " 401 Unauthorized");
			exit();
		}
		
		$department_staff += $this->Form->collapseObjectArray($this->SupportManagerproStaff->getAll($this->company_id, $department->id, false), array("first_name", "last_name"), "id", " ");
		$this->outputAsJson($department_staff);
		return false;
	}
	
	/**
	 * AJAX retrieves the partial that lists categories and responses
	 */
	public function getResponseListing() {
		$this->uses(array("SupportManagerpro.SupportManagerproResponses"));
		// Ensure a valid category was given
		$category = (isset($this->get[0]) ? $this->SupportManagerproResponses->getCategory($this->get[0]) : null);
		if ($category && $category->company_id != $this->company_id) {
			header($this->server_protocol . " 401 Unauthorized");
			exit();
		}
		
		// Load language for responses
		Language::loadLang("admin_responses", null, PLUGINDIR . "support_managerpro" . DS . "language" . DS);
		
		// Build the partial for listing categories and responses
		$category_id = (isset($category->id) ? $category->id : null);
		$vars = array(
			'categories' => $this->SupportManagerproResponses->getAllCategories($this->company_id, $category_id),
			'category' => $category,
			'show_links' => false
		);
		
		if ($category)
			$vars['responses'] = $this->SupportManagerproResponses->getAll($this->company_id, $category_id);
		
		echo $this->Json->encode($this->partial("admin_responses_response_list", $vars));
		return false;
	}
	
	/**
	 * AJAX retrieves the predefined response text for a specific response
	 */
	public function getResponse() {
		$this->uses(array("SupportManagerpro.SupportManagerproResponses"));
		// Ensure a valid response was given
		$response = (isset($this->get[0]) ? $this->SupportManagerproResponses->get($this->get[0]) : null);
		if ($response && $response->company_id != $this->company_id) {
			header($this->server_protocol . " 401 Unauthorized");
			exit();
		}
		
		echo $this->Json->encode($response->details);
		return false;
	}
}
?>