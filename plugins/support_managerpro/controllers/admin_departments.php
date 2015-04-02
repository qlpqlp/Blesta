<?php
/**
 * Support Managerpro Admin Departments controller
 * 
 * @package blesta
 * @subpackage blesta.plugins.support_managerpro
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class AdminDepartments extends SupportManagerproController {

	/**
	 * Setup
	 */
	public function preAction() {
		parent::preAction();
		
		$this->requireLogin();
		
		$this->uses(array("SupportManagerpro.SupportManagerproDepartments", "SupportManagerpro.SupportManagerproResponses"));
		
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
		
		$departments = $this->SupportManagerproDepartments->getList($this->company_id, $page, array($sort => $order));
		$total_results = $this->SupportManagerproDepartments->getListCount($this->company_id);
		
		// Overwrite default pagination settings
		$settings = array_merge(Configure::get("Blesta.pagination"), array(
				'total_results' => $total_results,
				'uri' => $this->base_uri . "plugin/support_managerpro/admin_departments/index/[p]/"
			)
		);
		$this->helpers(array("Pagination" => array($this->get, $settings)));
		$this->Pagination->setSettings(Configure::get("Blesta.pagination_ajax"));
		
		$this->set("departments", $departments);
		$this->set("priorities", $this->SupportManagerproDepartments->getPriorities());
        $this->set("string", $this->DataStructure->create("string"));
		
		return $this->renderAjaxWidgetIfAsync(isset($this->get[0]) || isset($this->get['sort']));
	}
	
	/**
	 * Add a department
	 */
	public function add() {
		$this->uses(array("SupportManagerpro.SupportManagerproTickets"));
		
		// Create a department
		if (!empty($this->post)) {
			// Set empty checkboxes
			$checkboxes = array("clients_only", "override_from_email");
			foreach ($checkboxes as $checkbox) {
				if (!isset($this->post[$checkbox]))
					$this->post[$checkbox] = "0";
			}
			
			// Set the close ticket interval and response ID to null if not set
			if (empty($this->post['close_ticket_interval']))
				$this->post['close_ticket_interval'] = null;
			if (empty($this->post['response_id']))
				$this->post['response_id'] = null;
			
			// Set the company ID
			$data = $this->post;
			$data['company_id'] = $this->company_id;
			
			$department = $this->SupportManagerproDepartments->add($data);
			
			if (($errors = $this->SupportManagerproDepartments->errors())) {
				// Error, reset vars
				$vars = (object)$this->post;
				$this->setMessage("error", $errors, false, null, false);
			}
			else {
				// Success, add this staff member to this department
                $this->addStaff($department->id);
				$this->flashMessage("message", Language::_("AdminDepartments.!success.department_created", true, $department->name), null, false);
				$this->redirect($this->base_uri . "plugin/support_managerpro/admin_departments/");
			}
		}
		
		// Set default fields
		if (!isset($vars))
			$vars = (object)array('port' => 110, 'box_name' => "INBOX", 'clients_only' => "1");
		
		// Set the selected auto response, if any
		if (!empty($vars->response_id)) {
			$response = $this->SupportManagerproResponses->get($vars->response_id);
			if ($response && $response->company_id == $this->company_id)
				$this->set("response", $response);
			else
				unset($vars->response_id);
		}
		
		$this->set("vars", $vars);
		$this->set("priorities", $this->SupportManagerproDepartments->getPriorities());
		$this->set("methods", $this->SupportManagerproDepartments->getMethods());
		$this->set("statuses", $this->SupportManagerproDepartments->getStatuses());
		$this->set("security_types", $this->SupportManagerproDepartments->getSecurityTypes());
		$this->set("message_types", $this->SupportManagerproDepartments->getMessageTypes());
		$this->set("close_ticket_intervals", array('' => Language::_("Global.select.never", true)) + $this->SupportManagerproDepartments->getCloseTicketIntervals());
		$this->set("ticket_statuses", $this->SupportManagerproTickets->getStatuses());
		$this->set("piping_config", "/usr/bin/php " . realpath(dirname(__FILE__) . DS . ".." . DS) . DS . "pipe.php plugin/support_managerpro/ticket_pipe/index/" . $this->company_id . "/");
	}
	
	/**
	 * Edit a department
	 */
	public function edit() {
		if (!isset($this->get[0]) || !($department = $this->SupportManagerproDepartments->get($this->get[0])) ||
			$this->company_id != $department->company_id)
			$this->redirect($this->base_uri . "plugin/support_managerpro/admin_departments/");
		
		$this->uses(array("SupportManagerpro.SupportManagerproTickets"));
		
		// Update a department
		if (!empty($this->post)) {
			// Set empty checkboxes
			$checkboxes = array("clients_only", "override_from_email");
			foreach ($checkboxes as $checkbox) {
				if (!isset($this->post[$checkbox]))
					$this->post[$checkbox] = "0";
			}
			
			// Set the close ticket interval and response ID to null if not set
			if (empty($this->post['close_ticket_interval']))
				$this->post['close_ticket_interval'] = null;
			if (empty($this->post['response_id']))
				$this->post['response_id'] = null;
			
			$department = $this->SupportManagerproDepartments->edit($department->id, $this->post);
			
			if (($errors = $this->SupportManagerproDepartments->errors())) {
				// Error, reset vars
				$vars = (object)$this->post;
				$this->setMessage("error", $errors, false, null, false);
			}
			else {
				// Success
				$department = $this->SupportManagerproDepartments->get($department->id);
				$this->flashMessage("message", Language::_("AdminDepartments.!success.department_updated", true, $department->name), null, false);
				$this->redirect($this->base_uri . "plugin/support_managerpro/admin_departments/");
			}
		}
		
		// Set initial department
		if (!isset($vars))
			$vars = $department;
		
		// Set the selected auto response, if any
		if (!empty($vars->response_id)) {
			$response = $this->SupportManagerproResponses->get($vars->response_id);
			if ($response && $response->company_id == $this->company_id)
				$this->set("response", $response);
			else
				unset($vars->response_id);
		}
		
		$this->set("vars", $vars);
		$this->set("priorities", $this->SupportManagerproDepartments->getPriorities());
		$this->set("methods", $this->SupportManagerproDepartments->getMethods());
		$this->set("statuses", $this->SupportManagerproDepartments->getStatuses());
		$this->set("security_types", $this->SupportManagerproDepartments->getSecurityTypes());
		$this->set("message_types", $this->SupportManagerproDepartments->getMessageTypes());
		$this->set("close_ticket_intervals", array('' => Language::_("Global.select.never", true)) + $this->SupportManagerproDepartments->getCloseTicketIntervals());
		$this->set("ticket_statuses", $this->SupportManagerproTickets->getStatuses());
		$this->set("piping_config", "/usr/bin/php " . realpath(dirname(__FILE__) . DS . ".." . DS) . DS . "pipe.php plugin/support_managerpro/ticket_pipe/index/" . $this->company_id . "/");
	}
	
	/**
	 * Delete a department
	 */
	public function delete() {
		if (!isset($this->post['id']) || !($department = $this->SupportManagerproDepartments->get($this->post['id'])) ||
			$this->company_id != $department->company_id)
			$this->redirect($this->base_uri . "plugin/support_managerpro/admin_departments/");
		
		// Attempt to delete the department
		$this->SupportManagerproDepartments->delete($department->id);
		
		// Set message
		if (($errors = $this->SupportManagerproDepartments->errors()))
			$this->flashMessage("error", $errors, null, false);
		else
			$this->flashMessage("message", Language::_("AdminDepartments.!success.department_deleted", true, $department->name), null, false);
		
		$this->redirect($this->base_uri . "plugin/support_managerpro/admin_departments/");
	}
	
	/**
	 * AJAX Retrieves staff associated with a department
	 */
	public function assignedStaff() {
		// Ensure a department ID was given
		if (!$this->isAjax() || !isset($this->get[0]) ||
			!($department = $this->SupportManagerproDepartments->get($this->get[0])) ||
			$department->company_id != $this->company_id) {
			header($this->server_protocol . " 401 Unauthorized");
			exit();
		}
		
		$this->uses(array("SupportManagerpro.SupportManagerproStaff"));
		
		$vars = array(
			'staff' => $this->SupportManagerproStaff->getAll($this->company_id, $department->id)
		);
		
		// Send the template
		echo $this->partial("admin_departments_assigned_staff", $vars);
		
		// Render without layout
		return false;
	}
	
	/**
	 * AJAX retrieves the partial that lists categories and responses
	 */
	public function getResponseListing() {
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
	 * AJAX retrieves a specific predefined response
	 */
	public function getResponse() {
		// Ensure a valid response was given
		$response = (isset($this->get[0]) ? $this->SupportManagerproResponses->get($this->get[0]) : null);
		if ($response && $response->company_id != $this->company_id) {
			header($this->server_protocol . " 401 Unauthorized");
			exit();
		}
		
		echo $this->Json->encode($response);
		return false;
	}
    /**
     * Assigns the staff member to this department
     * @see AdminDepartments::add()
     *
     * @param int $department_id The ID of the department to add the staff to
     */
    private function addStaff($department_id) {
        $this->uses(array("SupportManagerpro.SupportManagerproStaff"));

        $support_staff = $this->SupportManagerproStaff->get($this->staff_id, $this->company_id);

        // Create a new staff member
        if (!$support_staff) {
            // Build default staff schedules to all day, every day
            $schedules = array();
            $days = $this->SupportManagerproStaff->getDays();
            foreach (array_keys($days) as $day) {
                $schedules[] = array('day' => $day, 'all_day' => 1);
            }

            // Default to receive ticket emails for all priorities
            $settings = array('ticket_emails' => array());
            $department_priorities = $this->SupportManagerproDepartments->getPriorities();
            foreach ($department_priorities as $key => $language)
                $settings['ticket_emails'][$key] = "true";

            // Create the staff member and assign them to this department
            $vars = array(
                'staff_id' => $this->staff_id,
                'company_id' => $this->company_id,
                'departments' => array($department_id),
                'schedules' => $schedules,
                'settings' => $settings
            );
            $this->SupportManagerproStaff->add($vars);
        }
        else {
            // Re-save the support staff member while also assigning this department to them
            $schedules = array();
            $i = 0;
            foreach ($support_staff->schedules as $schedule) {
                // Format the schedule time
                $schedules[$i]['day'] = $schedule->day;
                $schedules[$i]['start_time'] = $this->Date->cast($schedule->start_time, Configure::get("SupportManagerpro.time_format"));
                $schedules[$i]['end_time'] = $this->Date->cast($schedule->end_time, Configure::get("SupportManagerpro.time_format"));
                $i++;
            }

            $departments = array($department_id);
            foreach ($support_staff->departments as $department)
                $departments[] = $department->id;

            $vars = array(
                'company_id' => $support_staff->company_id,
                'departments' => $departments,
                'schedules' => $schedules
            );

            $this->SupportManagerproStaff->edit($support_staff->id, $vars);
        }
    }
}
?>