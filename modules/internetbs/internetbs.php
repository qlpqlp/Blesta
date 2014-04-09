<?php
/**
 * InternetBS Module
 *
 * @package blesta
 * @subpackage blesta.components.modules.internetbs
 * @copyright Copyright (c) 2014, Infoscan - Informática, Lda.
 * @link http://www.weblx.pt/ WebLX
 */
class Internetbs extends Module {
	
	/**
	 * @var string The version of this module
	 */
	private static $version = "1.0.2";
	/**
	 * @var string The authors of this module
	 */
	private static $authors = array(
		array(
			'name'=> "Infoscan - Informática, Lda.",
			'url'=>"http://www.weblx.pt"
		),
		array(
			'name'=> "WebLX (Portugal)",
			'url'=>"http://www.weblx.pt"
		)
	);

	/**
	 * Initializes the module
	 */
	public function __construct() {
		// Load components required by this module
		Loader::loadComponents($this, array("Input"));
		
		// Load the language required by this module
		Language::loadLang("internetbs", null, dirname(__FILE__) . DS . "language" . DS);

		Configure::load("internetbs", dirname(__FILE__) . DS . "config" . DS);
	}

	/**
	 * Returns the name of this module
	 *
	 * @return string The common name of this module
	 */
	public function getName() {
		return Language::_("Internetbs.name", true);
	}
	
	/**
	 * Returns the version of this module
	 *
	 * @return string The current version of this module
	 */
	public function getVersion() {
		return self::$version;
	}

	/**
	 * Returns the name and URL for the authors of this module
	 *
	 * @return array A numerically indexed array that contains an array with key/value pairs for 'name' and 'url', representing the name and URL of the authors of this module
	 */
	public function getAuthors() {
		return self::$authors;
	}
	
	/**
	 * Returns the value used to identify a particular service
	 *
	 * @param stdClass $service A stdClass object representing the service
	 * @return string A value used to identify this service amongst other similar services
	 */
	public function getServiceName($service) {
		foreach ($service->fields as $field) {
			if ($field->key == "domain")
				return $field->value;
		}
		return null;
	}
	
	/**
	 * Returns a noun used to refer to a module row (e.g. "Server", "VPS", "Reseller Account", etc.)
	 *
	 * @return string The noun used to refer to a module row
	 */
	public function moduleRowName() {
		return Language::_("Internetbs.module_row", true);
	}
	
	/**
	 * Returns a noun used to refer to a module row in plural form (e.g. "Servers", "VPSs", "Reseller Accounts", etc.)
	 *
	 * @return string The noun used to refer to a module row in plural form
	 */
	public function moduleRowNamePlural() {
		return Language::_("Internetbs.module_row_plural", true);
	}
	
	/**
	 * Returns a noun used to refer to a module group (e.g. "Server Group", "Cloud", etc.)
	 *
	 * @return string The noun used to refer to a module group
	 */
	public function moduleGroupName() {
		return null;
	}
	
	/**
	 * Returns the key used to identify the primary field from the set of module row meta fields.
	 * This value can be any of the module row meta fields.
	 *
	 * @return string The key used to identify the primary field from the set of module row meta fields
	 */
	public function moduleRowMetaKey() {
		return "user";
	}
	
	/**
	 * Returns the value used to identify a particular package service which has
	 * not yet been made into a service. This may be used to uniquely identify
	 * an uncreated services of the same package (i.e. in an order form checkout)
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param array $vars An array of user supplied info to satisfy the request
	 * @return string The value used to identify this package service
	 * @see Module::getServiceName()
	 */
	public function getPackageServiceName($packages, array $vars=null) {
		if (isset($vars['domain']))
			return $vars['domain'];
		return null;
	}
	
	/**
	 * Attempts to validate service info. This is the top-level error checking method. Sets Input errors on failure.
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param array $vars An array of user supplied info to satisfy the request
	 * @return boolean True if the service validates, false otherwise. Sets Input errors when false.
	 */
	public function validateService($package, array $vars=null) {
		return true;
	}
	
