<?php
/**
 * SupportManagerproDepartments model
 * 
 * @package blesta
 * @subpackage blesta.plugins.support_managerpro
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class SupportManagerproDepartments extends SupportManagerproModel {
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		
		Language::loadLang("support_managerpro_departments", null, PLUGINDIR . "support_managerpro" . DS . "language" . DS);
	}
	
	/**
	 * Adds a department
	 *
	 * @param array $vars A list of input vars including:
	 * 	- company_id The company ID
	 * 	- name The department's name
	 * 	- description The department's description
	 * 	- email The department email address
	 * 	- method The method for sending email ('pipe' 'pop3', 'imap', 'none')
	 * 	- default_priority The default department ticket priority ('emergency', 'critical', 'high', 'medium', 'low')
	 * 	- host The email hostname (optional, required if method is not 'pipe')
	 * 	- user The email user (optional, required if method is not 'pipe')
	 * 	- pass The email pass (optional, required if method is not 'pipe')
	 * 	- port The email port (optional, required if method is not 'pipe')
	 * 	- security The security type (optional, required if method is not 'pipe')
	 * 	- box_name The box name type (optional)
	 * 	- mark_messages The message type (optional, required if method is not 'pipe')
	 * 	- clients_only (optional, defaults to '1')
	 * 	- override_from_email Whether or not to use the department's email address as the from address in email templates (optional, defaults to '1')
	 * 	- status The department status ('hidden' or 'visible')
	 * @return stdClass The stdClass object representing the newly-created department, or void on error
	 */
	public function add(array $vars) {
		$this->Input->setRules($this->getRules($vars));
		
		if ($this->Input->validates($vars)) {
			$fields = array("company_id", "name", "description", "email", "method",
				"default_priority", "host", "user", "pass", "port", "security",
				"box_name", "mark_messages", "clients_only", "override_from_email", "status");
			$this->Record->insert("support_departmentspro", $vars, $fields);
			
			return $this->get($this->Record->lastInsertId());
		}
	}
	
	/**
	 * Edits a department
	 *
	 * @param int $department_id The ID of the department to update
	 * @param array $vars A list of input vars including:
	 * 	- name The department's name
	 * 	- description The department's description
	 * 	- email The department email address
	 * 	- method The method for sending email ('pipe' 'pop3', 'imap', 'none')
	 * 	- default_priority The default department ticket priority ('emergency', 'critical', 'high', 'medium', 'low')
	 * 	- host The email hostname (optional, required if method is not 'pipe')
	 * 	- user The email user (optional, required if method is not 'pipe')
	 * 	- pass The email pass (optional, required if method is not 'pipe')
	 * 	- port The email port (optional, required if method is not 'pipe')
	 * 	- security The security type (optional, required if method is not 'pipe')
	 * 	- box_name The box name type (optional)
	 * 	- mark_messages The message type (optional, required if method is not 'pipe')
	 * 	- clients_only (optional, defaults to '1')
	 * 	- override_from_email Whether or not to use the department's email address as the from address in email templates (optional, defaults to '1')
	 * 	- status The department status ('hidden' or 'visible')
	 * @return stdClass The stdClass object representing the newly-created department, or void on error
	 */
	public function edit($department_id, array $vars) {
		$vars['department_id'] = $department_id;
		$this->Input->setRules($this->getRules($vars, true));
		
		if ($this->Input->validates($vars)) {
			$fields = array("name", "description", "email", "method",
				"default_priority", "host", "user", "pass", "port",  "security",
				"box_name", "mark_messages", "clients_only", "override_from_email", "status");
			$this->Record->where("id", "=", $department_id)->
				update("support_departmentspro", $vars, $fields);
			
			return $this->get($department_id);
		}
	}
	
	/**
	 * Attempts to delete a support department
	 *
	 * @param int $department_id The ID of the department to delete
	 */
	public function delete($department_id) {
		$rules = array(
			'department_id' => array(
				'has_tickets' => array(
					'rule' => array(array($this, "validateHasTickets")),
					'negate' => true,
					'message' => $this->_("SupportManagerproDepartments.!error.department_id.has_tickets")
				)
			)
		);
		
		$this->Input->setRules($rules);
		
		if ($this->Input->validates($vars)) {
			// Delete the department
			$this->Record->from("support_departmentspro")->
				where("id", "=", $department_id)->delete();
		}
	}
	
	/**
	 * Fetches a support department
	 *
	 * @param int $department_id The ID of the department to fetch
	 * @return mixed An stdClass object representing the department, or false if none exist
	 */
	public function get($department_id) {
		$fields = array("support_departmentspro.*", 'COUNT(support_staff_departmentspro.staff_id)' => "assigned_staff");
		return $this->Record->select($fields)->from("support_departmentspro")->
			leftJoin("support_staff_departmentspro", "support_staff_departmentspro.department_id", "=", "support_departmentspro.id", false)->
			where("id", "=", $department_id)->
			group("support_departmentspro.id")->
			fetch();
	}
	
	/**
	 * Retrieves a list of departments
	 *
	 * @param int $company_id The ID of the company whose department list to fetch
	 * @param int $page The page number of results to fetch (optional, default 1)
	 * @param array $order A key/value pair array of fields to order the results by
	 * @return array A list of stdClass objects, each representing a department
	 */
	public function getList($company_id, $page = 1, array $order_by = array('name' => "ASC")) {
		$this->Record = $this->getDepartments($company_id)->group("support_departmentspro.id");
		
		if ($order_by)
			$this->Record->order($order_by);
			
		return $this->Record->limit($this->getPerPage(), (max(1, $page) - 1)*$this->getPerPage())->
			fetchAll();
	}
	
	/**
	 * Retrieves the total number of departments
	 *
	 * @param int $company_id The ID of the company
	 * @return int The total number of departments
	 */
	public function getListCount($company_id) {
		return $this->getDepartments($company_id)->group("support_departmentspro.id")->numResults();
	}
	
	/**
	 * Fetches a list of all departments
	 *
	 * @param int $company_id The ID of the company whose departments to fetch
	 * @param string $status The status of the support department (i.e. "visible", "hidden") (optional, default null for all)
	 * @param boolean $clients_only True to fetch only those departments for logged-in clients, false to fetch departments not for logged-in clients, or null for all (optional, default null)
	 * @return array A list of stdClass objects, each representing a department
	 */
	public function getAll($company_id, $status = null, $clients_only=null) {
		$this->Record = $this->getDepartments($company_id);
		
		// Filter by status
		if ($status !== null)
			$this->Record->where("support_departmentspro.status", "=", $status);
		
		// Filter by client access
		if ($clients_only !== null)
			$this->Record->where("support_departmentspro.clients_only", "=", ($clients_only ? 1 : 0));
		
		return $this->Record->group("support_departmentspro.id")->fetchAll();
	}
	
	/**
	 * Fetches the support department with the given email address and (optionally) method
	 *
	 * @param int $company_id The ID of the company whose departments to fetch
	 * @param string $email The email address of the department to fetch
	 * @param string $method The method of the support department, null for any:
	 * 	- pipe
	 * 	- pop3
	 * 	- imap
	 * 	- none
	 * @return mixed A stdClass object representing the support department, false if no such department found
	 */
	public function getByEmail($company_id, $email, $method = null) {
		$this->Record = $this->getDepartments($company_id);
		
		$this->Record->where("support_departmentspro.email", "=", $email);
		
		if ($method)
			$this->Record->where("support_departmentspro.method", "=", $method);
		
		return $this->Record->fetch();
	}
	
	/**
	 * Fetches a list of all departments by given methods
	 *
	 * @param int $company_id The ID of the compane whose departments to fetch
	 * @param array $methods A list of method types to filter on (optional, null for all)
	 * @return array A list of stdClass objects, each representing a department
	 */
	public function getByMethod($company_id, array $methods=null) {
		$this->Record = $this->getDepartments($company_id);
		
		// Fetch by specific method types
		if (!empty($methods)) {
			$this->Record->open();
			
			$i = 0;
			foreach ($methods as $type) {
				if ($i++ == 0)
					$this->Record->where("method", "=", $type);
				else
					$this->Record->orWhere("method", "=", $type);
			}
			unset($i);
			
			$this->Record->close();
		}
		
		return $this->Record->fetchAll();
	}
	
	/**
	 * Fetches staff info for the staff member assigned to the given support department
	 * and who has the given email address set as their email or mobile email address
	 *
	 * @param int $department_id The support department ID the staff member must belong to
	 * @param string $email The email address the staff member must be assigned
	 * @return mixed A stdClass object representing the staff member, false if the staff member does not exist, is not active, or does not belong to the department
	 */
	public function getStaffByEmail($department_id, $email) {
		return $this->Record->select(array("staff.*"))->
			from("support_staff_departmentspro")->
			innerJoin("staff", "staff.id", "=", "support_staff_departmentspro.staff_id", false)->
			where("staff.status", "=", "active")->
			open()->
				where("staff.email", "=", $email)->
				orWhere("staff.email_mobile", "=", $email)->
			close()->
			where("support_staff_departmentspro.department_id", "=", $department_id)->fetch();
	}
	
	/**
	 * Retrieves a partially-constructed Record object for fetching departments
	 *
	 * @param int $company_id The ID of the company whose departments to fetch
	 * @return Record A partially-constructed Record object
	 */
	private function getDepartments($company_id) {
		$fields = array("support_departmentspro.*", 'COUNT(support_staff_departmentspro.staff_id)' => "assigned_staff");
		return $this->Record->select($fields)->from("support_departmentspro")->
			leftJoin("support_staff_departmentspro", "support_staff_departmentspro.department_id", "=", "support_departmentspro.id", false)->
			where("support_departmentspro.company_id", "=", $company_id)->group(array("support_departmentspro.id"));
	}
	
	/**
	 * Retrieves a list of department methods
	 *
	 * @return array A list of methods and their language
	 */
	public function getMethods() {
		return array(
			'none' => $this->_("SupportManagerproDepartments.methods.none"),
			'pipe' => $this->_("SupportManagerproDepartments.methods.pipe"),
			'pop3' => $this->_("SupportManagerproDepartments.methods.pop3"),
			'imap' => $this->_("SupportManagerproDepartments.methods.imap")
		);
	}
	
	/**
	 * Retrieves a list of department statuses
	 *
	 * @return array A list of statuses and their language
	 */
	public function getStatuses() {
		return array(
			'visible' => $this->_("SupportManagerproDepartments.statuses.visible"),
			'hidden' => $this->_("SupportManagerproDepartments.statuses.hidden")
		);
	}
	
	/**
	 * Retrieves a list of department priorities
	 *
	 * @return array A list of priorities and their language
	 */
	public function getPriorities() {
		return array(
			'emergency' => $this->_("SupportManagerproDepartments.priorities.emergency"),
			'critical' => $this->_("SupportManagerproDepartments.priorities.critical"),
			'high' => $this->_("SupportManagerproDepartments.priorities.high"),
			'medium' => $this->_("SupportManagerproDepartments.priorities.medium"),
			'low' => $this->_("SupportManagerproDepartments.priorities.low")
		);
	}
	
	/**
	 * Retrieves a list of security types
	 *
	 * @return array A list of security types and their language
	 */
	public function getSecurityTypes() {
		return array(
			'none' => $this->_("SupportManagerproDepartments.security_types.none"),
			'ssl' => $this->_("SupportManagerproDepartments.security_types.ssl"),
			'tls' => $this->_("SupportManagerproDepartments.security_types.tls")
		);
	}
	
	/**
	 * Retrieves a list of message types
	 *
	 * @return array A list of message types and their language
	 */
	public function getMessageTypes() {
		return array(
			'read' => $this->_("SupportManagerproDepartments.message_types.read"),
			'deleted' => $this->_("SupportManagerproDepartments.message_types.deleted")
		);
	}
	
	/**
	 * Fetches a list of rules for adding/editing a department
	 *
	 * @param array $vars A list of input vars
	 * @param boolean $edit True to get the edit rules, false for the add rules (optional, default false)
	 */
	private function getRules(array $vars, $edit = false) {
		$rules = array(
			'company_id' => array(
				'exists' => array(
					'rule' => array(array($this, "validateExists"), "id", "companies"),
					'message' => $this->_("SupportManagerproDepartments.!error.company_id.exists")
				)
			),
			'name' => array(
				'empty' => array(
					'rule' => "isEmpty",
					'negate' => true,
					'message' => $this->_("SupportManagerproDepartments.!error.name.empty")
				)
			),
			'description' => array(
				'empty' => array(
					'rule' => "isEmpty",
					'negate' => true,
					'message' => $this->_("SupportManagerproDepartments.!error.description.empty")
				)
			),
			'email' => array(
				'format' => array(
					'rule' => "isEmail",
					'message' => $this->_("SupportManagerproDepartments.!error.email.format")
				)
			),
			'method' => array(
				'format' => array(
					'if_set' => true,
					'rule' => array("in_array", array_keys($this->getMethods())),
					'message' => $this->_("SupportManagerproDepartments.!error.method.format")
				),
				'imap' => array(
					'if_set' => true,
					'rule' => array(array($this, "validateImapRequired")),
					'message' => $this->_("SupportManagerproDepartments.!error.method.imap")
				),
				'mailparse' => array(
					'if_set' => true,
					'rule' => array(array($this, "validateMailparseRequired")),
					'message' => $this->_("SupportManagerproDepartments.!error.method.mailparse")
				)
			),
			'default_priority' => array(
				'format' => array(
					'if_set' => true,
					'rule' => array("in_array", array_keys($this->getPriorities())),
					'message' => $this->_("SupportManagerproDepartments.!error.default_priority.format")
				)
			),
			'host' => array(
				'format' => array(
					'rule' => array(array($this, "validateHost"), $this->ifSet($vars['method'])),
					'message' => $this->_("SupportManagerproDepartments.!error.host.format")
				),
				'length' => array(
					'rule' => array("maxLength", 128),
					'message' => $this->_("SupportManagerproDepartments.!error.host.length")
				)
			),
			'user' => array(
				'format' => array(
					'rule' => array(array($this, "validateEmailCredential"), $this->ifSet($vars['method'])),
					'message' => $this->_("SupportManagerproDepartments.!error.user.format")
				),
				'length' => array(
					'rule' => array("maxLength", 64),
					'message' => $this->_("SupportManagerproDepartments.!error.user.length")
				)
			),
			'pass' => array(
				'format' => array(
					'rule' => array(array($this, "validateEmailCredential"), $this->ifSet($vars['method'])),
					'message' => $this->_("SupportManagerproDepartments.!error.password.format")
				),
				'length' => array(
					'rule' => array("maxLength", 64),
					'message' => $this->_("SupportManagerproDepartments.!error.password.length")
				)
			),
			'port' => array(
				'format' => array(
					'rule' => array(array($this, "validatePort"), $this->ifSet($vars['method'])),
					'message' => $this->_("SupportManagerproDepartments.!error.port.format")
				),
				'length' => array(
					'rule' => array("maxLength", 6),
					'message' => $this->_("SupportManagerproDepartments.!error.port.length")
				)
			),
			'security' => array(
				'format' => array(
					'rule' => array(array($this, "validateSecurityType"), $this->ifSet($vars['method'])),
					'message' => $this->_("SupportManagerproDepartments.!error.security.format")
				)
			),
			'box_name' => array(
				'format' => array(
					'if_set' => true,
					'rule' => true,
					'message' => "",
					'post_format' => array(array($this, "getBoxName"))
				)
			),
			'mark_messages' => array(
				'format' => array(
					'rule' => array(array($this, "validateMessageType"), $this->ifSet($vars['method'])),
					'message' => $this->_("SupportManagerproDepartments.!error.mark_messages.format")
				),
				'valid' => array(
					'rule' => array(array($this, "validatePopMessageType"), $this->ifSet($vars['method'])),
					'message' => $this->_("SupportManagerproDepartments.!error.mark_messages.valid")
				)
			),
			'clients_only' => array(
				'format' => array(
					'if_set' => true,
					'rule' => array("in_array", array(0,1)),
					'message' => $this->_("SupportManagerproDepartments.!error.clients_only.format")
				)
			),
			'override_from_email' => array(
				'format' => array(
					'if_set' => true,
					'rule' => array("in_array", array(0,1)),
					'message' => $this->_("SupportManagerproDepartments.!error.override_from_email.format")
				)
			),
			'status' => array(
				'format' => array(
					'rule' => array("in_array", array_keys($this->getStatuses())),
					'message' => $this->_("SupportManagerproDepartments.!error.status.format")
				)
			)
		);
		
		if ($edit) {
			// Remove unnecessary rules
			unset($rules['company_id']);
			
			// Set all rules to optional
			$rules = $this->setRulesIfSet($rules);
			
			// Require a valid department ID
			$rules['department_id'] = array(
				'exists' => array(
					'rule' => array(array($this, "validateExists"), "id", "support_departmentspro"),
					'message' => $this->_("SupportManagerproDepartments.!error.department_id.exists")
				)
			);
		}
		
		return $rules;
	}
	
	/**
	 * Gets the box name to use for the support department based on input
	 *
	 * @param mixed $box_name The box name to use for the department
	 * @param string $default_box The default box name to use if $box_name is empty
	 * @return string The box name to use for the department
	 */
	public function getBoxName($box_name, $default_box = "INBOX") {
		return (empty($box_name) ? $default_box : $box_name);
	}
	
	/**
	 * Validates the host based on the method
	 *
	 * @param string $host The hostname
	 * @param string $method The email method
	 *
	 * @return boolean True if the host validates, false otherwise
	 */
	public function validateHost($host, $method) {
		// Host must be set if pipe is not set
		if ($method != "pipe" && $method != "none" && empty($host))
			return false;
		
		return (empty($host) || $this->Input->matches($host, "/^([a-z0-9]|[a-z0-9][a-z0-9\-]{0,61}[a-z0-9])(\.([a-z0-9]|[a-z0-9][a-z0-9\-]{0,61}[a-z0-9]))+$/i"));
	}
	
	/**
	 * Validates the username or password based on the method
	 *
	 * @param string $field The field to validate
	 * @param string $method The email method
	 *
	 * @return boolean True if the field validates, false otherwise
	 */
	public function validateEmailCredential($field, $method) {
		// Host must be set if pipe is not set
		if ($method != "pipe" && $method != "none" && empty($field))
			return false;
		
		return true;
	}
	
	/**
	 * Validates the port based on the method
	 *
	 * @param string $port The port number
	 * @param string $method The email method
	 *
	 * @return boolean True if the port validates, false otherwise
	 */
	public function validatePort($port, $method) {
		// Host must be set if pipe is not set
		if ($method != "pipe" && $method != "none" && empty($port))
			return false;
		
		return (empty($port) || $this->Input->matches($port, "/^[0-9]+$/"));
	}
	
	/**
	 * Validates the security based on the method
	 *
	 * @param string $security The security type
	 * @param string $method The email method
	 * @return boolean True if the security type validates, false otherwise
	 */
	public function validateSecurityType($security, $method) {
		// Host must be set if pipe is not set
		if ($method != "pipe" && $method != "none" && empty($security))
			return false;
		
		return in_array($security, array_keys($this->getSecurityTypes()));
	}
	
	/**
	 * Validates the mark_messages type based on the method
	 *
	 * @param string $mark_messages The status of messages
	 * @param string $method The email method
	 * @return boolean True if the message type validates, false otherwise
	 */
	public function validateMessageType($mark_messages, $method) {
		// Host must be set if pipe is not set
		if ($method != "pipe" && $method != "none" && empty($mark_messages))
			return false;
		
		return in_array($mark_messages, array_keys($this->getMessageTypes()));
	}
	
	/**
	 * Validates the mark_messages type to ensure it is a valid type based on the method
	 *
	 * @param string $mark_messages The status of messages
	 * @param string $method The method type
	 * @return boolean True if the message type validates, false otherwise
	 */
	public function validatePopMessageType($mark_messages, $method) {
		// POP3 may only have messages set to 'deleted'
		if ($method == "pop3" && $mark_messages != "deleted")
			return false;
		return true;
	}
	
	/**
	 * Validates whether the given department has tickets assigned to it
	 *
	 * @param int $department_id The ID of the department
	 * @return boolean True if the department has tickets assigned to it, false otherwise
	 */
	public function validateHasTickets($department_id) {
		$num_tickets = $this->Record->select("support_ticketspro.*")->from("support_departmentspro")->
			innerJoin("support_ticketspro", "support_ticketspro.department_id", "=", "support_departmentspro.id", false)->
			where("support_departmentspro.id", "=", $department_id)->
			numResults();
		
		if ($num_tickets > 0)
			return true;
		return false;
	}

	/**
	 * Validates that the imap extension exists
	 *
	 * @param string $method The email handling method ('none', 'pipe', 'imap', 'pop3')
	 * @return boolean True if the imap extension exists or is not required for the given $method
	 */	
	public function validateImapRequired($method) {
		if ($method != "none" && $method != "pipe") {
			// imap extension required
			return extension_loaded("imap");
		}
		return true;
	}
	
	/**
	 * Validates that the mailparse extension exists
	 *
	 * @param string $method The email handling method ('none', 'pipe', 'imap', 'pop3')
	 * @return boolean True if the mailparse extension exists or is not required for the given $method
	 */
	public function validateMailparseRequired($method) {
		if ($method != "none") {
			// mailparse extension required
			return extension_loaded("mailparse");
		}
		return true;
	}
}
?>