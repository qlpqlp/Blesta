<?php
/**
 * Support Manager Admin Departments controller
 * 
 * @package blesta
 * @subpackage blesta.plugins.support_manager
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class AdminDepartments extends SupportManagerController {

	/**
	 * Setup
	 */
	public function preAction() {
		parent::preAction();
		
		$this->requireLogin();
		
		$this->uses(array("SupportManager.SupportManagerDepartments"));
		
		// Restore structure view location of the admin portal
		$this->structure->setDefaultView(APPDIR);
		$this->structure->setView(null, $this->orig_structure_view);
		
		$this->staff_id = $this->Session->read("blesta_staff_id");
	}
	
	/**
	 * List departments
	 */
	public function index() {
		$page = (isset($this->get[0]) ? (int)$this->get[0] : 1);
		$sort = (isset($this->get['sort']) ? $this->get['sort'] : "name");
		$order = (isset($this->get['order']) ? $this->get['order'] : "asc");
		
		$this->set("sort", $sort);
		$this->set("order", $order);
		$this->set("negate_order", ($order == "asc" ? "desc" : "asc"));
		
		$departments = $this->SupportManagerDepartments->getList($this->company_id, $page, array($sort => $order));
		$total_results = $this->SupportManagerDepartments->getListCount($this->company_id);
		
		// Overwrite default pagination settings
		$settings = array_merge(Configure::get("Blesta.pagination"), array(
				'total_results' => $total_results,
				'uri' => $this->base_uri . "plugin/support_manager/admin_departments/index/[p]/"
			)
		);
		$this->helpers(array("Pagination" => array($this->get, $settings)));
		$this->Pagination->setSettings(Configure::get("Blesta.pagination_ajax"));
		
		$this->set("departments", $departments);
		$this->set("priorities", $this->SupportManagerDepartments->getPriorities());
		
		return $this->renderAjaxWidgetIfAsync(isset($this->get[0]) || isset($this->get['sort']));
	}
	
	/**
	 * Add a department
	 */
	public function add() {
		// Create a department
		if (!empty($this->post)) {
			// Set empty checkboxes
			$checkboxes = array("clients_only", "override_from_email");
			foreach ($checkboxes as $checkbox) {
				if (!isset($this->post[$checkbox]))
					$this->post[$checkbox] = "0";
			}
			
			// Set the company ID
			$data = $this->post;
			$data['company_id'] = $this->company_id;
			
			$department = $this->SupportManagerDepartments->add($data);
			
			if (($errors = $this->SupportManagerDepartments->errors())) {
				// Error, reset vars
				$vars = (object)$this->post;
				$this->setMessage("error", $errors, false, null, false);
			}
			else {
				// Success
				$this->flashMessage("message", Language::_("AdminDepartments.!success.department_created", true, $department->name), null, false);
				$this->redirect($this->base_uri . "plugin/support_manager/admin_departments/");
			}
		}
		
		// Set default fields
		if (!isset($vars))
			$vars = (object)array('port' => 110, 'box_name' => "INBOX", 'clients_only' => "1");
		
		$this->set("vars", $vars);
		$this->set("priorities", $this->SupportManagerDepartments->getPriorities());
		$this->set("methods", $this->SupportManagerDepartments->getMethods());
		$this->set("statuses", $this->SupportManagerDepartments->getStatuses());
		$this->set("security_types", $this->SupportManagerDepartments->getSecurityTypes());
		$this->set("message_types", $this->SupportManagerDepartments->getMessageTypes());
		$this->set("piping_config", "/usr/bin/php " . realpath(dirname(__FILE__) . DS . ".." . DS) . DS . "pipe.php plugin/support_manager/ticket_pipe/index/" . $this->company_id . "/");
	}
	
	/**
	 * Edit a department
	 */
	public function edit() {
		if (!isset($this->get[0]) || !($department = $this->SupportManagerDepartments->get($this->get[0])) ||
			$this->company_id != $department->company_id)
			$this->redirect($this->base_uri . "plugin/support_manager/admin_departments/");
		
		// Update a department
		if (!empty($this->post)) {
			// Set empty checkboxes
			$checkboxes = array("clients_only", "override_from_email");
			foreach ($checkboxes as $checkbox) {
				if (!isset($this->post[$checkbox]))
					$this->post[$checkbox] = "0";
			}
				
			$department = $this->SupportManagerDepartments->edit($department->id, $this->post);
			
			if (($errors = $this->SupportManagerDepartments->errors())) {
				// Error, reset vars
				$vars = (object)$this->post;
				$this->setMessage("error", $errors, false, null, false);
			}
			else {
				// Success
				$department = $this->SupportManagerDepartments->get($department->id);
				$this->flashMessage("message", Language::_("AdminDepartments.!success.department_updated", true, $department->name), null, false);
				$this->redirect($this->base_uri . "plugin/support_manager/admin_departments/");
			}
		}
		
		// Set initial department
		if (!isset($vars))
			$vars = $department;
		
		$this->set("vars", $vars);
		$this->set("priorities", $this->SupportManagerDepartments->getPriorities());
		$this->set("methods", $this->SupportManagerDepartments->getMethods());
		$this->set("statuses", $this->SupportManagerDepartments->getStatuses());
		$this->set("security_types", $this->SupportManagerDepartments->getSecurityTypes());
		$this->set("message_types", $this->SupportManagerDepartments->getMessageTypes());
		$this->set("piping_config", "/usr/bin/php " . realpath(dirname(__FILE__) . DS . ".." . DS) . DS . "pipe.php plugin/support_manager/ticket_pipe/index/" . $this->company_id . "/");
	}
	
	/**
	 * Delete a department
	 */
	public function delete() {
		if (!isset($this->post['id']) || !($department = $this->SupportManagerDepartments->get($this->post['id'])) ||
			$this->company_id != $department->company_id)
			$this->redirect($this->base_uri . "plugin/support_manager/admin_departments/");
		
		// Attempt to delete the department
		$this->SupportManagerDepartments->delete($department->id);
		
		// Set message
		if (($errors = $this->SupportManagerDepartments->errors()))
			$this->flashMessage("error", $errors, null, false);
		else
			$this->flashMessage("message", Language::_("AdminDepartments.!success.department_deleted", true, $department->name), null, false);
		
		$this->redirect($this->base_uri . "plugin/support_manager/admin_departments/");
	}
	
	/**
	 * AJAX Retrieves staff associated with a department
	 */
	public function assignedStaff() {
		// Ensure a department ID was given
		if (!$this->isAjax() || !isset($this->get[0]) ||
			!($department = $this->SupportManagerDepartments->get($this->get[0])) ||
			$department->company_id != $this->company_id) {
			header($this->server_protocol . " 401 Unauthorized");
			exit();
		}
		
		$this->uses(array("SupportManager.SupportManagerStaff"));
		
		$vars = array(
			'staff' => $this->SupportManagerStaff->getAll($this->company_id, $department->id)
		);
		
		// Send the template
		echo $this->partial("admin_departments_assigned_staff", $vars);
		
		// Render without layout
		return false;
	}
}
?>