	/**
	 * 100% not tested yet
	 * Adds the service to the remote server. Sets Input errors on failure,
	 * preventing the service from being added.
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param array $vars An array of user supplied info to satisfy the request
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being added (if the current service is an addon service and parent service has already been provisioned)
	 * @param string $status The status of the service being added. These include:
	 * 	- active
	 * 	- canceled
	 * 	- pending
	 * 	- suspended
	 * @return array A numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function addService($package, array $vars=null, $parent_package=null, $parent_service=null, $status="pending") {

		$row = $this->getModuleRow($package->module_row);
		$api = $this->getApi($row->meta->user, $row->meta->key, $row->meta->sandbox == "true");
		
		#
		# TODO: Handle validation checks
		#
		
		$tld = null;
		$input_fields = array();

		if (isset($vars['domain']))
			$tld = $this->getTld($vars['domain']);
		
		if ($package->meta->type == "domain") {
			if (array_key_exists("transferAuthInfo", $vars)) {
				$input_fields = array_merge(Configure::get("Internetbs.transfer_fields"),
					array(
						'OrderType' => true, 'UseContacts' => true, 'DomainCount' => true,
						'NumYears' => true, 'SLD1' => true, 'TLD1' => true,
						'AuthInfo1' => true, 'UseDNS' => true
					)
				);
			}
			else {
				$whois_fields = Configure::get("Internetbs.whois_fields");
				$input_fields = array_merge(Configure::get("Internetbs.domain_fields"), $whois_fields,
					(array)Configure::get("Internetbs.domain_fields" . $tld),
					array('NumYears' => true, 'SLD' => true, 'TLD' => true, 'SLD1' => true, 'TLD1' => true, 'DomainCount' => 1, 'UseDNS' => true),
					(array)Configure::get("Internetbs.nameserver_fields")
				);
			}
		}
		
		if (isset($vars['use_module']) && $vars['use_module'] == "true") {
			if ($package->meta->type == "domain") {

				$vars['regperiod'] = 1 . "Y";
				//$vars['SLD'] = substr($vars['domain'], 0, -strlen($tld));
				//$vars['TLD'] = ltrim($tld, ".");
				$vars['domain'] = $vars['domain'];

				foreach ($package->pricing as $pricing) {
					if ($pricing->id == $vars['pricing_id']) {
						$vars['regperiod'] = $pricing->term . "Y";
						break;
					}
				}
				
				// Handle transfer
				if (isset($vars['transfer_key'])) {
					//$vars['DomainCount'] = "1";
					//$vars['UseContacts'] = "1";
					$vars['domain'] = $vars['domain'];
					$vars['transferAuthInfo'] = $vars['transfer_key'];
					
					$fields = array_intersect_key($vars, $input_fields);
					
					$command = new InternetbsAll($api);
					$response = $command->Domain_Transfer_Initiate($fields);
					$this->processResponse($api, $response);
					
					if ($this->Input->errors())
						return;
					
					return array(array('key' => "domain", 'value' => $fields['domain'], 'encrypted' => 0));
				}
				// Handle registration
				else {

					// Set all whois info from client ($vars['client_id'])
					if (!isset($this->Clients))
						Loader::loadModels($this, array("Clients"));
					if (!isset($this->Contacts))
						Loader::loadModels($this, array("Contacts"));
						
					$client = $this->Clients->get($vars['client_id']);
					$numbers = $this->Contacts->getNumbers($client->contact_id, "phone");

					foreach ($whois_fields as $key => $value) {
						if (strpos($key, "firstname") !== false)
							$vars[$key] = $client->first_name;
						elseif (strpos($key, "lastname") !== false)
							$vars[$key] = $client->last_name;
						elseif (strpos($key, "street3") !== false)
						elseif (strpos($key, "street2") !== false)
							$vars[$key] = $client->address2;
						elseif (strpos($key, "street") !== false)
							$vars[$key] = $client->address1;
						elseif (strpos($key, "city") !== false)
							$vars[$key] = $client->city;
						elseif (strpos($key, "postalcode") !== false)
							$vars[$key] = $client->zip;
						elseif (strpos($key, "countrycode") !== false)
							$vars[$key] = $client->country;
						elseif (strpos($key, "phonenumber") !== false)
							$vars[$key] = $this->formatPhone(isset($numbers[0]) ? $numbers[0]->number : null, $client->country);
						elseif (strpos($key, "email") !== false)
							$vars[$key] = $client->email;
					}

					//$vars['UseDNS'] = "default";

                	$nslist = array ();
					for ($i=1; $i<=5; $i++) {
						if (!isset($vars["ns" . $i]) || $vars["ns" . $i] == "")
							unset($vars["ns" . $i]);
						else
							//unset($vars['UseDNS']);
                            array_push ($nslist,$vars["ns$i"]);
					}

                	// ns_list is optional
                	if(count($nslist)) {
                		$vars['ns_list'] = trim(implode(',',$nslist),",");
                	}

					$fields = array_intersect_key($vars, $input_fields);

					$command = new InternetbsAll($api);
					$response = $command->domain_create($fields);
					$this->processResponse($api, $response);
					
					if ($this->Input->errors())
						return;
					
					return array(array('key' => "domain", 'value' => $vars['domain'], 'encrypted' => 0));
				}
			}
			else {
				#
				# TODO: Create SSL cert
				#
			}
		}
		
		$meta = array();
		$fields = array_intersect_key($vars, $input_fields);
		foreach ($fields as $key => $value) {
			$meta[] = array(
				'key' => $key,
				'value' => $value,
				'encrypted' => 0
			);
		}

		return $meta;
	}
	
	/**
	 * Edits the service on the remote server. Sets Input errors on failure,
	 * preventing the service from being edited.
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $vars An array of user supplied info to satisfy the request
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being edited (if the current service is an addon service)
	 * @return array A numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function editService($package, $service, array $vars=array(), $parent_package=null, $parent_service=null) {
		return null; // All this handled by admin/client tabs instead
	}
	
	/**
	 * Cancels the service on the remote server. Sets Input errors on failure,
	 * preventing the service from being canceled.
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being canceled (if the current service is an addon service)
	 * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function cancelService($package, $service, $parent_package=null, $parent_service=null) {
		return null; // Nothing to do
	}
	
	/**
	 * Suspends the service on the remote server. Sets Input errors on failure,
	 * preventing the service from being suspended.
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being suspended (if the current service is an addon service)
	 * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function suspendService($package, $service, $parent_package=null, $parent_service=null) {
		return null; // Nothing to do
	}
	
	/**
	 * Unsuspends the service on the remote server. Sets Input errors on failure,
	 * preventing the service from being unsuspended.
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being unsuspended (if the current service is an addon service)
	 * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function unsuspendService($package, $service, $parent_package=null, $parent_service=null) {
		return null; // Nothing to do
	}
	
	/**
	 * Allows the module to perform an action when the service is ready to renew.
	 * Sets Input errors on failure, preventing the service from renewing.
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being renewed (if the current service is an addon service)
	 * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function renewService($package, $service, $parent_package=null, $parent_service=null) {
		return null;
	}
	
	/**
	 * Updates the package for the service on the remote server. Sets Input
	 * errors on failure, preventing the service's package from being changed.
	 *
	 * @param stdClass $package_from A stdClass object representing the current package
	 * @param stdClass $package_to A stdClass object representing the new package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being changed (if the current service is an addon service)
	 * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function changeServicePackage($package_from, $package_to, $service, $parent_package=null, $parent_service=null) {
		return null; // Nothing to do
	}

	/**
	 * Validates input data when attempting to add a package, returns the meta
	 * data to save when adding a package. Performs any action required to add
	 * the package on the remote server. Sets Input errors on failure,
	 * preventing the package from being added.
	 *
	 * @param array An array of key/value pairs used to add the package
	 * @return array A numerically indexed array of meta fields to be stored for this package containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function addPackage(array $vars=null) {
		
		$meta = array();
		if (isset($vars['meta']) && is_array($vars['meta'])) {
			// Return all package meta fields
			foreach ($vars['meta'] as $key => $value) {
				$meta[] = array(
					'key' => $key,
					'value' => $value,
					'encrypted' => 0
				);
			}
		}
		
		return $meta;
	}
	
	/**
	 * Validates input data when attempting to edit a package, returns the meta
	 * data to save when editing a package. Performs any action required to edit
	 * the package on the remote server. Sets Input errors on failure,
	 * preventing the package from being edited.
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param array An array of key/value pairs used to edit the package
	 * @return array A numerically indexed array of meta fields to be stored for this package containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function editPackage($package, array $vars=null) {
		
		$meta = array();
		if (isset($vars['meta']) && is_array($vars['meta'])) {
			// Return all package meta fields
			foreach ($vars['meta'] as $key => $value) {
				$meta[] = array(
					'key' => $key,
					'value' => $value,
					'encrypted' => 0
				);
			}
		}
		
		return $meta;	
	}
	
	/**
	 * Returns the rendered view of the manage module page
	 *
	 * @param mixed $module A stdClass object representing the module and its rows
	 * @param array $vars An array of post data submitted to or on the manage module page (used to repopulate fields after an error)
	 * @return string HTML content containing information to display when viewing the manager module page
	 */
	public function manageModule($module, array &$vars) {
		// Load the view into this object, so helpers can be automatically added to the view
		$this->view = new View("manage", "default");
		$this->view->base_uri = $this->base_uri;
		$this->view->setDefaultView("components" . DS . "modules" . DS . "internetbs" . DS);
		
		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html", "Widget"));

		$this->view->set("module", $module);
		
		return $this->view->fetch();
	}
	
