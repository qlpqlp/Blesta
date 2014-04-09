<?php
/**
 * SupportManagerproTickets model
 *
 * @package blesta
 * @subpackage blesta.plugins.support_managerpro
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class SupportManagerproTickets extends SupportManagerproModel {
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		Language::loadLang("support_managerpro_tickets", null, PLUGINDIR . "support_managerpro" . DS . "language" . DS);
	}

	/**
	 * Adds a support ticket
	 *
	 * @param array $vars A list of ticket vars, including:
	 * 	- department_id The ID of the department to assign this ticket
	 * 	- staff_id The ID of the staff member this ticket is assigned to (optional)
	 * 	- service_id The ID of the service this ticket is related to (optional)
	 * 	- client_id The ID of the client this ticket is assigned to (optional)
	 * 	- email The email address that a ticket was emailed in from (optional)
	 * 	- summary A brief title/summary of the ticket issue
	 * 	- priority The ticket priority (i.e. "emergency", "critical", "high", "medium", "low") (optional, default "low")
	 * 	- status The status of the ticket (i.e. "open", "awaiting_reply", "in_progress", "closed") (optional, default "open")
	 * @param boolean $require_email True to require the email field be given, false otherwise (optional, default false)
	 * @return mixed The ticket ID, or null on error
	 */
	public function add(array $vars, $require_email = false) {
		// Generate a ticket number
		$vars['code'] = $this->generateCode();
		
		if (isset($vars['staff_id']) && $vars['staff_id'] == "")
			$vars['staff_id'] = null;
		if (isset($vars['service_id']) && $vars['service_id'] == "")
			$vars['service_id'] = null;
		
		$vars['date_added'] = date("c");
		$this->Input->setRules($this->getRules($vars, false, $require_email));
		
		if ($this->Input->validates($vars)) {
			// Add the support ticket
			$fields = array("code", "department_id", "staff_id", "service_id", "client_id",
				"name", "email", "summary", "priority", "status", "date_added");
			$this->Record->insert("support_ticketspro", $vars, $fields);
			
			return $this->Record->lastInsertId();
		}
	}
	
	/**
	 * Updates a support ticket
	 *
	 * @param int $ticket_id The ID of the ticket to update
	 * @param array $vars A list of ticket vars, including (all optional):
	 * 	- department_id The department to reassign the ticket to
	 * 	- staff_id The ID of the staff member to assign the ticket to
	 * 	- service_id The ID of the client service this ticket relates to
	 * 	- client_id The ID of the client this ticket is to be assigned to (can only be set if it is currently null)
	 * 	- summary A brief title/summary of the ticket issue
	 * 	- priority The ticket priority (i.e. "emergency", "critical", "high", "medium", "low")
	 * 	- status The status of the ticket (i.e. "open", "awaiting_reply", "in_progress", "closed")
	 * @return stdClass An stdClass object representing the ticket (without replies)
	 */
	public function edit($ticket_id, array $vars) {
		$vars['ticket_id'] = $ticket_id;
		
		if (isset($vars['staff_id']) && $vars['staff_id'] == "")
			$vars['staff_id'] = null;
		if (isset($vars['service_id']) && $vars['service_id'] == "")
			$vars['service_id'] = null;
		
		$this->Input->setRules($this->getRules($vars, true));
		
		// Update the ticket
		if ($this->Input->validates($vars)) {
			$fields = array("department_id", "staff_id", "service_id", "client_id", "summary",
				"priority", "status");
			
			// Allow the date closed to be set
			if (isset($vars['status'])) {
				$fields[] = "date_closed";
				if ($vars['status'] == "closed") {
					if (empty($vars['date_closed']))
						$vars['date_closed'] = $this->dateToUtc(date("c"));
				}
				else
					$vars['date_closed'] = null;
			}
			
			$this->Record->where("id", "=", $ticket_id)->
				update("support_ticketspro", $vars, $fields);
			
			return $this->Record->get($ticket_id, false);
		}
	}
	
	/**
	 * Closes a ticket and logs that it has been closed
	 *
	 * @param int $ticket_id The ID of the ticket to close
	 * @param int $staff_id The ID of the staff that closed the ticket (optional, default null if client closed the ticket)
	 */
	public function close($ticket_id, $staff_id = null) {
		// Set vars
		$statuses = $this->getStatuses();
		$vars = array(
			'details' => Language::_("SupportManagerproTickets.log.status", true, $statuses['closed']),
			'type' => "log"
		);

		// Begin a transaction
		$this->Record->begin();

		// Add the reply
		$this->addReply($ticket_id, $vars);
		$errors = $this->errors();

		// Update the ticket to closed
		$this->edit($ticket_id, array('status' => "closed", 'date_closed' => date("c")));
		$edit_errors = $this->errors();
		$errors = array_merge(($errors ? $errors : array()), ($edit_errors ? $edit_errors : array()));

		if ($errors)
			$this->Record->rollBack();
		else
			$this->Record->commit();
	}
	
	/**
	 * Adds a reply to a ticket. If ticket data (e.g. department_id, status, priority, summary) have changed
	 * then this will also invoke SupportManagerproTickets::edit() to update the ticket, and record any log entries.
	 *
	 * Because of this functionality, this method is assumed to (and should) already be in a transaction when called,
	 * and SupportManagerproTickets::edit() should not be called separately.
	 * 
	 * @param int $ticket_id The ID of the ticket to reply to
	 * @param array $vars A list of reply vars, including:
	 * 	- staff_id The ID of the staff member this reply is from (optional)
	 * 	- client_id The ID of the client this reply is from (optional)
	 * 	- type The type of reply (i.e. "reply, "note", "log") (optional, default "reply")
	 * 	- details The details of the ticket
	 * 	- department_id The ID of the ticket department (optional)
	 * 	- summary The ticket summary (optional)
	 * 	- priority The ticket priority (optional)
	 * 	- status The ticket status (optional)
	 * 	- ticket_staff_id The ID of the staff member the ticket is assigned to (optional)
	 * @param array $files A list of file attachments that matches the global FILES array, which contains an array of "attachment" files
	 * @param boolean $new_ticket True if this reply is apart of ticket being created, false otherwise (default false)
	 * @return int The ID of the ticket reply on success, void on error
	 */
	public function addReply($ticket_id, array $vars, array $files = null, $new_ticket = false) {
		$vars['ticket_id'] = $ticket_id;
		$vars['date_added'] = date("c");
		if (!isset($vars['type']))
			$vars['type'] = "reply";
			
		// Remove reply details if it contains only the signature
		if (isset($vars['details']) && isset($vars['staff_id'])) {
			if (!isset($this->SupportManagerproStaff))
				Loader::loadModels($this, array("SupportManagerpro.SupportManagerproStaff"));
				
			$staff_settings = $this->SupportManagerproStaff->getSettings($vars['staff_id'], Configure::get("Blesta.company_id"));
			if (isset($staff_settings['signature']) && trim($staff_settings['signature']) == trim($vars['details']))
				$vars['details'] = "";
		}

		// Determine whether or not options have changed that need to be logged
		$log_options = array();
		// "status" should be the last element in case it is set to closed, so it will be the last log entry added
		$loggable_fields = array('department_id' => "department_id", 'ticket_staff_id' => "staff_id", 'summary' => "summary",
			'priority' => "priority", 'status' => "status");

		if (!$new_ticket && $vars['type'] == "reply" && (isset($vars['department_id']) || isset($vars['summary']) || isset($vars['priority']) || isset($vars['status']) || isset($vars['ticket_staff_id']))) {
			if (($ticket = $this->get($ticket_id, false))) {
				// Determine if any log replies need to be made
				foreach ($loggable_fields as $key => $option) {
					// Save to be logged iff the field has been changed
					if (isset($vars[$key]) && property_exists($ticket, $option) && $ticket->{$option} != $vars[$key])
						$log_options[] = $key;
				}
			}
		}
		
		// Check whether logs are being added simultaneously, and if so, do not
		// add a reply iff no reply details, nor files, are attached
		// i.e. allow log entries to be added without a reply/note regardless of vars['type']
		$skip_reply = false;
		if (!empty($log_options) && empty($vars['details']) && (empty($files) || empty($files['attachment']['name'][0])))
			$skip_reply = true;

		if (!$skip_reply) {
			$this->Input->setRules($this->getReplyRules($vars, $new_ticket));
			
			if ($this->Input->validates($vars)) {
				// Create the reply
				$fields = array("ticket_id", "staff_id", "type", "details", "date_added");
				$this->Record->insert("support_repliespro", $vars, $fields);
				$reply_id = $this->Record->lastInsertId();


        if ($vars['details'] != "" && isset($vars['staff_id']) && isset($vars['status'])){
         // update replay ticket status to awaiting_reply
                  $this->edit($vars['ticket_id'], array('status' => "awaiting_reply"));
        }


				// Handle file upload
				if (!empty($files['attachment'])) {
					Loader::loadComponents($this, array("SettingsCollection", "Upload"));
					
					// Set the uploads directory
					$temp = $this->SettingsCollection->fetchSetting(null, Configure::get("Blesta.company_id"), "uploads_dir");
					$upload_path = $temp['value'] . Configure::get("Blesta.company_id") . DS . "support_managerpro_files" . DS;
					
					$this->Upload->setFiles($files, false);
					$this->Upload->setUploadPath($upload_path);
					
					$file_vars = array('files' => array());
					if (!($errors = $this->Upload->errors())) {
						// Will not overwrite existing file
						$this->Upload->writeFile("attachment", false, null, array($this, "makeFileName"));
						$data = $this->Upload->getUploadData();
						
						// Set the file names/paths
						foreach ($files['attachment']['name'] as $index => $file_name) {
							if (isset($data['attachment'][$index])) {
								$file_vars['files'][] = array(
									'name' => $data['attachment'][$index]['orig_name'],
									'file_name' => $data['attachment'][$index]['full_path']
								);
							}
						}
						
						$errors = $this->Upload->errors();
					}
					
					// Error, could not upload the files
					if ($errors) {
						$this->Input->setErrors($errors);
						// Attempt to remove the files if they were somehow written
						foreach ($file_vars['files'] as $files) {
							if (isset($files['file_name']))
								@unlink($files['file_name']);
						}
						return;
					}
					else {
						// Add the attachments
						$file_fields = array("reply_id", "name", "file_name");
						foreach ($file_vars['files'] as $files) {
							if (!empty($files))
								$this->Record->insert("support_attachmentspro", array_merge($files, array('reply_id' => $reply_id)), $file_fields);
						}
					}
				}
			}
		}
		
		// Only attempt to update log options if there are no previous errors
		if (!empty($log_options) && !$this->errors()) {
			// Update the support ticket
			$data = array_intersect_key($vars, $loggable_fields);
			$ticket_staff_id_field = array();
			if (isset($data['ticket_staff_id']))
				$ticket_staff_id_field = (isset($data['ticket_staff_id']) ? array('staff_id' => $data['ticket_staff_id']) : array());
				
			$this->edit($ticket_id, array_merge($data, $ticket_staff_id_field));
			
			if (!($errors = $this->errors())) {
				// Log each support ticket field change
				foreach ($log_options as $field) {
					$log_vars = array(
						'staff_id' => isset($vars['staff_id']) ? $vars['staff_id'] : "0",
						'type' => "log"
					);
					
					$lang_var1 = "";
					switch ($field) {
						case "department_id":
							$department = $this->Record->select("name")->from("support_departmentspro")->
								where("id", "=", $vars['department_id'])->fetch();
							$lang_var1 = ($department ? $department->name : "");
							break;
						case "priority":
							$priorities = $this->getPriorities();
							$lang_var1 = (isset($priorities[$vars['priority']]) ? $priorities[$vars['priority']] : "");
							break;
						case "status":
							$statuses = $this->getStatuses();
							$lang_var1 = (isset($statuses[$vars['status']]) ? $statuses[$vars['status']] : "");
							break;
						case "ticket_staff_id":
							if (!isset($this->Staff))
								Loader::loadModels($this, array("Staff"));
								
							$staff = $this->Staff->get($vars['ticket_staff_id']);
							
							if ($vars['ticket_staff_id'] && $staff)
								$lang_var1 = $staff->first_name . " " . $staff->last_name;
							else
								$lang_var1 = Language::_("SupportManagerproTickets.log.unassigned", true);
						default:
							break;
					}
					
					$log_vars['details'] = Language::_("SupportManagerproTickets.log." . $field, true, $lang_var1);
					
					$this->addReply($ticket_id, $log_vars);
				}
			}
		}
		
		// Return the ID of the reply
		if (isset($reply_id))
			return $reply_id;
	}
	
	/**
	 * Retrieves the total number of tickets in the given status assigned to the given staff/client
	 *
	 * @param string $status The status of the support tickets ('open', 'awaiting_reply', 'in_progress', 'closed')
	 * @param int $staff_id The ID of the staff member assigned to the tickets or associated departments (optional)
	 * @param int $client_id The ID of the client assigned to the tickets (optional)
	 * @return int The total number of tickets in the given status
	 */
	public function getStatusCount($status, $staff_id = null, $client_id = null) {
		// Fetch all departments this staff belongs to
		$department_ids = array();
		if ($staff_id)
			$department_ids = $this->getStaffDepartments($staff_id);
		
		// Fetch tickets
		$this->Record->select(array("support_ticketspro.id"))->
			from("support_ticketspro")->
			innerJoin("support_departmentspro", "support_departmentspro.id", "=", "support_ticketspro.department_id", false)->
			where("support_departmentspro.company_id", "=", Configure::get("Blesta.company_id"));
		
		// Filter by status
		switch ($status) {
			case "not_closed":
				$this->Record->where("support_ticketspro.status", "!=", "closed");
				break;
			default:
				$this->Record->where("support_ticketspro.status", "=", $status);
				break;
		}
		
		// Filter by tickets staff can view
		if ($staff_id) {
			// Staff must be assigned to the ticket or in the same department as the ticket
			$this->Record->open()->where("support_ticketspro.staff_id", "=", $staff_id);
				
			if (!empty($department_ids))
				$this->Record->orWhere("support_ticketspro.department_id", "in", $department_ids);
				
			$this->Record->close();
		}
		
		// Filter by tickets assigned to the client
		if ($client_id)
			$this->Record->where("support_ticketspro.client_id", "=", $client_id);
		
		return $this->Record->group("support_ticketspro.id")->numResults();
	}
	
	/**
	 * Retrieves a specific ticket
	 *
	 * @param int $ticket_id The ID of the ticket to fetch
	 * @param boolean $get_replies True to include the ticket replies, false not to
	 * @param array $reply_types A list of reply types to include (optional, default null for all)
	 * 	- "reply", "note", "log"
	 * @param int $staff_id The ID of the staff member assigned to the tickets or associated departments (optional)
	 * @return mixed An stdClass object representing the ticket, or false if none exist
	 */
	public function get($ticket_id, $get_replies = true, array $reply_types = null, $staff_id = null) {
		// Get the ticket
		$ticket = $this->getTickets(null, $staff_id, null, $ticket_id)->fetch();
		
		if ($ticket && $get_replies)
			$ticket->replies = $this->getReplies($ticket->id, $reply_types);
		
		return $ticket;
	}

	/**
	 * Retrieves a specific ticket
	 *
	 * @param int $code The code of the ticket to fetch
	 * @param boolean $get_replies True to include the ticket replies, false not to
	 * @param array $reply_types A list of reply types to include (optional, default null for all)
	 * 	- "reply", "note", "log"
	 * @return mixed An stdClass object representing the ticket, or false if none exist
	 */
	public function getTicketByCode($code, $get_replies = true, array $reply_types = null) {
		// Get the ticket
		$ticket = $this->getTickets()->where("support_ticketspro.code", "=", $code)->fetch();

		if ($get_replies)
			$ticket->replies = $this->getReplies($ticket->id, $reply_types);

		return $ticket;
	}
	
	/**
	 * Converts the given file name into an appropriate file name to store to disk
	 *
	 * @param string $file_name The name of the file to rename
	 * @return string The rewritten file name in the format of YmdTHisO_[hash] (e.g. 20121009T154802+0000_1f3870be274f6c49b3e31a0c6728957f)
	 */
	public function makeFileName($file_name) {
		$ext = strrchr($file_name, ".");
		$file_name = md5($file_name . uniqid()) . $ext;
		
		return $this->dateToUtc(date("c"), "Ymd\THisO") . "_" . $file_name;
	}

	/**
	 * Retrieve a list of tickets
	 *
	 * @param string $status The status of the support tickets ('open', 'awaiting_reply', 'in_progress', 'closed', 'not_closed')
	 * @param int $staff_id The ID of the staff member assigned to the tickets or associated departments (optional)
	 * @param int $client_id The ID of the client assigned to the tickets (optional)
	 * @param int $page The page number of results to fetch
	 * @param array $order_by A list of sort=>order options
	 * @param boolean $get_replies True to include the ticket replies, false not to
	 * @param array $reply_types A list of reply types to include (optional, default null for all)
	 * 	- "reply", "note", "log"
	 * @return array A list of stdClass objects representing tickets
	 */
	public function getList($status, $staff_id = null, $client_id = null, $page = 1, array $order_by = array('last_reply_date' => "desc"), $get_replies = true, array $reply_types = null) {
		$tickets = $this->getTickets($status, $staff_id, $client_id)->order($order_by)->
			limit($this->getPerPage(), (max(1, $page) - 1)*$this->getPerPage())->fetchAll();
		
		// Fetch ticket replies
		if ($get_replies) {
			foreach ($tickets as &$ticket)
				$ticket->replies = $this->getReplies($ticket->id, $reply_types);
		}
		
		return $tickets;
	}
	
	/**
	 * Retrieves the total number of tickets
	 *
	 * @param string $status The status of the support tickets ('open', 'awaiting_reply', 'in_progress', 'closed', 'not_closed')
	 * @param int $staff_id The ID of the staff member assigned to the tickets or associated departments (optional)
	 * @param int $client_id The ID of the client assigned to the tickets (optional)
	 * @return int The total number of tickets
	 */
	public function getListCount($status, $staff_id = null, $client_id = null) {
		return $this->getTickets($status, $staff_id, $client_id)->numResults();
	}
	
	/**
	 * Search tickets
	 *
	 * @param string $query The value to search tickets for
	 * @param int $staff_id The ID of the staff member searching tickets (optional)
	 * @param int $page The page number of results to fetch (optional, default 1)
	 * @param array $order_by The sort=>$order options
	 * @return array An array of tickets that match the search criteria
	 */
	public function search($query, $staff_id = null, $page=1, $order_by = array('last_reply_date' => "desc")) {
		$this->Record = $this->searchTickets($query, $staff_id);
		return $this->Record->order($order_by)->
			limit($this->getPerPage(), (max(1, $page) - 1)*$this->getPerPage())->
			fetchAll();
	}
	
	/**
	 * Returns the total number of tickets returned from SupportManagerproTickets::search(), useful
	 * in constructing pagination
	 *
	 * @param string $query The value to search tickets for
	 * @param int $staff_id The ID of the staff member searching tickets (optional)
	 * @see SupportManagerproTickets::search()
	 */
	public function getSearchCount($query, $staff_id = null) {
		$this->Record = $this->searchTickets($query, $staff_id);
		return $this->Record->numResults();
	}
	
	/**
	 * Partially constructs the query for searching tickets
	 *
	 * @param string $query The value to search tickets for
	 * @param int $staff_id The ID of the staff member searching tickets
	 * @return Record The partially constructed query Record object
	 * @see SupportManagerproTickets::search(), SupportManagerproTickets::getSearchCount()
	 */
	private function searchTickets($query, $staff_id = null) {
		// Fetch the tickets
		$this->Record = $this->getTickets(null, $staff_id);
		
		$this->Record->open()->
				like("support_ticketspro.summary", "%" . $query . "%")->
				orLike("support_ticketspro.email", "%" . $query . "%")->
				orLike("support_ticketspro.code", "%" . $query . "%")->
			close();
		
		return $this->Record;
	}
	
	/**
	 * Retrieves a specific attachment
	 *
	 * @param int $attachment_id The ID of the attachment to fetch
	 * @return mixed An stdClass object representing the attachment, or false if none exist
	 */
	public function getAttachment($attachment_id) {
		$fields = array("support_attachmentspro.*", "support_repliespro.ticket_id", "support_ticketspro.client_id", "support_ticketspro.department_id");
		return $this->Record->select($fields)->from("support_attachmentspro")->
			innerJoin("support_repliespro", "support_repliespro.id", "=", "support_attachmentspro.reply_id", false)->
			innerJoin("support_ticketspro", "support_ticketspro.id", "=", "support_repliespro.ticket_id", false)->
			where("support_attachmentspro.id", "=", $attachment_id)->fetch();
	}
	
	/**
	 * Retrieves a list of attachments for a given ticket
	 *
	 * @param int $ticket_id The ID of the ticket to fetch attachments for
	 * @param int $reply_id The ID of the reply belonging to this ticket to fetch attachments for
	 * @return array A list of attachments
	 */
	public function getAttachments($ticket_id, $reply_id = null) {
		$fields = array("support_attachmentspro.*");
		$this->Record->select($fields)->from("support_attachmentspro")->
			innerJoin("support_repliespro", "support_repliespro.id", "=", "support_attachmentspro.reply_id", false)->
			innerJoin("support_ticketspro", "support_ticketspro.id", "=", "support_repliespro.ticket_id", false)->
			where("support_ticketspro.id", "=", $ticket_id);
		
		// Fetch attachments only for a specific reply
		if ($reply_id)
			$this->Record->where("support_repliespro.id", "=", $reply_id);
		
		return $this->Record->order(array('support_repliespro.date_added' => "DESC"))->fetchAll();
	}
	
	/**
	 * Gets all replies to a specific ticket
	 *
	 * @param $ticket_id The ID of the ticket whose replies to fetch
	 * @param array $types A list of reply types to include (optional, default null for all)
	 * 	- "reply", "note", "log"
	 * @return array A list of replies to the given ticket
	 */
	private function getReplies($ticket_id, array $types = null) {
		$fields = array("support_repliespro.*", 'IF(support_repliespro.staff_id IS NULL, contacts.first_name, staff.first_name)' => "first_name",
			'IF(support_repliespro.staff_id IS NULL, contacts.last_name, staff.last_name)' => "last_name",
			'IF(support_repliespro.staff_id IS NULL, contacts.email, staff.email)' => "email"
		);
		
		$this->Record->select($fields, false)->
			select(array('IF(staff.id IS NULL, IF(support_ticketspro.email IS NULL, ?, ?), ?)' => "reply_by"), false)->
			appendValues(array("client", "email", "staff"))->
			from("support_repliespro")->
			innerJoin("support_ticketspro", "support_ticketspro.id", "=", "support_repliespro.ticket_id", false)->
			leftJoin("clients", "clients.id", "=", "support_ticketspro.client_id", false)->
				on("contacts.contact_type", "=", "primary")->
			leftJoin("contacts", "contacts.client_id", "=", "clients.id", false)->
			leftJoin("staff", "staff.id", "=", "support_repliespro.staff_id", false)->
			where("support_ticketspro.id", "=", $ticket_id);
		
		// Filter by specific types given
		if ($types) {
			$i = 0;
			foreach ($types as $type) {
				if ($i++ == 0)
					$this->Record->open()->where("support_repliespro.type", "=", $type);
				else
					$this->Record->orWhere("support_repliespro.type", "=", $type);
			}
			
			if ($i > 0)
				$this->Record->close();
		}
		
		$replies = $this->Record->order(array('support_repliespro.date_added' => "DESC", 'support_repliespro.id' => "DESC"))->fetchAll();
		
		// Fetch attachments
		foreach ($replies as &$reply)
			$reply->attachments = $this->getAttachments($ticket_id, $reply->id);
		
		return $replies;
	}
	
	/**
	 * Returns a Record object for fetching tickets
	 *
	 * @param string $status The status of the support tickets ('open', 'awaiting_reply', 'in_progress', 'closed', 'not_closed')
	 * @param int $staff_id The ID of the staff member assigned to the tickets or associated departments (optional)
	 * @param int $client_id The ID of the client assigned to the tickets (optional)
	 * @param int $ticket_id The ID of a specific ticket to fetch
	 * @return Record A partially-constructed Record object for fetching tickets
	 */
	private function getTickets($status = null, $staff_id = null, $client_id = null, $ticket_id = null) {
		// Fetch all departments this staff belongs to
		$department_ids = array();
		if ($staff_id)
			$department_ids = $this->getStaffDepartments($staff_id);
		
		$sub_query = new Record();
		$sub_query->select(array("support_repliespro.ticket_id", 'MAX(support_repliespro.date_added)' => "reply_date"))->
			from("support_repliespro")->where("support_repliespro.type", "=", "reply")->
			group(array("support_repliespro.ticket_id"));
		$replies = $sub_query->get();
		$reply_values = $sub_query->values;
		$this->Record->reset();
		
		$fields = array("support_ticketspro.*", 'support_repliespro.date_added' => "last_reply_date",
			'support_departmentspro.name' => "department_name", "support_departmentspro.company_id");
		$last_reply_fields = array(
			'IF(support_repliespro.staff_id IS NULL, IF(support_ticketspro.email IS NULL, ?, ?), ?)' => "last_reply_by",
			'IF(support_repliespro.staff_id IS NULL, contacts.first_name, staff.first_name)' => "last_reply_first_name",
			'IF(support_repliespro.staff_id IS NULL, contacts.last_name, staff.last_name)' => "last_reply_last_name",
			'IF(support_repliespro.staff_id IS NULL, IFNULL(support_ticketspro.email, ?), ?)' => "last_reply_email"
		);
		$last_reply_values = array(
			"client", "email", "staff",
			null, null
		);
		
		$this->Record->select($fields)->
			select($last_reply_fields, false)->appendValues($last_reply_values)->
			from("support_ticketspro")->
			on("support_repliespro.type", "=", "reply")->
			innerJoin("support_repliespro", "support_ticketspro.id", "=", "support_repliespro.ticket_id", false)->
			on("support_repliespro.date_added", "=", "replies.reply_date", false)->
			innerJoin(array($replies => "replies"), "replies.ticket_id", "=", "support_repliespro.ticket_id", false)->
			appendValues($reply_values)->
			on("support_departmentspro.company_id", "=", Configure::get("Blesta.company_id"))->
			innerJoin("support_departmentspro", "support_departmentspro.id", "=", "support_ticketspro.department_id", false)->
			leftJoin("clients", "clients.id", "=", "support_ticketspro.client_id", false)->
			on("contacts.contact_type", "=", "primary")->
			leftJoin("contacts", "contacts.client_id", "=", "clients.id", false)->
			leftJoin("staff", "staff.id", "=", "support_repliespro.staff_id", false);
		
		// Filter by status
		if ($status) {
			switch ($status) {
				case "not_closed":
					$this->Record->where("support_ticketspro.status", "!=", "closed");
					break;
				default:
					$this->Record->where("support_ticketspro.status", "=", $status);
					break;
			}
		}
		
		// Filter by a single ticket
		if ($ticket_id)
			$this->Record->where("support_ticketspro.id", "=", $ticket_id);
		
		// Filter by tickets staff can view
		if ($staff_id) {
			// Staff must be assigned to the ticket or in the same department as the ticket
			$this->Record->open()->where("support_ticketspro.staff_id", "=", $staff_id);
			
			if (!empty($department_ids))
				$this->Record->orWhere("support_ticketspro.department_id", "in", $department_ids);

			$this->Record->close();
		}
		
		// Filter by tickets assigned to the client
		if ($client_id)
			$this->Record->where("support_ticketspro.client_id", "=", $client_id);
		
		return $this->Record;
	}
	
	/**
	 * Retrieves a list of priorities and their language
	 *
	 * @return array A list of priority => language priorities
	 */
	public function getPriorities() {
		return array(
			'emergency' => $this->_("SupportManagerproTickets.priority.emergency"),
			'critical' => $this->_("SupportManagerproTickets.priority.critical"),
			'high' => $this->_("SupportManagerproTickets.priority.high"),
			'medium' => $this->_("SupportManagerproTickets.priority.medium"),
			'low' => $this->_("SupportManagerproTickets.priority.low")
		);
	}
	
	/**
	 * Retrieves a list of statuses and their language
	 *
	 * @return array A list of status => language statuses
	 */
	public function getStatuses() {
		return array(
			'open' => $this->_("SupportManagerproTickets.status.open"),
			'awaiting_reply' => $this->_("SupportManagerproTickets.status.awaiting_reply"),
			'in_progress' => $this->_("SupportManagerproTickets.status.in_progress"),
			'closed' => $this->_("SupportManagerproTickets.status.closed"),
			'spam' => $this->_("SupportManagerproTickets.status.spam"),
			'deleted' => $this->_("SupportManagerproTickets.status.deleted")
		);
	}
	
	/**
	 * Retrieves a list of reply types and their language
	 *
	 * @return array A list of type => language reply types
	 */
	public function getReplyTypes() {
		return array(
			'reply' => $this->_("SupportManagerproTickets.type.reply"),
			'note' => $this->_("SupportManagerproTickets.type.note"),
			'log' => $this->_("SupportManagerproTickets.type.log")
		);
	}
	
	/**
	 * Retrieves a list of department IDs for a given staff member
	 *
	 * @param int $staff_id The ID of the staff member whose departments to fetch
	 * @return array A list of department IDs that this staff member belongs to
	 */
	private function getStaffDepartments($staff_id) {
		// Fetch all departments this staff belongs to
		$departments = $this->Record->select(array("support_staff_departmentspro.department_id"))->
			from("support_staff_departmentspro")->
			where("support_staff_departmentspro.staff_id", "=", $staff_id)->
			fetchAll();
		
		// Create a list of department IDs this staff belongs to
		$department_ids = array();
		foreach ($departments as $department)
			$department_ids[] = $department->department_id;
		
		return $department_ids;
	}
	
	/**
	 * Fetches the client for the given company using the given email address.
	 * Searches first the primary contact of each client, and if no results found
	 * then any contact for the clients in the given company. Returns the first
	 * client found.
	 *
	 * @param int $company_id The ID of the company to fetch a client for
	 * @param string $email The email address to fetch clients on
	 * @return mixed A stdClass object representing the client whose contact matches the email address, false if no client found
	 */
	public function getClientByEmail($company_id, $email) {
		// Fetch client based on primary contact email
		$client = $this->Record->select(array("clients.*"))->
			from("contacts")->
			innerJoin("clients", "clients.id", "=", "contacts.client_id", false)->
			innerJoin("client_groups", "client_groups.id", "=", "clients.client_group_id", false)->
			where("client_groups.company_id", "=", $company_id)->
			where("contacts.email", "=", $email)->
			where("contacts.contact_type", "=", "primary")->fetch();
		
		// If no client found, fetch client based on any contact email
		if (!$client) {
			$client = $this->Record->select(array("clients.*"))->
				from("contacts")->
				innerJoin("clients", "clients.id", "=", "contacts.client_id", false)->
				innerJoin("client_groups", "client_groups.id", "=", "clients.client_group_id", false)->
				where("client_groups.company_id", "=", $company_id)->
				where("contacts.email", "=", $email)->fetch();
		}
		return $client;
	}
	
	/**
	 * Returns the ticket info if any exists
	 *
	 * @param string $body The body of the message
	 * @return mixed Null if no ticket info exists, an array otherwise containing:
	 * 	- ticket_code The ticket code number
	 * 	- code The validation code that can be used to verify the ticket number
	 * 	- valid Whether or not the code is valid for this ticket_code
	 */
	public function parseTicketInfo($str) {
		// Format of ticket number #NUM -CODE-
		// For example: #504928 -efa3-
		// Example in subject: Your Ticket #504928 -efa3- Has a New Comment
		preg_match("/\#([0-9]+) \-([a-f0-9]+)\-/i", $str, $matches);
		
		if (count($matches) < 3)
			return null;
		
		$ticket_code = isset($matches[1]) ? $matches[1] : null;
		$code = isset($matches[2]) ? $matches[2] : null;
		
		return array(
			'ticket_code' => $ticket_code,
			'code' => $code,
			'valid' => $this->validateReplyCode($ticket_code, $code)
		);
	}
	
	/**
	 * Generates a pseudo-random reply code from an sha256 HMAC of the ticket ID code
	 *
	 * @param int $ticket_code The ticket code to generate the reply code from
	 * @param int $length The length of the reply code between 4 and 64 characters
	 * @return string A 4-character hexidecimal reply code
	 */
	public function generateReplyCode($ticket_code, $length = 4) {
		$hash = $this->systemHash($ticket_code);
		$hash_size = strlen($hash);
		
		if ($length < 4)
			$length = 4;
		elseif ($length > $hash_size)
			$length = $hash_size;
			
		return substr($hash, mt_rand(0, $hash_size-$length), $length);
	}
	
	/**
	 * Generates a pseudo-random reply code from an sha256 HMAC of the ticket ID code
	 * and concatenates it with the ticket ID
	 *
	 * @param int $ticket_code The ticket code to generate the reply code from
	 * @param int $length The length of the reply code between 4 and 64 characters
	 * @return string A formatted reply number (e.g. "#504928 -efa3-")
	 */
	public function generateReplyNumber($ticket_code, $length = 4) {
		// Format of ticket number #NUM -CODE-
		// For example: #504928 -efa3-
		
		$code = $this->generateReplyCode($ticket_code, $length);
		return "#" . $ticket_code . " -" . $code . "-";
	}
	
	/**
	 * Sends ticket updated/received emails
	 *
	 * @param int $reply_id The ID of the ticket reply that the email is to use
	 * @param array $additional_tags A key=>value list of the email_action=>tags array to send
	 * 	e.g. array('SupportManagerpro.ticket_received' => array('tag' => "value"))
	 */
	public function sendEmail($reply_id, $additional_tags = array()) {
		// Fetch the associated ticket
		$fields = array("support_ticketspro.*", 'support_repliespro.id' => "reply_id",
			'support_repliespro.staff_id' => "reply_staff_id",
			'support_repliespro.type' => "reply_type", "support_repliespro.details",
			'support_repliespro.date_added' => "reply_date_added",
			'support_departmentspro.id' => "department_id", 'support_departmentspro.company_id' => "company_id",
			'support_departmentspro.name' => "department_name", 'support_departmentspro.email' => "department_email",
			"support_departmentspro.override_from_email");
		$ticket = $this->Record->select($fields)->
			select(array('IF(staff.id IS NULL, IF(support_ticketspro.email IS NULL, ?, ?), ?)' => "reply_by"), false)->
			appendValues(array("client", "email", "staff"))->
			from("support_repliespro")->
			innerJoin("support_ticketspro", "support_ticketspro.id", "=", "support_repliespro.ticket_id", false)->
			innerJoin("support_departmentspro", "support_departmentspro.id", "=", "support_ticketspro.department_id", false)->
			leftJoin("clients", "clients.id", "=", "support_ticketspro.client_id", false)->
				on("contacts.contact_type", "=", "primary")->
			leftJoin("contacts", "contacts.client_id", "=", "clients.id", false)->
			leftJoin("staff", "staff.id", "=", "support_repliespro.staff_id", false)->
			where("support_repliespro.id", "=", $reply_id)->
			fetch();
		
		// Only send email if the ticket is a reply type
		if ($ticket && $ticket->reply_type == "reply") {
			// Determine whether this is the only reply or not
			$total_replies = $this->Record->select(array("support_repliespro.id"))->from("support_ticketspro")->
				innerJoin("support_repliespro", "support_repliespro.ticket_id", "=", "support_ticketspro.id", false)->
				where("support_ticketspro.id", "=", $ticket->id)->
				numResults();
			
			// Set status/priority language
			$priorities = $this->getPriorities();
			$statuses = $this->getStatuses();
			$ticket->priority_language = $priorities[$ticket->priority];
			$ticket->status_language = $statuses[$ticket->status];
			
			// Parse details into HTML for HTML templates
			Loader::loadHelpers($this, array("TextParser"));
			$ticket->details_html = $this->TextParser->encode("markdown", $ticket->details);
			
			// Send the ticket emails
			$this->sendTicketEmail($ticket, ($total_replies == 1), $additional_tags);
		}
	}
	
	/**
	 * Sends ticket emails
	 *
	 * @param stdClass $ticket An stdClass object representing the ticket
	 * @param boolean $new_ticket True if this is the first ticket reply, false if it is a reply to an existing ticket
	 * @param array $additional_tags A key=>value list of the email_action=>tags array to send
	 * 	e.g. array('SupportManagerpro.ticket_received' => array('tag' => "value"))
	 */
	private function sendTicketEmail($ticket, $new_ticket, $additional_tags = array()) {
		switch ($ticket->reply_by) {
			case "staff":
				$this->sendTicketByStaffEmail($ticket, $additional_tags);
				break;
			case "email":
			case "client":
				$this->sendTicketByClientEmail($ticket, $new_ticket, $additional_tags);
				break;
			default:
				break;
		}
	}
	
	/**
	 * Sends a ticket received notice to a client for a new ticket
	 *
	 * @param stdClass $ticket An stdClass object representing the ticket
	 * @param array $additional_tags A key=>value list of the email_action=>tags array to send
	 * 	e.g. array('SupportManagerpro.ticket_received' => array('tag' => "value"))
	 */
	private function sendTicketReceived($ticket, $additional_tags = array()) {
		Loader::loadModels($this, array("Clients", "Emails"));
		
		// Set options for the email
		$options = array(
			'to_client_id' => $ticket->client_id,
			'from_staff_id' => null,
			'reply_to' => $ticket->department_email
		);
		
		$to_email = $ticket->email;
		if ($ticket->client_id > 0) {
			$client = $this->Clients->get($ticket->client_id);
			if ($client)
				$to_email = $client->email;
		}
		$language = (isset($client->settings['language']) ? $client->settings['language'] : null);
		
		$email_action = "SupportManagerpro.ticket_received";
		
		// Set the tags
		$other_tags = (isset($additional_tags[$email_action]) ? $additional_tags[$email_action] : array());
		$tags = array_merge(array('ticket' => $ticket, 'ticket_hash_code' => $this->generateReplyNumber($ticket->code, 4)), $other_tags);
		$this->Emails->send($email_action, $ticket->company_id, $language, $to_email, $tags, null, null, null, $options);
	}
	
	/**
	 * Sends the ticket updated email to staff regarding a ticket created/updated by a client.
	 * In the case $new_ticket is true, a ticket received notice is also sent to the client.
	 *
	 * @param stdClass $ticket An stdClass object representing the ticket
	 * @param boolean $new_ticket True if this is the first ticket reply, false if it is a reply to an existing ticket
	 * @param array $additional_tags A key=>value list of the email_action=>tags array to send
	 * 	e.g. array('SupportManagerpro.ticket_received' => array('tag' => "value"))
	 */
	private function sendTicketByClientEmail($ticket, $new_ticket, $additional_tags = array()) {
		Loader::loadModels($this, array("Emails", "SupportManagerpro.SupportManagerproStaff"));
		
		// Send the ticket received notification to the client
		if ($new_ticket) {
			$this->sendTicketReceived($ticket, $additional_tags);
		}
		
		// Set the date/time that each staff member must be available to receive notices
		$day = strtolower($this->dateToUtc($ticket->reply_date_added . "Z", "D"));
		$time = $this->dateToUtc($ticket->reply_date_added . "Z", "H:i:s");
		
		// Fetch all staff available to receive notifications at this time
		$staff = $this->SupportManagerproStaff->getAllAvailable($ticket->company_id, $ticket->department_id, array($day => $time));
		
		$to_addresses = array();
		$to_mobile_addresses = array();
		
		// Check each staff member is set to receive the notice
		foreach ($staff as $member) {
			// Determine whether this staff is set to receive the ticket email
			if (isset($member->settings['ticket_emails']) && is_array($member->settings['ticket_emails'])) {
				foreach ($member->settings['ticket_emails'] as $priority => $enabled) {
					if ($enabled == "true" && $ticket->priority == $priority) {
						$to_addresses[] = $member->email;
						break;
					}
				}
			}
			
			// Determine whether this staff is set to receive the ticket mobile email
			if (!empty($member->email_mobile) && isset($member->settings['mobile_ticket_emails']) && is_array($member->settings['mobile_ticket_emails'])) {
				foreach ($member->settings['mobile_ticket_emails'] as $priority => $enabled) {
					if ($enabled == "true" && $ticket->priority == $priority) {
						$to_mobile_addresses[] = $member->email_mobile;
						break;
					}
				}
			}
		}
		
		$options = array(
			'to_client_id' => null,
			'from_staff_id' => null,
			'reply_to' => $ticket->department_email
		);
		
		// Set the template from address to the departments'
		if (property_exists($ticket, "override_from_email") && $ticket->override_from_email == 1)
			$options['from'] = $ticket->department_email;
		
		// Set the tags
		$ticket_hash_code = $this->generateReplyNumber($ticket->code, 6);
		$email_action = "SupportManagerpro.staff_ticket_updated";
		$other_tags = (isset($additional_tags[$email_action]) ? $additional_tags[$email_action] : array());
		$tags = array_merge(array('ticket' => $ticket, 'ticket_hash_code' => $ticket_hash_code), $other_tags);
		
		// Send the staff ticket updated emails
		foreach ($to_addresses as $key => $address)
			$this->Emails->send($email_action, $ticket->company_id, null, $address, $tags, null, null, null, $options);
		
		// Set the tags
		$email_action = "SupportManagerpro.staff_ticket_updated_mobile";
		$other_tags = (isset($additional_tags[$email_action]) ? $additional_tags[$email_action] : array());
		$tags = array_merge(array('ticket' => $ticket, 'ticket_hash_code' => $ticket_hash_code), $other_tags);
		
		// Send the staff ticket updated mobile emails
		foreach ($to_mobile_addresses as $key => $address)
			$this->Emails->send($email_action, $ticket->company_id, null, $address, $tags, null, null, null, $options);
	}
	
	/**
	 * Sends the ticket email to a client regarding a ticket created/updated by a staff member
	 *
	 * @param stdClass $ticket An stdClass object representing the ticket
	 * @param array $additional_tags A key=>value list of the email_action=>tags array to send
	 * 	e.g. array('SupportManagerpro.ticket_received' => array('tag' => "value"))
	 */
	private function sendTicketByStaffEmail($ticket, $additional_tags = array()) {
		Loader::loadModels($this, array("Clients", "Emails"));
		
		// Fetch client to set email language
		$to_email = $ticket->email;
		if ($ticket->client_id > 0) {
			$client = $this->Clients->get($ticket->client_id);
			if ($client)
				$to_email = $client->email;
		}
		$language = (isset($client->settings['language']) ? $client->settings['language'] : null);
		
		$email_action = "SupportManagerpro.ticket_updated";
		
		// Send the email to the client
		$other_tags = (isset($additional_tags[$email_action]) ? $additional_tags[$email_action] : array());
		$tags = array_merge(array('ticket' => $ticket, 'ticket_hash_code' => $this->generateReplyNumber($ticket->code, 4)), $other_tags);
		$options = array(
			'to_client_id' => $ticket->client_id,
			'from_staff_id' => null,
			'reply_to' => $ticket->department_email
		);
		
		// Set the template from address to the departments'
		if (property_exists($ticket, "override_from_email") && $ticket->override_from_email == 1)
			$options['from'] = $ticket->department_email;
		
		$this->Emails->send($email_action, $ticket->company_id, $language, $to_email, $tags, null, null, null, $options);
	}
	
	/**
	 * Checks whether a particular email address has received more than $count emails
	 * in the last $time_limit seconds
	 *
	 * @param string $email The email address to check
	 * @param int $count The maximum number of allowed emails within the time limit
	 * @param string $time_limit The time length in the past (e.g. "5 minutes")
	 * @return boolean True if the email has received <= $count emails since $time_limit, false otherwise
	 */
	public function checkLoopBack($email, $count, $time_limit) {
		// Fetch the number of emails sent to the email address recently
		$past_date = $this->dateToUtc(strtotime(date("c") . " -" . $time_limit));
		$emails_sent = $this->Record->select()->from("log_emails")->
			where("from_address", "=", $email)->
			where("date_sent", ">=", $past_date)->
			numResults();
		
		if ($emails_sent <= $count)
			return true;
		return false;
	}
	
	/**
	 * Validates that the given reply code is correct for the ticket ID code
	 *
	 * @param int $ticket_code The ticket code to validate the reply code for
	 * @return boolean True if the reply code is valid, false otherwise
	 */
	public function validateReplyCode($ticket_code, $code) {
		$hash = $this->systemHash($ticket_code);
		return strpos($hash, $code) !== false;
	}
	
	/**
	 * Retrieves a list of rules for adding/editing support ticket replies
	 *
	 * @param array $vars A list of input vars
	 * @param boolean $new_ticket True to get the rules if this ticket is in the process of being created, false otherwise (optional, default false)
	 * @return array A list of ticket reply rules
	 */
	private function getReplyRules(array $vars, $new_ticket = false) {
		$rules = array(
			'staff_id' => array(
				'exists' => array(
					'if_set' => true,
					'rule' => array(array($this, "validateStaffExists")),
					'message' => $this->_("SupportManagerproTickets.!error.staff_id.exists")
				)
			),
			'type' => array(
				'format' => array(
					'if_set' => true,
					'rule' => array("in_array", array_keys($this->getReplyTypes())),
					'message' => $this->_("SupportManagerproTickets.!error.type.format")
				)
			),
			'details' => array(
				'empty' => array(
					'rule' => "isEmpty",
					'negate' => true,
					'message' => $this->_("SupportManagerproTickets.!error.details.empty")
				)
			),
			'date_added' => array(
				'format' => array(
					'rule' => true,
					'message' => "",
					'post_format' => array(array($this, "dateToUtc"))
				)
			)
		);
		
		if ($new_ticket) {
			// The reply type must be 'reply' on a new ticket
			$rules['type']['new_valid'] = array(
				'if_set' => true,
				'rule' => array("compares", "==", "reply"),
				'message' => $this->_("SupportManagerproTickets.!error.type.new_valid")
			);
		}
		else {
			// Validate ticket exists
			$rules['ticket_id'] = array(
				'exists' => array(
					'rule' => array(array($this, "validateExists"), "id", "support_ticketspro"),
					'message' => $this->_("SupportManagerproTickets.!error.ticket_id.exists")
				)
			);
			// Validate client can reply to this ticket
			$rules['client_id'] = array(
				'attached_to' => array(
					'if_set' => true,
					'rule' => array(array($this, "validateClientTicket"), $this->ifSet($vars['ticket_id'])),
					'message' => $this->_("SupportManagerproTickets.!error.client_id.attached_to")
				)
			);
		}
		
		return $rules;
	}
	
	/**
	 * Retrieves a list of rules for adding/editing support tickets
	 *
	 * @param array $vars A list of input vars
	 * @param boolean $edit True to get the edit rules, false for the add rules (optional, default false)
	 * @param boolean $require_email True to require the email field be given, false otherwise (optional, default false)
	 * @return array A list of support ticket rules
	 */
	private function getRules(array $vars, $edit = false, $require_email = false) {
		$rules = array(
			'code' => array(
				'format' => array(
					'rule' => array("matches", "/^[0-9]+$/"),
					'message' => $this->_("SupportManagerproTickets.!error.code.format")
				)
			),
			'department_id' => array(
				'exists' => array(
					'rule' => array(array($this, "validateExists"), "id", "support_departmentspro"),
					'message' => $this->_("SupportManagerproTickets.!error.department_id.exists")
				)
			),
			'staff_id' => array(
				'exists' => array(
					'if_set' => true,
					'rule' => array(array($this, "validateStaffExists")),
					'message' => $this->_("SupportManagerproTickets.!error.staff_id.exists")
				)
			),
			'service_id' => array(
				'exists' => array(
					'if_set' => true,
					'rule' => array(array($this, "validateExists"), "id", "services"),
					'message' => $this->_("SupportManagerproTickets.!error.service_id.exists")
				),
				'belongs' => array(
					'if_set' => true,
					'rule' => array(array($this, "validateClientService"), $this->ifSet($vars['client_id'])),
					'message' => $this->_("SupportManagerproTickets.!error.service_id.belongs")
				)
			),
			'client_id' => array(
				'exists' => array(
					'if_set' => true,
					'rule' => array(array($this, "validateExists"), "id", "clients"),
					'message' => $this->_("SupportManagerproTickets.!error.client_id.exists")
				)
			),
			'email' => array(
				'format' => array(
					'rule' => array(array($this, "validateEmail"), $require_email),
					'message' => $this->_("SupportManagerproTickets.!error.email.format")
				)
			),
			'summary' => array(
				'empty' => array(
					'rule' => "isEmpty",
					'negate' => true,
					'message' => $this->_("SupportManagerproTickets.!error.summary.empty")
				),
				'length' => array(
					'rule' => array("maxLength", 255),
					'message' => $this->_("SupportManagerproTickets.!error.summary.length")
				)
			),
			'priority' => array(
				'format' => array(
					'if_set' => true,
					'rule' => array("in_array", array_keys($this->getPriorities())),
					'message' => $this->_("SupportManagerproTickets.!error.priority.format")
				)
			),
			'status' => array(
				'format' => array(
					'if_set' => true,
					'rule' => array("in_array", array_keys($this->getStatuses())),
					'message' => $this->_("SupportManagerproTickets.!error.status.format")
				)
			),
			'date_added' => array(
				'format' => array(
					'rule' => true,
					'message' => "",
					'post_format' => array(array($this, "dateToUtc"))
				)
			)
		);
		
		if ($edit) {
			// Remove unnecessary rules
			unset($rules['date_added']);
			
			// Require that a client ID not be set
			$rules['client_id']['set'] = array(
				'rule' => array(array($this, "validateTicketUnassigned"), $this->ifSet($vars['ticket_id'])),
				'message' => Language::_("SupportManagerproTickets.!error.client_id.set", true)
			);
			
			// Set edit-specific rules
			$rules['date_closed'] = array(
				'format' => array(
					'rule' => array(array($this, "validateDateClosed")),
					'message' => $this->_("SupportManagerproTickets.!error.date_closed.format"),
					'post_format' => array(array($this, "dateToUtc"))
				)
			);
			
			// Set all rules to optional
			$rules = $this->setRulesIfSet($rules);
			
			// Require a ticket be given
			$rules['ticket_id'] = array(
				'exists' => array(
					'rule' => array(array($this, "validateExists"), "id", "support_ticketspro"),
					'message' => $this->_("SupportManagerproTickets.!error.ticket_id.exists")
				)
			);
		}
		
		return $rules;
	}
	
	/**
	 * Validates whether the given client can reply to the given ticket
	 *
	 * @param int $client_id The ID of the client
	 * @param int $ticket_id The ID of the ticket
	 * @return boolean True if the client can reply to the ticket, false otherwise
	 */
	public function validateClientTicket($client_id, $ticket_id) {
		// Ensure this client is assigned this ticket
		$results = $this->Record->select("id")->from("support_ticketspro")->
			where("id", "=", $ticket_id)->where("client_id", "=", $client_id)->
			numResults();
		
		return ($results > 0);
	}
	
	/**
	 * Validates that the given client can be assigned to the given ticket
	 *
	 * @param int $client_id The ID of the client to assign to the ticket
	 * @param int $ticket_id The ID of the ticket
	 * @return boolean True if the client may be assigned to the ticket, false otherwise
	 */
	public function validateTicketUnassigned($client_id, $ticket_id) {
		// Fetch the ticket
		$ticket = $this->get($ticket_id, false);
		
		// No ticket found, ignore this error
		if (!$ticket)
			return true;
		
		// Ticket may have either no client, or this client
		if ($ticket->client_id === null || $ticket->client_id == $client_id) {
			// Client must also be in the same company as the ticket
			$count = $this->Record->select(array("client_groups.id"))->
				from("client_groups")->
				innerJoin("clients", "clients.client_group_id", "=", "client_groups.id", false)->
				where("clients.id", "=", $client_id)->
				where("client_groups.company_id", "=", $ticket->company_id)->
				numResults();
			
			if ($count > 0)
				return true;
		}
		return false;
	}
	
	/**
	 * Validates that the given staff ID exists when adding/editing tickets
	 *
	 * @param int $staff_id The ID of the staff member
	 * @return boolean True if the staff ID exists, false otherwise
	 */
	public function validateStaffExists($staff_id) {
		// Staff ID 0 indicates a system-level ID
		if ($staff_id == "" || $staff_id == "0" || $this->validateExists($staff_id, "id", "staff", false))
			return true;
		return false;
	}
	
	/**
	 * Validates that the given service ID is assigned to the given client ID
	 *
	 * @param int $service_id The ID of the service
	 * @param int $client_id The ID of the client
	 * @return boolean True if the service ID belongs to the client ID, false otherwise
	 */
	public function validateClientService($service_id, $client_id) {
		$count = $this->Record->select()->from("services")->
			where("id", "=", $service_id)->
			where("client_id", "=", $client_id)->
			numResults();
		
		return ($count > 0);
	}
	
	/**
	 * Validates the email address given for support tickets
	 *
	 * @param string $email The support ticket email address
	 * @param boolean $require_email True to require the email field be given, false otherwise (optional, default false)
	 * @return boolean True if the email address is valid, false otherwise
	 */
	public function validateEmail($email, $require_email = false) {
		return (empty($email) && !$require_email ? true : $this->Input->isEmail($email));
	}
	
	/**
	 * Validates the date closed for support tickets
	 *
	 * @param string $date_closed The date a ticket is closed
	 * @return boolean True if the date is in a valid format, false otherwise
	 */
	public function validateDateClosed($date_closed) {
		return (empty($date_closed) ? true : $this->Input->isDate($date_closed));
	}
	
	/**
	 * Generates a ticket number
	 *
	 * @return int A ticket number
	 */
	private function generateCode() {
		// Determine the number of digits to contain in the ticket number
		$digits = (int)Configure::get("SupportManagerpro.ticket_code_length");
		$min = str_pad("1", $digits, "1");
		$max = str_pad("9", $digits, "9");
		
		// Attempt to generate a ticket code without duplicates 3 times
		// and accepts the third ticket code regardless of duplication
		$attempts = 0;
		$ticket_code = "";
		while ($attempts++ < 3) {
			$ticket_code = mt_rand($min, $max);

			// Skip if this ticket already exists
			if ($this->validateExists($ticket_code, "code", "support_ticketspro"))
				continue;
			return $ticket_code;
		}
		return $ticket_code;
	}

	/**
	 * Change a ticket status to Close and logs that it has been closed
	 *
	 * @param int $ticket_id The ID of the ticket to add to spam status
	 * @param int $staff_id The ID of the staff that do the action
	 */
	public function ClosedStatus($ticket_id, $staff_id = null) {
		// Set vars
		$statuses = $this->getStatuses();
		$vars = array(
			'details' => Language::_("SupportManagerproTickets.log.status", true, $statuses['closed']),
			'type' => "log"
		);
		// Begin a transaction
		$this->Record->begin();
        $errors = $this->errors();
		// Update the ticket to close
		$this->edit($ticket_id, array('status' => "closed", 'date_closed' => date("c")));
		$edit_errors = $this->errors();
		$errors = array_merge(($errors ? $errors : array()), ($edit_errors ? $edit_errors : array()));
		if ($errors)
			$this->Record->rollBack();
		else
			$this->Record->commit();
	}

	/**
	 * Change a ticket status to Spam and logs that it has been closed
	 *
	 * @param int $ticket_id The ID of the ticket to add to spam status
	 * @param int $staff_id The ID of the staff that do the action
	 */
	public function SpamStatus($ticket_id, $staff_id = null) {
		// Set vars
		$statuses = $this->getStatuses();
		$vars = array(
			'details' => Language::_("SupportManagerproTickets.log.status", true, $statuses['spam']),
			'type' => "log"
		);
		// Begin a transaction
		$this->Record->begin();
        $errors = $this->errors();
		// Update the ticket to spam
		$this->edit($ticket_id, array('status' => "spam", 'date_closed' => date("c")));
		$edit_errors = $this->errors();
		$errors = array_merge(($errors ? $errors : array()), ($edit_errors ? $edit_errors : array()));
		if ($errors)
			$this->Record->rollBack();
		else
			$this->Record->commit();
	}


	/**
	 * Change a ticket status to Deleted and logs that it has been closed
	 *
	 * @param int $ticket_id The ID of the ticket to add do delete status
	 * @param int $staff_id The ID of the staff that do the action
	 */
	public function DeletedStatus($ticket_id, $staff_id = null) {
		// Set vars
		$statuses = $this->getStatuses();
		$vars = array(
			'details' => Language::_("SupportManagerproTickets.log.status", true, $statuses['deleted']),
			'type' => "log"
		);
		// Begin a transaction
		$this->Record->begin();
        $errors = $this->errors();
		// Update the ticket to deleted
		$this->edit($ticket_id, array('status' => "deleted", 'date_closed' => date("c")));
		$edit_errors = $this->errors();
		$errors = array_merge(($errors ? $errors : array()), ($edit_errors ? $edit_errors : array()));
		if ($errors)
			$this->Record->rollBack();
		else
			$this->Record->commit();
	}


	/**
	 * Permanent Delete Tickets
	 *
	 * @param int $ticket_id The ID of the ticket to be deleted permanent
	 * @param int $staff_id The ID of the staff that do the action
	 */
	public function PurgeTickets($ticket_id, $staff_id = null) {
		// Start a transaction
		$this->Record->begin();

		// Delete all Tickts from the support system
		$this->Record->from("support_repliespro")->
			leftJoin("support_ticketspro", "support_ticketspro.id", "=", "support_repliespro.ticket_id", false)->
				on("support_attachmentspro.reply_id", "=", "support_repliespro.id", false)->
			leftJoin("support_attachmentspro", "support_attachmentspro.reply_id", "=", "support_attachmentspro.reply_id", false)->
			where("support_ticketspro.id", "=", $ticket_id)->
			delete(array("support_repliespro.*", "support_ticketspro.*", "support_attachmentspro.*"));
		// Commit the transaction
		$this->Record->commit();
	}


	/**
	 * Permanent Delete Reply
	 *
	 * @param int $reply_id The ID of the ticket to be deleted permanent
	 * @param int $staff_id The ID of the staff that do the action
	 */
	public function PurgeReply($reply_id, $staff_id = null) {
		// Start a transaction
		$this->Record->begin();

		// Delete reply from ticket
		$this->Record->from("support_repliespro")->
				on("support_attachmentspro.reply_id", "=", "support_repliespro.id", false)->
			leftJoin("support_attachmentspro", "support_attachmentspro.reply_id", "=", "support_attachmentspro.reply_id", false)->
			where("support_repliespro.id", "=", $reply_id)->
			delete(array("support_repliespro.*", "support_attachmentspro.*"));
		// Commit the transaction
		$this->Record->commit();
	}

	/**
	 * Delete Tickets when Merged into One
	 *
	 * @param int $ticket_id The ID of the ticket merged
	 * @param int $staff_id The ID of the staff that do the action
	 */
	public function PurgeOneTicket($ticket_id, $staff_id = null) {
		// Start a transaction
		$this->Record->begin();

		// Delete one Ticket from the support system
		$this->Record->from("support_ticketspro")->
			where("support_ticketspro.id", "=", $ticket_id)->
			delete();
		// Commit the transaction
		$this->Record->commit();
	}

	/**
	 * Merge Tickets
	 *
	 * @param int $ticket_id The ID of the ticket to close
	 */
	public function MergeTickets($ticket_id, $ticket_idm, $staff_id = null) {
		// Start a transaction
		$this->Record->begin();

        // Merge Tickets
        $this->Record->where("ticket_id", "=", $ticket_id)->update("support_repliespro", array("ticket_id"=>$ticket_idm));
		$this->Record->commit();

        // Delete Ticket Merged
        $this->PurgeOneTicket($ticket_id);
	}


	/**
	 * Retrieves a specific ticket by email
	 *
    **/
	public function getTicketByEmail($email, $get_replies = true, array $reply_types = null) {
		// Get the ticket
        $ntickets = 0;
		$ticket = $this->getTickets()->where("support_ticketspro.email", "=", $email)->where("support_ticketspro.status", "=", "spam")->fetch();
        $ntickets = $ticket->id;
        if ($ntickets > 0)
        return $ntickets;
	}


}
?>