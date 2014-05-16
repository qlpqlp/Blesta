<?php
/**
 * Ticket Managerpro component
 *
 * Connects to POP3/IMAP mail servers to download emails for each ticket department,
 * and creates tickets or ticket replies from provided email messages.
 * 
 * @package blesta
 * @subpackage blesta.plugins.support_managerpro.components
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class TicketManagerpro {
	
	/**
	 * @var int The maximum number of replies that may be sent within a given period of time
	 */
	private $max_reply_limit = 5;
	/**
	 * @var string Amount of time to verify replies within
	 */
	private $reply_period = "5 minutes";
	/**
	 * @var array A set of options for ticket emails, e.g. "client_uri"
	 */
	private $options = array();
	/**
	 * @var string The path to the system temp directory
	 */
	private $tmp_dir = null;
	
	/**
	 * Sets an array of options for use with processing tickets from email, e.g. "client_uri", "admin_uri", "is_cli"
	 *
	 * @param array $options A set of key/value pairs
	 */
	public function setOptions(array $options) {
		$this->options = $options;
	}
	
	/**
	 * Connects to all POP3/IMAP support departments and processes emails for
	 * each, creating or replying to tickets as necessary.
	 */
	public function processDepartmentEmails() {
		$company_id = Configure::get("Blesta.company_id");
		
		Loader::load(PLUGINDIR . "support_managerpro" . DS . "vendors" . DS . "php-imap" . DS . "ImapMailbox.php");
		Loader::load(PLUGINDIR . "support_managerpro" . DS . "vendors" . DS . "mime_mail_parser" . DS . "MimeMailParser.class.php");
		
		Loader::loadModels($this, array("SupportManagerpro.SupportManagerproDepartments"));
		
		// Download messages for reach department
		foreach ($this->SupportManagerproDepartments->getByMethod($company_id, array('pop3', 'imap')) as $department) {
			
			$host = $department->host;
			$port = $department->port;
			$user = $department->user;
			$password = $department->pass;
			$service = $department->method;
			$security = $department->security == "none" ? null : $department->security;
			$box_name = $department->box_name;
			$mark_as = $department->mark_messages;
			
			// POP3 always returns all messages, so we must delete any messages
			// we've already read to prevent them from creating duplicate tickets
			if ($service == "pop3")
				$mark_as = "deleted";
			
			$flags = array();
			$flags[] = $service;
			if ($security)
				$flags[] = $security;
			$flags[] = "novalidate-cert";
			
			$connection = "{" . $host . ($port ? ":" . $port : null) . "/" . implode("/", $flags) . "}" . imap_utf7_encode($box_name);
			
			try {
				$mailbox = new ImapMailbox($connection, $user, $password);
				
				$search_type = "ALL";
				if ($mark_as == "read")
					$search_type = "UNSEEN";
				
				foreach ($mailbox->searchMailbox($search_type) as $mail_id) {
					$email = new MimeMailParser();
					
					$email->setText($mailbox->fetchHeader($mail_id) . $mailbox->fetchBody($mail_id));
					$this->processTicketFromEmail($email, $department);
					
					if ($mark_as == "read")
						$mailbox->markMessageAsRead($mail_id);
					else
						$mailbox->deleteMessage($mail_id);
					
				}
				unset($mailbox);
			}
			catch (Exception $e) {
				// Ignore errors, continue on to the next department
			}
		}
	}
	
	/**
	 * Creates a ticket or ticket reply from an email message
	 *
	 * @param MimeMailParser $email The email object
	 * @param stdClass $department A stdClass object representing the department, null to detect the department from the TO address
	 */
	public function processTicketFromEmail(MimeMailParser $email, $department = null) {
		Loader::loadHelpers($this, array("Html"));
		
		$company_id = Configure::get("Blesta.company_id");
		
		if (!isset($this->EmailParser))
			Loader::loadComponents($this, array("SupportManagerpro.EmailParser"));
		if (!isset($this->SupportManagerproTickets))
			Loader::loadModels($this, array("SupportManagerpro.SupportManagerproTickets"));
		if (!isset($this->SupportManagerproDepartments))
			Loader::loadModels($this, array("SupportManagerpro.SupportManagerproDepartments"));
		if (!isset($this->Settings))
			Loader::loadModels($this, array("Settings"));
		
		if (!$this->tmp_dir) {
			$tmp_dir = $this->Settings->getSetting("temp_dir");
			if ($tmp_dir)
				$this->tmp_dir = $tmp_dir->value;
		}
		
        // Fech Name FROM
        $name_from = $this->EmailParser->getNameFrom($email, "from");
		// Fetch TO address
		$to = $this->EmailParser->getAddress($email, "x-original-to");
		if (empty($to))
			$to = $this->EmailParser->getAddress($email, "to");
		$to = array_unique($to);

		$from = $this->EmailParser->getAddress($email, "from");
		if (isset($from[0]))
			$from = $from[0];

        if (empty($name_from)){$name_from=$to;}

		$subject = $this->EmailParser->getSubject($email);
		$subject = $subject == "" ? Configure::get("SupportManagerpro.summary_default") : $subject;
		
		$ticket_info = $this->SupportManagerproTickets->parseTicketInfo($subject);

		$body = $this->EmailParser->getText($email);
		if ($ticket_info)
			$body = $this->cleanupBody($body);
		
		// Fetch the references to all files uploaded for this ticket
		$files = $this->EmailParser->getAttachments($email, $this->tmp_dir);
		
		// Set company hostname and client URI for ticket email
		$client_uri = (array_key_exists("client_uri", $this->options) ? $this->options['client_uri'] : WEBDIR . Configure::get("Route.client") . "/");
		$hostname = isset(Configure::get("Blesta.company")->hostname) ? Configure::get("Blesta.company")->hostname : "";
		
		// Handle ticket replies
		if ($ticket_info) {
			// Ensure ticket code is valid
			if ($ticket_info['valid']) {
				
				$ticket = $this->SupportManagerproTickets->getTicketByCode($ticket_info['ticket_code'], false);
				
				// If ticket found, record the reply
				if ($ticket) {
					$reply = array(
						'type' => "reply",
						'details' => $body,
						'staff_id' => null,
						'client_id' => null
					);
					
					// Re-open this ticket
					if ($ticket->status == "closed")
						$reply['status'] = "open";
					
					// If reply came from staff member, put staff ID
					if (($staff = $this->SupportManagerproDepartments->getStaffByEmail($ticket->department_id, $from)))
						$reply['staff_id'] = $staff->id;
					// If the reply was not from a staff member, it must have been the client
					else {
						$reply['client_id'] = $ticket->client_id;
						
						// If the ticket was previously awaiting this client's reply change it back to open
						if ($ticket->status == "awaiting_reply")
							$reply['status'] = "open";
					}
					
					// Check if only clients are allowed to reply to tickets
					if ($reply['staff_id'] == null && $reply['client_id'] == null &&
						($department = $this->SupportManagerproDepartments->get($ticket->department_id)) && $department->clients_only) {
						// Only clients are allowed to reply to tickets
						$this->sendBounceNotice($email);
						return;
					}
					
					$reply_id = $this->SupportManagerproTickets->addReply($ticket->id, $reply, $files);
					
					if (!$reply_id) {
						// Ticket reply failed
						$this->sendBounceNotice($email, $reply['client_id']);
					}
					else {
						// Don't allow reply to be sent if enough emails have been sent to this
						// address within the given window of time
						if ($this->SupportManagerproTickets->checkLoopBack($email, $this->max_reply_limit, $this->reply_period)) {
							// Send the email associated with this ticket
							$key = mt_rand();
							$hash = $this->SupportManagerproTickets->generateReplyHash($ticket->id, $key);
							$additional_tags = array('SupportManagerpro.ticket_updated' => array('update_ticket_url' => $this->Html->safe($hostname . $client_uri . "plugin/support_managerpro/client_tickets/reply/" . $ticket->id . "/?sid=" . rawurlencode($this->SupportManagerproTickets->systemEncrypt('h=' . substr($hash, -16) . "|k=" . $key)))));
							$this->SupportManagerproTickets->sendEmail($reply_id, $additional_tags);
						}
					}
				}
				else {
					// Ticket not found
					$this->sendBounceNotice($email);
					return;
				}
			}
			else {
				// Ticket code is not valid
				$this->sendBounceNotice($email);
				return;
			}
		}
		// Handle creating a new ticket
		else {
			$department_found = false;
			// Attempt to create a ticket from the first valid department
			foreach ($to as $address) {
				
				// Look up department based on to address if not given
				if (!$department)
					$department = $this->SupportManagerproDepartments->getByEmail($company_id, $address);
				
				if ($department) {
					$department_found = true;
					
					// Try to find an existing client with this from address to assign the ticket to
					$client = $this->SupportManagerproTickets->getClientByEmail($company_id, $from);
					
					$client_id = null;
					$from_email = null;
					
					if ($client)
						$client_id = $client->id;
					else
						$from_email = $from;
					
					// Check if only clients are allowed to open tickets
					if ($client_id == null && $department->clients_only) {
						// Only clients are allowed to open tickets
						$this->sendBounceNotice($email);
						return;
					}
					
                    // Check if the email adress is on SPAM database
					$found_spam = $this->SupportManagerproTickets->getTicketByEmail($from);
                    if (isset($found_spam)){
			    		$ticket_info = array(
				    		'department_id' => $department->id,
			    			'summary' => $subject,
					    	'priority' => $department->default_priority,
						    'status' => "spam"
				    	);
                    }else{
					    $ticket_info = array(
						    'department_id' => $department->id,
						    'summary' => $subject,
					    	'priority' => $department->default_priority
				    	);
                    }

						$ticket_info['name'] = $name_from;

					if ($client_id)
						$ticket_info['client_id'] = $client_id;
						$ticket_info['email'] = $from;
					if ($from_email)
						$ticket_info['email'] = $from_email;

					$ticket_id = $this->SupportManagerproTickets->add($ticket_info, ($from_email ? true : false));
					
					if (!$ticket_id) {
						// Ticket could not be added
						$this->sendBounceNotice($email, $client_id);
						return;
					}
					
					$reply = array(
						'type' => "reply",
						'details' => $body
					);
					
					if ($client_id)
						$reply['client_id'] = $client_id;

					$reply_id = $this->SupportManagerproTickets->addReply($ticket_id, $reply, $files, true);
					
					// Don't allow reply to be sent if enough emails have been sent to this
					// address within the given window of time
					if ($this->SupportManagerproTickets->checkLoopBack($email, $this->max_reply_limit, $this->reply_period)) {
						// Send the email associated with this ticket
						$key = mt_rand();
						$hash = $this->SupportManagerproTickets->generateReplyHash($ticket_id, $key);
						$additional_tags = array('SupportManagerpro.ticket_updated' => array('update_ticket_url' => $this->Html->safe($hostname . $client_uri . "plugin/support_managerpro/client_tickets/reply/" . $ticket_id . "/?sid=" . rawurlencode($this->SupportManagerproTickets->systemEncrypt('h=' . substr($hash, -16) . "|k=" . $key)))));
						$this->SupportManagerproTickets->sendEmail($reply_id, $additional_tags);
					}
					return;
				}
			}
			
			if (!$department_found) {
				// Department not found
				$this->sendBounceNotice($email);
			}
		}
	}
	
	/**
	 * Sends a bounce to sender
	 *
	 * @param MimeMailParser $email The email object that bounced
	 * @param int $client_id The ID of the client that sent the email (if any)
	 */
	private function sendBounceNotice(MimeMailParser $email, $client_id = null) {
		if (!isset($this->Emails))
			Loader::loadModels($this, array("Emails"));
		if (!isset($this->Clients))
			Loader::loadModels($this, array("Clients"));
			
		// Send email to the from address
		$to = $this->EmailParser->getAddress($email, "from");
		if (isset($to[0]))
			$to = $to[0];
		
		// Don't allow bounce to be sent if enough emails have been sent to this
		// address within the given window of time
		if (!$this->SupportManagerproTickets->checkLoopBack($to, $this->max_reply_limit, $this->reply_period))
			return;
		
		$lang = null;
		if ($client_id) {
			$client = $this->Clients->get($client_id);
			
			if ($client && $client->settings['language'])
				$lang = $client->settings['language'];
		}
		
		$tags = array();
		$options = array('to_client_id' => $client_id);
		
		$this->Emails->send("SupportManagerpro.ticket_bounce", Configure::get("Blesta.company_id"), $lang, $to, $tags, null, null, null, $options);
	}
	
	/**
	 * Clean the body of a message by removing quoted text
	 *
	 * @param string $body The body (possibly) containing quoted text
	 * @return string The body with quoted text removed
	 */
	private function cleanupBody($body) {
		return preg_replace("/^>.*?[\r\n]/m", "", $body);
	}
}
?>