	/**
	 * Returns the rendered view of the add module row page
	 *
	 * @param array $vars An array of post data submitted to or on the add module row page (used to repopulate fields after an error)
	 * @return string HTML content containing information to display when viewing the add module row page
	 */
	public function manageAddRow(array &$vars) {
		// Load the view into this object, so helpers can be automatically added to the view
		$this->view = new View("add_row", "default");
		$this->view->base_uri = $this->base_uri;
		$this->view->setDefaultView("components" . DS . "modules" . DS . "internetbs" . DS);
		
		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html", "Widget"));
		
		// Set unspecified checkboxes
		if (!empty($vars)) {
			if (empty($vars['sandbox']))
				$vars['sandbox'] = "false";
		}
		
		$this->view->set("vars", (object)$vars);
		return $this->view->fetch();	
	}

	/**
	 * Returns the rendered view of the edit module row page
	 *
	 * @param stdClass $module_row The stdClass representation of the existing module row
	 * @param array $vars An array of post data submitted to or on the edit module row page (used to repopulate fields after an error)
	 * @return string HTML content containing information to display when viewing the edit module row page
	 */	
	public function manageEditRow($module_row, array &$vars) {
		// Load the view into this object, so helpers can be automatically added to the view
		$this->view = new View("edit_row", "default");
		$this->view->base_uri = $this->base_uri;
		$this->view->setDefaultView("components" . DS . "modules" . DS . "internetbs" . DS);
		
		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html", "Widget"));
		
		if (empty($vars))
			$vars = $module_row->meta;
		else {
			// Set unspecified checkboxes
			if (empty($vars['sandbox']))
				$vars['sandbox'] = "false";
		}
		
		$this->view->set("vars", (object)$vars);
		return $this->view->fetch();
	}
	
	/**
	 * Adds the module row on the remote server. Sets Input errors on failure,
	 * preventing the row from being added.
	 *
	 * @param array $vars An array of module info to add
	 * @return array A numerically indexed array of meta fields for the module row containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 */
	public function addModuleRow(array &$vars) {
		$meta_fields = array("user", "key", "sandbox");
		$encrypted_fields = array("key");

		// Set unspecified checkboxes
		if (empty($vars['sandbox']))
			$vars['sandbox'] = "false";
		
		$this->Input->setRules($this->getRowRules($vars));
		
		// Validate module row
		if ($this->Input->validates($vars)) {

			// Build the meta data for this row
			$meta = array();
			foreach ($vars as $key => $value) {
				
				if (in_array($key, $meta_fields)) {
					$meta[] = array(
						'key' => $key,
						'value' => $value,
						'encrypted' => in_array($key, $encrypted_fields) ? 1 : 0
					);
				}
			}
			
			return $meta;
		}
	}
	
	/**
	 * Edits the module row on the remote server. Sets Input errors on failure,
	 * preventing the row from being updated.
	 *
	 * @param stdClass $module_row The stdClass representation of the existing module row
	 * @param array $vars An array of module info to update
	 * @return array A numerically indexed array of meta fields for the module row containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 */
	public function editModuleRow($module_row, array &$vars) {
		// Same as adding
		return $this->addModuleRow($vars);
	}
	
	/**
	 * Deletes the module row on the remote server. Sets Input errors on failure,
	 * preventing the row from being deleted.
	 *
	 * @param stdClass $module_row The stdClass representation of the existing module row
	 */
	public function deleteModuleRow($module_row) {
		
	}
	
	/**
	 * Returns all fields used when adding/editing a package, including any
	 * javascript to execute when the page is rendered with these fields.
	 *
	 * @param $vars stdClass A stdClass object representing a set of post fields
	 * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
	 */
	public function getPackageFields($vars=null) {
		Loader::loadHelpers($this, array("Html"));
		
		$fields = new ModuleFields();
		
		$types = array(
			'domain' => Language::_("Internetbs.package_fields.type_domain", true),
			#
			# TODO: Add support for SSL certs
			#'ssl' => Language::_("Internetbs.package_fields.type_ssl", true)
			#
		);
		
		// Set type of package
		$type = $fields->label(Language::_("Internetbs.package_fields.type", true), "internetbs_type");
		$type->attach($fields->fieldSelect("meta[type]", $types,
			$this->Html->ifSet($vars->meta['type']), array('id'=>"internetbs_type")));
		$fields->setField($type);	
		
		// Set all TLD checkboxes
        $tld_options = $fields->label(Language::_("Internetbs.package_fields.tld_options", true));
		
		$tlds = Configure::get("Internetbs.tlds");
		sort($tlds);
		foreach ($tlds as $tld) {
			$tld_label = $fields->label($tld, "tld_" . $tld);
			$tld_options->attach($fields->fieldCheckbox("meta[tlds][]", $tld, (isset($vars->meta['tlds']) && in_array($tld, $vars->meta['tlds'])), array('id' => "tld_" . $tld), $tld_label));
		}
		$fields->setField($tld_options);
		
		// Set nameservers
		for ($i=1; $i<=5; $i++) {
			$type = $fields->label(Language::_("Internetbs.package_fields.ns" . $i, true), "internetbs_ns" . $i);
			$type->attach($fields->fieldText("meta[ns][]",
				$this->Html->ifSet($vars->meta['ns'][$i-1]), array('id'=>"internetbs_ns" . $i)));
			$fields->setField($type);
		}	
		
		$fields->setHtml("
			<script type=\"text/javascript\">
				$(document).ready(function() {
					toggleTldOptions($('#internetbs_type').val());
				
					// Re-fetch module options to pull cPanel packages and ACLs
					$('#internetbs_type').change(function() {
						toggleTldOptions($(this).val());
					});
					
					function toggleTldOptions(type) {
						if (type == 'ssl')
							$('.internetbs_tlds').hide();
						else
							$('.internetbs_tlds').show();
					}
				});
			</script>
		");
		
		return $fields;
	}
	
	/**
	 * Returns an array of key values for fields stored for a module, package,
	 * and service under this module, used to substitute those keys with their
	 * actual module, package, or service meta values in related emails.
	 *
	 * @return array A multi-dimensional array of key/value pairs where each key is one of 'module', 'package', or 'service' and each value is a numerically indexed array of key values that match meta fields under that category.
	 * @see Modules::addModuleRow()
	 * @see Modules::editModuleRow()
	 * @see Modules::addPackage()
	 * @see Modules::editPackage()
	 * @see Modules::addService()
	 * @see Modules::editService()
	 */
	public function getEmailTags() {
		return array('service' => array('domain'));
	}

	/**
	 * Returns all fields to display to an admin attempting to add a service with the module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param $vars stdClass A stdClass object representing a set of post fields
	 * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
	 */
	public function getAdminAddFields($package, $vars=null) {
		
		if ($package->meta->type == "domain") {
			
			// Set default name servers
			if (!isset($vars->ns1) && isset($package->meta->ns)) {
				$i=1;
				foreach ($package->meta->ns as $ns) {
					$vars->{"ns" . $i++} = $ns;
				}
			}
			
			// Handle transfer request
			if (isset($vars->transfer) || isset($vars->transfer_key)) {
				return $this->arrayToModuleFields(Configure::get("Internetbs.transfer_fields"), null, $vars);
			}
			// Handle domain registration
			else {
				
				#
				# TODO: Select TLD, then display additional fields
				#
				
				$module_fields = $this->arrayToModuleFields(array_merge(Configure::get("Internetbs.domain_fields"), Configure::get("Internetbs.nameserver_fields")), null, $vars);
				
				if (isset($vars->domain)) {
					$tld = $this->getTld($vars->domain);
					
					$extension_fields = Configure::get("Internetbs.domain_fields" . $tld);
					if ($extension_fields)
						$module_fields = $this->arrayToModuleFields($extension_fields, $module_fields, $vars);
				}
				
				return $module_fields;
			}
		}
		else {
			return new ModuleFields();
		}
	}
	
	/**
	 * Returns all fields to display to a client attempting to add a service with the module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param $vars stdClass A stdClass object representing a set of post fields
	 * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
	 */
	public function getClientAddFields($package, $vars=null) {

		// Handle universal domain name
		if (isset($vars->domain))
			$vars->domain = $vars->domain;
		
		if ($package->meta->type == "domain") {
			
			// Set default name servers
			if (!isset($vars->ns) && isset($package->meta->ns)) {
				$i=1;
				foreach ($package->meta->ns as $ns) {
					$vars->{"ns" . $i++} = $ns;
				}
			}
			
			// Handle transfer request
			if (isset($vars->transfer) || isset($vars->transfer_key)) {
				$fields = Configure::get("Internetbs.transfer_fields");
				
				// We should already have the domain name don't make editable
				$fields['domain']['type'] = "hidden";
				$fields['domain']['label'] = null;
				
				return $this->arrayToModuleFields($fields, null, $vars);
			}
			// Handle domain registration
			else {
				$fields = array_merge(Configure::get("Internetbs.nameserver_fields"), Configure::get("Internetbs.domain_fields"));
				
				// We should already have the domain name don't make editable
				$fields['domain']['type'] = "hidden";
				$fields['domain']['label'] = null;
				
				$module_fields = $this->arrayToModuleFields($fields, null, $vars);
				
				if (isset($vars->domain)) {
					$tld = $this->getTld($vars->domain);
					
					$extension_fields = Configure::get("Internetbs.domain_fields" . $tld);
					if ($extension_fields)
						$module_fields = $this->arrayToModuleFields($extension_fields, $module_fields, $vars);
				}
				
				return $module_fields;
			}
		}
		else {
			return new ModuleFields();
		}
	}
	
	/**
	 * Returns all fields to display to an admin attempting to edit a service with the module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param $vars stdClass A stdClass object representing a set of post fields
	 * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
	 */	
	public function getAdminEditFields($package, $vars=null) {
		if ($package->meta->type == "domain") {
			return new ModuleFields();
		}
		else {
			return new ModuleFields();
		}
	}
	
	/**
	 * Fetches the HTML content to display when viewing the service info in the
	 * admin interface.
	 *
	 * @param stdClass $service A stdClass object representing the service
	 * @param stdClass $package A stdClass object representing the service's package
	 * @return string HTML content containing information to display when viewing the service info
	 */
	public function getAdminServiceInfo($service, $package) {
		return "";
	}
	
	/**
	 * Fetches the HTML content to display when viewing the service info in the
	 * client interface.
	 *
	 * @param stdClass $service A stdClass object representing the service
	 * @param stdClass $package A stdClass object representing the service's package
	 * @return string HTML content containing information to display when viewing the service info
	 */
	public function getClientServiceInfo($service, $package) {
		return "";
	}
	
	/**
	 * Returns all tabs to display to an admin when managing a service whose
	 * package uses this module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @return array An array of tabs in the format of method => title. Example: array('methodName' => "Title", 'methodName2' => "Title2")
	 */
	public function getAdminTabs($package) {
		if ($package->meta->type == "domain") {
			return array(
				'tabWhois' => Language::_("Internetbs.tab_whois.title", true),
				'tabNameservers' => Language::_("Internetbs.tab_nameservers.title", true),
				'tabSettings' => Language::_("Internetbs.tab_settings.title", true)
			);
		}
		else {
			#
			# TODO: Handle SSL certs
			#
		}
	}

	/**
	 * Returns all tabs to display to a client when managing a service whose
	 * package uses this module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @return array An array of tabs in the format of method => title. Example: array('methodName' => "Title", 'methodName2' => "Title2")
	 */
	public function getClientTabs($package) {
		if ($package->meta->type == "domain") {
			return array(
				'tabClientWhois' => Language::_("Internetbs.tab_whois.title", true),
				'tabClientNameservers' => Language::_("Internetbs.tab_nameservers.title", true),
				'tabClientSettings' => Language::_("Internetbs.tab_settings.title", true)
			);
		}
		else {
			#
			# TODO: Handle SSL certs
			#
		}
	}
	
	/**
	 * Admin Whois tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabWhois($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageWhois("tab_whois", $package, $service, $get, $post, $files);
	}
	
	/**
	 * Client Whois tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabClientWhois($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageWhois("tab_client_whois", $package, $service, $get, $post, $files);
	}
	
	/**
	 * Admin Nameservers tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabNameservers($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageNameservers("tab_nameservers", $package, $service, $get, $post, $files);
	}
	
	/**
	 * Admin Nameservers tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabClientNameservers($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageNameservers("tab_client_nameservers", $package, $service, $get, $post, $files);
	}
	
	/**
	 * Admin Settings tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabSettings($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageSettings("tab_settings", $package, $service, $get, $post, $files);
	}
	
	/**
	 * Client Settings tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabClientSettings($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageSettings("tab_client_settings", $package, $service, $get, $post, $files);
	}
	
	/**
	 * 100% complete
	 * Handle updating whois information
	 *
	 * @param string $view The view to use
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	private function manageWhois($view, $package, $service, array $get=null, array $post=null, array $files=null) {
		
		$this->view = new View($view, "default");
		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html"));

		$row = $this->getModuleRow($package->module_row);
		$api = $this->getApi($row->meta->user, $row->meta->key, $row->meta->sandbox == "true");
		$command = new InternetbsAll($api);
		
		$vars = new stdClass();
		
		$whois_fields = Configure::get("Internetbs.whois_fields");
		$fields = $this->serviceFieldsToObject($service->fields);
		$whois_sections = Configure::get("Internetbs.whois_sections");

		if (!empty($post)) {
			$post = array_merge(array('domain' => $fields->domain), array_intersect_key($post, $whois_fields));
			$response = $command->Domain_Update($post);
			$this->processResponse($api, $response);
			
			$vars = (object)$post;
		}
		else {
			$response = $command->Domain_Info(array('Domain' => $fields->domain));
			$this->processResponse($api, $response);


			if ($response->status() == "SUCCESS") {
				$data = $response->response()->contacts;
				foreach ($whois_sections as $section) {

					if (isset($data->{$section})) {

						foreach ($data->{$section} as $name => $value) {
							$vars->{ucfirst($section)."_".$name} = $value;
						}
					}
				}
			}
		}

		$this->view->set("vars", $vars);
		$this->view->set("fields", $this->arrayToModuleFields($whois_fields, null, $vars)->getFields());
		$this->view->set("sections", array('Registrant', 'Technical', 'Billing', 'Admin'));
		$this->view->setDefaultView("components" . DS . "modules" . DS . "internetbs" . DS);
		return $this->view->fetch();
	}
	
	/**
	 * 100% complete
	 * Handle updating nameserver information
	 *
	 * @param string $view The view to use
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	private function manageNameservers($view, $package, $service, array $get=null, array $post=null, array $files=null) {
		
		$this->view = new View($view, "default");
		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html"));
		
		$vars = new stdClass();
		
		$row = $this->getModuleRow($package->module_row);
		$api = $this->getApi($row->meta->user, $row->meta->key, $row->meta->sandbox == "true");
		$command = new InternetbsAll($api);
		
		$fields = $this->serviceFieldsToObject($service->fields);
		
		if (!empty($post)) {
			$vars = $post;
            $nslist = "";
			// Default to using default nameservers
			//$vars['usedns'] = "Default";
			foreach ($vars['ns'] as $i => $ns) {
				if ($ns != "") {
				  $nslist .= $ns.",";

					//unset($vars['usedns']);
				}
			}

			unset($vars['ns']);

			$response = $command->Domain_Update(array('domain' => $fields->domain, 'ns_list' => $nslist));
			$this->processResponse($api, $response);

			$vars = (object)$post;
		}
		else {
			$response = $command->Domain_Info(array('domain' => $fields->domain));
			$this->processResponse($api, $response);

			if ($response->status() != "FAILURE") {
				$data = $response->response();
				if (isset($data->nameserver)) {
					foreach ($data->nameserver as $ns)
						$vars->ns[] = $ns;
				}

			}
		}
		
		$this->view->set("vars", $vars);
		$this->view->setDefaultView("components" . DS . "modules" . DS . "internetbs" . DS);
		return $this->view->fetch();
	}
	
	/**
	 * 100% complete
	 * Handle updating settings
	 *
	 * @param string $view The view to use
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	private function manageSettings($view, $package, $service, array $get=null, array $post=null, array $files=null) {

		$this->view = new View($view, "default");
		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html"));
		
		$vars = new stdClass();
		
		$row = $this->getModuleRow($package->module_row);
		$api = $this->getApi($row->meta->user, $row->meta->key, $row->meta->sandbox == "true");
		$command = new InternetbsAll($api);
		
		$fields = $this->serviceFieldsToObject($service->fields);
		
		if (!empty($post)) {
			
			if (isset($post['registrar_lock'])) {
			  if ($post['registrar_lock'] == "true"){
				$response = $command->Domain_RegistrarLock_Enable(array(
					'domain' => $fields->domain,
				));
               }else{
				$response = $command->Domain_RegistrarLock_Disable(array(
					'domain' => $fields->domain,
				));
               }
				$this->processResponse($api, $response);
			}

			if (isset($post['request_epp'])) {
				$response = $command->Domain_Info(array(
					'domain' => $fields->domain
				));


			$this->processResponse($api, $response);

			if ($response->status() == "SUCCESS") {
				$data = $response->response();
				$vars->epp_key = $data->{"transferauthinfo"};
			}


			}

			$vars = (object)$post;
		}
		else {
			$response = $command->Domain_RegistrarLock_Status(array('domain' => $fields->domain));
			$this->processResponse($api, $response);

			if ($response->status() == "SUCCESS") {
				$data = $response->response();
				$vars->registrar_lock = $data->{"registrar_lock_status"} == "LOCKED" ? "true" : "false";
			}

			$response = $command->Domain_Info(array('domain' => $fields->domain));
			$this->processResponse($api, $response);

			if ($response->status() == "SUCCESS") {
				$data = $response->response();
				$vars->epp_key = $data->{"transferauthinfo"};
			}

		}

		$this->view->set("vars", $vars);
		$this->view->setDefaultView("components" . DS . "modules" . DS . "internetbs" . DS);
		return $this->view->fetch();
	}
	
	/**
	 * 100% complete
	 * Performs a whois lookup on the given domain
	 *
	 * @param string $domain The domain to lookup
	 * @return boolean True if available, false otherwise
	 */
	public function checkAvailability($domain) {
		$row = $this->getModuleRow();
		$api = $this->getApi($row->meta->user, $row->meta->key, $row->meta->sandbox == "true");
		$all = new InternetbsAll($api);

		$response = $all->domain_check(array('domain' => $domain));

		if ($response->status() != "AVAILABLE")
			return false;

		$response = $response->response();

		return $response->status == "AVAILABLE";
	}
	
	/**
	 * Builds and returns the rules required to add/edit a module row
	 *
	 * @param array $vars An array of key/value data pairs
	 * @return array An array of Input rules suitable for Input::setRules()
	 */
	private function getRowRules(&$vars) {
		return array(
			'user' => array(
				'valid' => array(
					'rule' => "isEmpty",
					'negate' => true,
					'message' => Language::_("Internetbs.!error.user.valid", true)
				)
			),
			'key' => array(
				'valid' => array(
					'last' => true,
					'rule' => "isEmpty",
					'negate' => true,
					'message' => Language::_("Internetbs.!error.key.valid", true)
				),
				'valid_connection' => array(
					'rule' => array(array($this, "validateConnection"), $vars['user'], isset($vars['sandbox']) ? $vars['sandbox'] : "false"),
					'message' => Language::_("Internetbs.!error.key.valid_connection", true)
				)
			)
		);
	}
	
	/**
	 * 100% complete
	 * Validates that the given connection details are correct by attempting to check the availability of a domain
	 *
	 * @param string $key The API key
	 * @param string $user The API user
	 * @param string $sandbox "true" if this is a sandbox account, false otherwise
	 * @return boolean True if the connection details are valid, false otherwise
	 */
	public function validateConnection($key, $user, $sandbox) {
		$api = $this->getApi($user, $key, $sandbox == "true");
		$all = new InternetbsAll($api);
		return $all->domain_check(array('domain' => "internetbs.net"))->status() == "UNAVAILABLE";
	}
	
	/**
	 * Initializes the InternetbsApi and returns an instance of that object
	 *
	 * @param string $user The user to connect as
	 * @param string $key The key to use when connecting
	 * @param boolean $sandbox Whether or not to process in sandbox mode (for testing)
	 * @return InternetbsApi The InternetbsApi instance
	 */
	private function getApi($user, $key, $sandbox) {
		Loader::load(dirname(__FILE__) . DS . "apis" . DS . "internetbs_api.php");
		
		return new InternetbsApi($user, $key, $sandbox);
	}
	
	/**
	 * 100% complete
	 * Process API response, setting an errors, and logging the request
	 *
	 * @param InternetbsApi $api The internetbs API object
	 * @param InternetbsResponse $response The internetbs API response object
	 */
	private function processResponse(InternetbsApi $api, InternetbsResponse $response) {
		$this->logRequest($api, $response);
		
		// Set errors, if any
		if ($response->status() != "SUCCESS") {
			$errors = $response->errors() ? $response->errors() : array();
			$this->Input->setErrors(array('errors' => $errors));
		}
	}
	
	/**
	 * 100% completed
	 * Logs the API request
	 *
	 * @param InternetbsApi $api The internetbs API object
	 * @param InternetbsResponse $response The internetbs API response object
	 */
	private function logRequest(InternetbsApi $api, InternetbsResponse $response) {
		$last_request = $api->lastRequest();
		$last_request['args']['pw'] = "xxxx";

		$this->log($last_request['url'], serialize($last_request['args']), "input", true);
		$this->log($last_request['url'], $response->raw(), "output", $response->status() == "SUCCESS");
	}
	
	/**
	 * Returns the TLD of the given domain
	 *
	 * @param string $domain The domain to return the TLD from
	 * @return string The TLD of the domain
	 */
	private function getTld($domain) {
		$tlds = Configure::get("Internetbs.tlds");

		$domain = strtolower($domain);
		
		foreach ($tlds as $tld) {
			if (substr($domain, -strlen($tld)) == $tld)
				return $tld;
		}
		return strstr($domain, ".");
	}
	
	/**
	 * Formats a phone number into +NNN.NNNNNNNNNN
	 *
	 * @param string $number The phone number
	 * @param string $country The ISO 3166-1 alpha2 country code
	 * @return string The number in +NNN.NNNNNNNNNN
	 */
	private function formatPhone($number, $country) {
		if (!isset($this->Contacts))
			Loader::loadModels($this, array("Contacts"));
		
		return $this->Contacts->intlNumber($number, $country, ".");
	}
}
?>