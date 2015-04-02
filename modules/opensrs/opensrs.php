<?php
/**
 * OpenSRS Module
 *
 * @package blesta
 * @subpackage blesta.components.modules.opensrs
 * @copyright Copyright (c) 2014, Infoscan - Informática, Lda.
 * @link http://www.weblx.pt/ WebLX
 */
class Opensrs extends Module {

	/**
	 * @var string The version of this module
	 */
	private static $version = "1.0.1";
	/**
	 * @var string The authors of this module
	 */
	private static $authors = array(
		array(
			'name'=> "Infoscan - Informatica, Lda [WebLX]",
			'url'=>"http://www.weblx.pt"
		),
		array(
			'name'=> "| (If you like it, click here to donate by PayPal)",
			'url'=>"https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CKNEWEGABW47A"
		)
	);

	/**
	 * Initializes the module
	 */
	public function __construct() {
		// Load components required by this module
		Loader::loadComponents($this, array("Input"));

		// Load the language required by this module
		Language::loadLang("opensrs", null, dirname(__FILE__) . DS . "language" . DS);

		Configure::load("opensrs", dirname(__FILE__) . DS . "config" . DS);

        if (!class_exists('openSRS_base')) {
            require_once dirname(__FILE__) . DS . "apis" . DS . "commands" . DS ."openSRS_loader.php";
        }

        // Set up the global error variables
        //global $osrsError;
        //global $osrsLogError;

	}

	/**
	 * Returns the name of this module
	 *
	 * @return string The common name of this module
	 */
	public function getName() {
		return Language::_("Opensrs.name", true);
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
		return Language::_("Opensrs.module_row", true);
	}

	/**
	 * Returns a noun used to refer to a module row in plural form (e.g. "Servers", "VPSs", "Reseller Accounts", etc.)
	 *
	 * @return string The noun used to refer to a module row in plural form
	 */
	public function moduleRowNamePlural() {
		return Language::_("Opensrs.module_row_plural", true);
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

	/**  TODO Create an Event to change the Type from domain to domainrenew $package->module_id
	 * OpenSRS 100% completed
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

        global $connectData;
        $tld = false;
   		$row = $this->getModuleRow($package->module_row);

		$input_fields = array();

		if ($package->meta->type == "domain") {
			if (array_key_exists("transfer_key", $vars)) {
				$whois_fields = Configure::get("Opensrs.whois_fields");
	    		$input_fields = array_merge(Configure::get("Opensrs.domain_fields"), $whois_fields,
                (array)Configure::get("Opensrs.transfer_fields"),
					array(
						'period' => true, 'domain' => true,
						'transferAuthInfo' => true
					)
				);

			}
			else {
				$whois_fields = Configure::get("Opensrs.whois_fields");
				$input_fields = array_merge(Configure::get("Opensrs.domain_fields"), $whois_fields,
					(array)Configure::get("Opensrs.domain_fields" . $this->getTld($vars['domain'])),
					array('period' => true, 'domain' => true),
					(array)Configure::get("Opensrs.nameserver_fields")
				);
			}
		}


		if (isset($vars['use_module']) && $vars['use_module'] == "true") {
			if ($package->meta->type == "domain") {

    		$tld = $this->getTld($vars['domain']);
    		$vars['SLD'] = substr($vars['domain'], 0, -strlen($tld));
    		$vars['TLD'] = ltrim($tld, ".");

            $domainUser = $this->getDomainUser($vars['TLD'], $vars['SLD']);
            $domainPass = $this->getDomainPass($vars['TLD'], $vars['SLD'], $row->meta->hashkey);

				$vars['period'] = 1;

				foreach ($package->pricing as $pricing) {
					if ($pricing->id == $vars['pricing_id']) {
						$vars['period'] = $pricing->term;
						$period_type = $pricing->period;
						break;
					}
				}


				// Handle transfer
				if (isset($vars['transfer_key'])) {

                      $callArray = array(
                            'func' => 'provSWregister',
                            'data' => array(
                                'domain' => $vars['SLD'] . "." . $vars['TLD'],
                                'f_lock_domain' => "1",
                                'custom_nameservers' => "1",
                                'reg_username' => $domainUser,
                                'reg_password' => $domainPass,
                                'period' => $vars['period'],
                                'handle' => "process",
                                'reg_type' => "transfer"
                             ),
                            'connect' => $this->generateConnectData($row->meta->key, $row->meta->user, $row->meta->sandbox)
                       );

                  $callArray["data"]["auth_info"] = $vars['transfer_key'];

				}else{
				// Handle registration
                        $callArray = array(
                            'func' => 'provSWregister',
                            'data' => array(
                                'domain' => $vars['SLD'] . "." . $vars['TLD'],
                                'custom_nameservers' => "1",
                                'f_lock_domain' => "1",
                                'reg_username' => $domainUser,
                                'reg_password' => $domainPass,
                                'period' => $vars['period'],
                                'reg_type' => "new",
                                'handle' => "process"
                         ),
                             'connect' => $this->generateConnectData($row->meta->key, $row->meta->user, $row->meta->sandbox)
                        );

        }

            $callArray["data"]["custom_tech_contact"] = "0";
            $callArray["data"]["f_whois_privacy"] = "0";

            //$callArray["tech"] = $callArray["admin"];
            //$callArray["billing"] = $callArray["admin"];

            $contactType = 'personal';
            $contactValueType = "";



					// Set all whois info from client ($vars['client_id'])
					if (!isset($this->Clients))
						Loader::loadModels($this, array("Clients"));
					if (!isset($this->Contacts))
						Loader::loadModels($this, array("Contacts"));

					$client = $this->Clients->get($vars['client_id']);
					$numbers = $this->Contacts->getNumbers($client->contact_id, "phone");

                    $remove_chars = "º";

                    $callArray[$contactType]["first_name"] = str_replace($remove_chars, "", $client->first_name);
                    $callArray[$contactType]["last_name"] = str_replace($remove_chars, "", $client->last_name);

            //check if org_name is blank
            //if (empty($contactSet[$contactValueType . "companyname"]))
                    $callArray[$contactType]["org_name"] = str_replace($remove_chars, "", $client->first_name) . " " . str_replace($remove_chars, "", $client->last_name);
            //else
            //$callArray[$contactType]["org_name"] = $contactSet[$contactValueType . "companyname"];

                    $callArray[$contactType]["address1"] = str_replace($remove_chars, "", $client->address1);
                    $callArray[$contactType]["address2"] = str_replace($remove_chars, "", $client->address2);
                    $callArray[$contactType]["address3"] = "";
                    $callArray[$contactType]["city"] = $client->city;
                    $callArray[$contactType]["state"] = $client->city;
                    $callArray[$contactType]["postal_code"] = $client->zip;
                    $callArray[$contactType]["country"] = $client->country;
                    $callArray[$contactType]["email"] = $client->email;
                    $callArray[$contactType]["phone"] = $this->formatPhone(isset($numbers[0]) ? $numbers[0]->number : null, $client->country);
                    $callArray[$contactType]["fax"] = "";
                    $callArray[$contactType]["url"] = "";
                    $callArray[$contactType]["lang_pref"] = "EN";

            for ($i = 1; $i <= 5; $i++) {
                if (strcmp($vars["ns" . $i], "") != 0) {
                    $callArray['data']['name' . $i] = $vars["ns" . $i];
                    $callArray['data']['sortorder' . $i] = $i;
                }
            }

                 //$callArray = addCCTLDFields($params, $callArray);
                $openSRSHandler = processOpenSRS("array", $callArray);

                $callArray["connect"]["osrs_key"] = "xxxx";
                $openSRSHandlerResponse = str_replace($row->meta->key, "xxxx", serialize($openSRSHandler));
        		$this->log($connectData["osrs_host"], serialize($callArray), "input", true);
        		$this->log($connectData["osrs_host"], $openSRSHandlerResponse, "output", $openSRSHandler->resultFullRaw["is_success"] == 1);

                if ($openSRSHandler->resultFullRaw["is_success"] == 0)
                $this->Input->setErrors(array('errors' => array('error' => "Invalid Domain")));

				return array(array('key' => "domain", 'value' => $vars['domain'], 'encrypted' => 0));
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

	/**OpenSRS 100% Completed
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

        global $connectData;

   		$row = $this->getModuleRow($package->module_row);

		if ($package->meta->type == "domain" || $package->meta->type == "domainrenew") {

           $regperiod = 1;

			foreach ($package->pricing as $pricing) {
				if ($pricing->id == $service->pricing_id) {
					$regperiod = $pricing->term;
					break;
				}
			}

           // OSRS requires the expiration year with a renewal, this function
           // will grab that expration year for this domain.
           $expirationYear = $this->getExpirationYear($domain);

            $currentYear = intval(date("Y"));

            // Check to make sure the renewal isn't going over the 10 year max
            // from this year.  If it is, then push an error out.
            if (intval($expirationYear) > $currentYear)
                $regMax = intval(date("Y")) + 10;
            else
                $regMax = intval($expirationYear) + 10;

            $renewedUntil = intval($expirationYear) + intval($regperiod);

            if ($regMax < $renewedUntil) {
                return "Domain can only be renewed to a maximum of 10 years.";
            } else {

                $callArray = array(
                    'func' => 'provRenew',
                    'data' => array(
                        'domain' => $domain,
                        'handle' => "process",
                        'period' => $regperiod,
                        'auto_renew' => "0",
                        'currentexpirationyear' => $expirationYear
                    ),
                    'connect' => $this->generateConnectData($row->meta->key, $row->meta->user, $row->meta->sandbox)
                );

                $openSRSHandler = processOpenSRS("array", $callArray);

                $callArray["connect"]["osrs_key"] = "xxxx";
                $openSRSHandlerResponse = str_replace($row->meta->key, "xxxx", serialize($openSRSHandler));
        		$this->log($connectData["osrs_host"], serialize($callArray), "input", true);
        		$this->log($connectData["osrs_host"], $openSRSHandlerResponse, "output", $openSRSHandler->resultFullRaw["is_success"] == 1);

                if (strcmp($openSRSHandler->resultFullRaw["is_success"], "1") != 0) {
                    return $openSRSHandler->resultFullRaw["response_text"];
                }

            }
		}
		else {
			#
			# TODO: SSL Cert: Set cancelation date of service?
			#
		}


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

        // Todo Renew domain for xx years wen changing Package or Term
		if (($row = $this->getModuleRow())) {
            //print_r ($row);
            //print_r ($package_to->pricing[0]->pricing_id);
			// Only request a package change if it has changed
			if ($package_from->meta->package != $package_to->meta->package) {



			foreach ($package_to->pricing as $pricing_to) {
				if ($pricing_to->id == $service->pricing_id) {
					$package_to_term = $pricing_to->term;
					break;
				}
			}
			 //	$service_fields = $this->serviceFieldsToObject($service->fields);

//				$this->log($row->meta->host_name . "|changepackage", serialize(array($service_fields->cpanel_username, $package_to->meta->package)), "input", true);

//				$this->parseResponse($api->changepackage($service_fields->cpanel_username, $package_to->meta->package));
			}
		}


		return null; // Nothing to do
	}

	/**OpenSRS 100% Completed
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

	/**OpenSRS 100% Completed
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

	/**OpenSRS 100% Completed
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
		$this->view->setDefaultView("components" . DS . "modules" . DS . "opensrs" . DS);

		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html", "Widget"));

		$this->view->set("module", $module);

		return $this->view->fetch();
	}

	/**OpenSRS 100% Completed
	 * Returns the rendered view of the add module row page
	 *
	 * @param array $vars An array of post data submitted to or on the add module row page (used to repopulate fields after an error)
	 * @return string HTML content containing information to display when viewing the add module row page
	 */
	public function manageAddRow(array &$vars) {
		// Load the view into this object, so helpers can be automatically added to the view
		$this->view = new View("add_row", "default");
		$this->view->base_uri = $this->base_uri;
		$this->view->setDefaultView("components" . DS . "modules" . DS . "opensrs" . DS);

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

	/**OpenSRS 100% Completed
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
		$this->view->setDefaultView("components" . DS . "modules" . DS . "opensrs" . DS);

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

	/**OpenSRS 100% Completed
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
		$meta_fields = array("user", "key", "hashkey", "sandbox");
		$encrypted_fields = array("key", "hashkey");

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

	/** OpenSRS 100% Completed
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
			'domain' => Language::_("Opensrs.package_fields.type_domain", true),
			'domaintransfer' => Language::_("Opensrs.package_fields.type_domaintransfer", true),
			'domainrenew' => Language::_("Opensrs.package_fields.type_domainrenew", true),
			#
			# TODO: Add support for SSL certs
			#'ssl' => Language::_("Opensrs.package_fields.type_ssl", true)
			#
		);

		// Set type of package
		$type = $fields->label(Language::_("Opensrs.package_fields.type", true), "opensrs_type");
		$type->attach($fields->fieldSelect("meta[type]", $types,
			$this->Html->ifSet($vars->meta['type']), array('id'=>"opensrs_type")));
		$fields->setField($type);

		// Set all TLD checkboxes
        $tld_options = $fields->label(Language::_("Opensrs.package_fields.tld_options", true));

		$tlds = Configure::get("Opensrs.tlds");
		sort($tlds);
		foreach ($tlds as $tld) {
			$tld_label = $fields->label($tld, "tld_" . $tld);
			$tld_options->attach($fields->fieldCheckbox("meta[tlds][]", $tld, (isset($vars->meta['tlds']) && in_array($tld, $vars->meta['tlds'])), array('id' => "tld_" . $tld), $tld_label));
		}
		$fields->setField($tld_options);

		// Set nameservers
		for ($i=1; $i<=5; $i++) {
			$type = $fields->label(Language::_("Opensrs.package_fields.ns" . $i, true), "opensrs_ns" . $i);
			$type->attach($fields->fieldText("meta[ns][]",
				$this->Html->ifSet($vars->meta['ns'][$i-1]), array('id'=>"opensrs_ns" . $i)));
			$fields->setField($type);
		}

		$fields->setHtml("
			<script type=\"text/javascript\">
				$(document).ready(function() {
					toggleTldOptions($('#opensrs_type').val());

					// Re-fetch module options to pull cPanel packages and ACLs
					$('#opensrs_type').change(function() {
						toggleTldOptions($(this).val());
					});

					function toggleTldOptions(type) {
						if (type == 'ssl')
							$('.opensrs_tlds').hide();
						else
							$('.opensrs_tlds').show();
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

	/** OpenSRS 100% Completed
	 * Returns all fields to display to an admin attempting to add a service with the module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param $vars stdClass A stdClass object representing a set of post fields
	 * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
	 */
	public function getAdminAddFields($package, $vars=null) {

        // Handle the tree types domain, domaintranfer and domain renew to show diferent prices
		if ($package->meta->type == "domain" || $package->meta->type == "domaintransfer" || $package->meta->type == "domainrenew") {

			// Set default name servers
			if (!isset($vars->ns1) && isset($package->meta->ns)) {
				$i=1;
				foreach ($package->meta->ns as $ns) {
					$vars->{"ns" . $i++} = $ns;
				}
			}

			// Handle transfer request
			if (isset($vars->transfer) || isset($vars->transfer_key)) {
				return $this->arrayToModuleFields(Configure::get("Opensrs.transfer_fields"), null, $vars);
			}
			// Handle domain registration
			else {

				#
				# TODO: Select TLD, then display additional fields
				#

				$module_fields = $this->arrayToModuleFields(array_merge(Configure::get("Opensrs.domain_fields"), Configure::get("Opensrs.nameserver_fields")), null, $vars);

				if (isset($vars->domain)) {
					$tld = $this->getTld($vars->domain);

					$extension_fields = Configure::get("Opensrs.domain_fields" . $tld);
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

	/** OpenSRS 100% Completed
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

        // Handle the two types domain and domaintranfer to request the EPPKey
		if ($package->meta->type == "domain" || $package->meta->type == "domaintransfer") {

			// Set default name servers
			if (!isset($vars->ns) && isset($package->meta->ns)) {
				$i=1;
				foreach ($package->meta->ns as $ns) {
					$vars->{"ns" . $i++} = $ns;
				}
			}

			// Handle transfer request
			if (isset($vars->transfer) || isset($vars->transfer_key)) {
				$fields = Configure::get("Opensrs.transfer_fields");

				// We should already have the domain name don't make editable
				$fields['domain']['type'] = "hidden";
				$fields['domain']['label'] = null;

				return $this->arrayToModuleFields($fields, null, $vars);
			}
			// Handle domain registration
			else {
				$fields = array_merge(Configure::get("Opensrs.nameserver_fields"), Configure::get("Opensrs.domain_fields"));

				// We should already have the domain name don't make editable
				$fields['domain']['type'] = "hidden";
				$fields['domain']['label'] = null;

				$module_fields = $this->arrayToModuleFields($fields, null, $vars);

				if (isset($vars->domain)) {
					$tld = $this->getTld($vars->domain);

					$extension_fields = Configure::get("Opensrs.domain_fields" . $tld);
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
		if ($package->meta->type == "domain" || $package->meta->type == "domaintransfer" || $package->meta->type == "domainrenew") {
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
		if ($package->meta->type == "domain" || $package->meta->type == "domaintransfer" || $package->meta->type == "domainrenew") {
			return array(
				'tabCommands' => Language::_("Opensrs.tab_commands.title", true),
				'tabWhois' => Language::_("Opensrs.tab_whois.title", true),
				'tabNameservers' => Language::_("Opensrs.tab_nameservers.title", true),
				'tabDNS' => Language::_("Opensrs.tab_dns.title", true),
				'tabEmailForwarding' => Language::_("Opensrs.tab_emailforwarding.title", true),
				'tabSettings' => Language::_("Opensrs.tab_settings.title", true)
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
				'tabClientWhois' => Language::_("Opensrs.tab_whois.title", true),
				'tabClientNameservers' => Language::_("Opensrs.tab_nameservers.title", true),
				'tabClientDNS' => Language::_("Opensrs.tab_dns.title", true),
				'tabClientEmailForwarding' => Language::_("Opensrs.tab_emailforwarding.title", true),
				'tabClientSettings' => Language::_("Opensrs.tab_settings.title", true)
			);
		}
		else {
			#
			# TODO: Handle SSL certs
			#
		}
	}

	/**
	 * Admin Command tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabCommands($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageCommands("tab_commands", $package, $service, $get, $post, $files);
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
	 * Admin Email Fowards tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabEmailForwarding($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageEmailForwarding("tab_emailforwarding", $package, $service, $get, $post, $files);
	}

	/**
	 * Admin Email Fowards tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabClientEmailForwarding($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageEmailForwarding("tab_client_emailforwarding", $package, $service, $get, $post, $files);
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
	private function manageCommands($view, $package, $service, array $get=null, array $post=null, array $files=null) {

		$this->view = new View($view, "default");
		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html"));

		$vars = new stdClass();

		//$row = $this->getModuleRow($package->module_row);
		//$api = $this->getApi($row->meta->user, $row->meta->key, $row->meta->sandbox == "true");
		//$command = new OpensrsAll($api);

		//$fields = $this->serviceFieldsToObject($service->fields);

		$this->view->set("vars", $vars);
		$this->view->setDefaultView("components" . DS . "modules" . DS . "opensrs" . DS);
		return $this->view->fetch();
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
		$command = new OpensrsAll($api);

		$vars = new stdClass();

		$whois_fields = Configure::get("Opensrs.whois_fields");
		$fields = $this->serviceFieldsToObject($service->fields);
		$whois_sections = Configure::get("Opensrs.whois_sections");

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
                          if (!is_string($value)){$value = "";}// check if its string
						  $vars->{ucfirst($section)."_".$name} = $value;
						}
					}

				}
			}
		}

		$this->view->set("vars", $vars);
		$this->view->set("fields", $this->arrayToModuleFields($whois_fields, null, $vars)->getFields());
		$this->view->set("sections", array('Registrant', 'Technical', 'Billing', 'Admin'));
		$this->view->setDefaultView("components" . DS . "modules" . DS . "opensrs" . DS);
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
		$command = new OpensrsAll($api);

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
		$this->view->setDefaultView("components" . DS . "modules" . DS . "opensrs" . DS);
		return $this->view->fetch();
	}

	/**
	 * 100% complete
	 * Handle updating email fowarding information
	 *
	 * @param string $view The view to use
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	private function manageEmailForwarding($view, $package, $service, array $get=null, array $post=null, array $files=null) {

		$this->view = new View($view, "default");
		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html"));

		$vars = new stdClass();

		$row = $this->getModuleRow($package->module_row);
		$api = $this->getApi($row->meta->user, $row->meta->key, $row->meta->sandbox == "true");
		$command = new OpensrsAll($api);

		$fields = $this->serviceFieldsToObject($service->fields);

		if (!empty($post)) {
			$vars = $post;

    	foreach ($vars['efsourceh'] as $i => $efsourceh) {
            if (!empty($vars['efsourceh'][$i])){
    		$sourceh = $vars['efsourceh'][$i] . "@" . $fields->domain;
    		$sourceh = urlencode ($sourceh);
    		$command->Domain_EmailForward_Remove(array('source' => $sourceh));
            }
      	}

    	foreach ($vars['efsource'] as $i => $efsource) {
            if (!empty($vars['efsource'][$i])){
           		$source = $vars['efsource'][$i] . "@" . $fields->domain;
           		$source = urlencode (trim($source));
                $destination = urlencode(trim($vars['efdestination'][$i]));
    		 	$response = $command->Domain_EmailForward_Add(array('source' => $source, 'destination' => $destination));
                $this->processResponse($api, $response);
            }

      	}
			unset($vars['efsourceh']);
			unset($vars['efsource']);
			unset($vars['efdestination']);
			unset($vars['domain']);
			$vars = (object)$post;

		}
		else {
			$response = $command->Domain_EmailForward_List(array('domain' => $fields->domain));
			$this->processResponse($api, $response);

			if ($response->status() != "FAILURE") {
				$data = $response->response();
                    // get email rules/fowards
                    if($data->total_rules > 0) {
        				foreach ($data->rule as $efsource){
        				  if(!empty($efsource->source)){
		    			   	$vars->efsource[] = substr($efsource->source, 0, strpos($efsource->source, '@'));
                            $vars->efdestination[] = $efsource->destination;
                          }else{
                            $vars->efsource[0] = substr($data->rule->source, 0, strpos($data->rule->source, '@'));
                            $vars->efdestination[0] = $data->rule->destination;
                          }
                        }
                    }

			}
        $vars->domain = $fields->domain;
		}
		$this->view->set("vars", $vars);
		$this->view->setDefaultView("components" . DS . "modules" . DS . "opensrs" . DS);
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
		$command = new OpensrsAll($api);

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
		$this->view->setDefaultView("components" . DS . "modules" . DS . "opensrs" . DS);
		return $this->view->fetch();
	}

	/**
	 * OpenSRS 100% complete
	 * Performs a whois lookup on the given domain
	 *
	 * @param string $domain The domain to lookup
	 * @return boolean True if available, false otherwise
	 */
	public function checkAvailability($domain) {

        global $connectData;

		$row = $this->getModuleRow();

		$tld = trim($this->getTld($domain), ".");
		$sld = trim(substr($domain, 0, -strlen($tld)), ".");

        $callArray = array (
        	"func" => 'lookupDomain',
        	"data" => array (
        		"domain" => $sld,
                "maximum" => "1",
        		"selected" => $tld,
        		"defaulttld" => $tld
        	),
            'connect' => $this->generateConnectData($row->meta->key, $row->meta->user, $row->meta->sandbox)
        );

        $openSRSHandler = processOpenSRS("array", $callArray);

        $callArray["connect"]["osrs_key"] = "xxxx";
        $openSRSHandlerResponse = str_replace($row->meta->key, "xxxx", serialize($openSRSHandler));
//        print_r ($openSRSHandler->resultFullRaw);
		$this->log($connectData["osrs_host"], serialize($callArray), "input", true);
		$this->log($connectData["osrs_host"], $openSRSHandlerResponse, "output", $openSRSHandler->resultFullRaw["is_success"] == 1);

//        if ($openSRSHandler->resultRaw[0]["status"] == "available")
//            return true;

        if ($openSRSHandler->resultRaw[0]["status"] == "taken")
            return false;

            return true;

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
					'message' => Language::_("Opensrs.!error.user.valid", true)
				)
			),
			'hashkey' => array(
				'valid' => array(
					'last' => true,
					'rule' => "isEmpty",
					'negate' => true,
					'message' => Language::_("Opensrs.!error.hashkey.valid", true)
				)
			),
			'key' => array(
				'valid' => array(
					'last' => true,
					'rule' => "isEmpty",
					'negate' => true,
					'message' => Language::_("Opensrs.!error.key.valid", true)
				),
				'valid_connection' => array(
					'rule' => array(array($this, "validateConnection"), $vars['user'], isset($vars['sandbox']) ? $vars['sandbox'] : "false"),
					'message' => Language::_("Opensrs.!error.key.valid_connection", true)
				)
			)
		);
	}

	/**
	 * OpenSRS 100% complete
	 * Validates that the given connection details are correct by attempting to check the availability of a domain
	 *
	 * @param string $key The API key
	 * @param string $user The API user
	 * @param string $sandbox "true" if this is a sandbox account, false otherwise
	 * @return boolean True if the connection details are valid, false otherwise
	 */
	public function validateConnection($key, $user, $sandbox) {
    global $connectData;
    
        $callArray = array (
        	"func" => 'lookupDomain',
        	"data" => array (
        		"domain" => "opensrs",
                "maximum" => "1",
        		"selected" => ".com",
        		"defaulttld" => ".com"
        	),
            'connect' => $this->generateConnectData($key, $user, $sandbox)
        );

        $openSRSHandler = processOpenSRS("array", $callArray);

        $callArray["connect"]["osrs_key"] = "xxxx";
        $openSRSHandlerResponse = str_replace($key, "xxxx", serialize($openSRSHandler));
        $openSRSHandlerResponse = str_replace('" ', '', $openSRSHandlerResponse);

		$this->log($connectData["osrs_host"], serialize($callArray), "input", true);
		$this->log($connectData["osrs_host"], $openSRSHandlerResponse, "output", $openSRSHandler->resultFullRaw["is_success"] == 1);

		return $openSRSHandler->resultFullRaw["is_success"] == 1;
	}

	/**
	 * Initializes the OpensrsApi and returns an instance of that object
	 *
	 * @param string $user The user to connect as
	 * @param string $key The key to use when connecting
	 * @param boolean $sandbox Whether or not to process in sandbox mode (for testing)
	 * @return OpensrsApi The OpensrsApi instance
	 */
	private function getApi($user, $key, $sandbox) {
		Loader::load(dirname(__FILE__) . DS . "apis" . DS . "opensrs_api.php");

		return new OpensrsApi($user, $key, $sandbox);
	}

	/**
	 * 100% complete
	 * Process API response, setting an errors, and logging the request
	 *
	 * @param OpensrsApi $api The opensrs API object
	 * @param OpensrsResponse $response The opensrs API response object
	 */
	private function processResponse(OpensrsApi $api, OpensrsResponse $response) {
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
	 * @param OpensrsApi $api The opensrs API object
	 * @param OpensrsResponse $response The opensrs API response object
	 */
	private function logRequest(OpensrsApi $api, OpensrsResponse $response) {
        global $connectData;

		$last_request = $api->lastRequest();
		$last_request['args']['pw'] = "xxxx";

		$this->log($connectData["osrs_host"], serialize($last_request['args']), "input", true);
		$this->log($connectData["osrs_host"], $response->raw(), "output", $response->status() == "SUCCESS");
	}

	/**
	 * Returns the TLD of the given domain
	 *
	 * @param string $domain The domain to return the TLD from
	 * @return string The TLD of the domain
	 */
	private function getTld($domain) {
		$tlds = Configure::get("Opensrs.tlds");

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



    // Generates the User name based on domain name
    private function getDomainUser($tld, $sld) {
        /* Added by BC : RA : 5-7-2014 : To set request string at logModuleCall */
        $callArray = array('tld' => $tld, 'sld' => $sld);
        /* End : To set request string at logModuleCall */
        $domainUser = $sld . $tld;
        $domainUser = str_replace("-", "", $domainUser);
        $domainUser = str_replace(".", "", $domainUser);

        if (strlen($domainUser) > 20) {
            $domainUser = substr($domainUser, 0, 19);
        }

        /* Added by BC : NG : 27-6-2014 : For add log in getDomainUser function */
        //opensrspro_logModuleCall(__FUNCTION__, $callArray, $domainUser, $domainUser, "");
        /* End : For add log in getDomainUser function */

        return $domainUser;
    }


    // Generates a secure password beased on the domain name and a admin
    // provided hash key.
    private function getDomainPass($tld, $sld, $hashKey) {
        /* Added by BC : RA : 5-7-2014 : To set request string at logModuleCall */
        $callArray = array('tld' => $tld, 'sld' => $sld, 'hashKey' => $hashKey);
        /* End : To set request string at logModuleCall */
        $domainPass = sha1(sha1($tld . $sld . $hashKey) . $hashKey);
        $domainPass = substr($domainPass, 0, 19);

         /* Added by BC : NG : 27-6-2014 : For add log in getDomainPass function */
        //opensrspro_logModuleCall(__FUNCTION__, $callArray, $domainPass, $domainPass, "");
        /* End : For add log in getDomainPass function */

        return $domainPass;
    }

    // Grabs the cookie from OSRS, sends any errors back via the global error variables
    private function getCookie($domain, $domainUser, $domainPass, $params) {

        global $osrsLogError;
        global $osrsError;
        $cookie = false;


        $cookieCall = array(
            'func' => 'cookieSet',
            'data' => array(
                'domain' => $domain,
                'reg_username' => $domainUser,
                'reg_password' => $domainPass
            ),
            'connect' => generateConnectData($params)
        );

        set_error_handler("osrsError", E_USER_WARNING);

        $cookieReturn = processOpenSRS("array", $cookieCall);

        restore_error_handler();

        if (strcmp($cookieReturn->resultFullRaw["is_success"], "1") == 0) {
            $cookie = $cookieReturn->resultFullRaw["attributes"]["cookie"];
        } else {
            $osrsLogError = $cookieReturn->resultFullRaw["response_text"];
        }

        opensrspro_logModuleCall(__FUNCTION__, $cookieCall, $cookieReturn->resultFullRaw, $cookie, $params);

        return $cookie;
    }

    // Generates the connection data needed to send an OSRS call
    private function generateConnectData($key, $user, $sandbox = null) {

        global $connectData;

        if ($sandbox == "true") {
            $connectData["osrs_username"] = $user;
            $connectData["osrs_password"] = "placeholder";
            $connectData["osrs_key"] = $key;
            $connectData["osrs_environment"] = "TEST";
            $connectData["osrs_host"] = "horizon.opensrs.net";
            $connectData["osrs_port"] = "55000";
            $connectData["osrs_sslPort"] = "55443";
        } else {
            $connectData["osrs_username"] = $user;
            $connectData["osrs_password"] = "placeholder";
            $connectData["osrs_key"] = $key;
            $connectData["osrs_environment"] = "PROD";
            $connectData["osrs_host"] = "rr-n1-tor.opensrs.net";
            $connectData["osrs_port"] = "55000";
            $connectData["osrs_sslPort"] = "55443";
        }

        $connectData["osrs_protocol"] = "XCP";
        $connectData["osrs_baseClassVersion"] = "2.8.0";
        $connectData["osrs_version"] = "XML:0.1";

     /* Added by BC : NG : 27-6-2014 : For add log in generateConnectData function */
    //    opensrspro_logModuleCall(__FUNCTION__, $params, $connectData, $connectData, $params);
    /* End : For add log in generateConnectData function */

        return $connectData;
    }

    // Checks to make sure the word reseller is not in an error message.  If it is,
    // it will replace it with the general error.
    private function filterForResellerError($error, $generalError) {

         /* Added by BC : RA : 5-7-2014 : To set request string at logModuleCall */
        $callArray = array('error' => $error, 'generalError' => $generalError);
        /* End : To set request string at logModuleCall */

        $newError = "";

        if (preg_match("/\sreseller[\s\.,;\-:]/", $error) == 0)
            $newError = $error;
        else
            $newError = $generalError;

         /* Added by BC : NG : 27-6-2014 : For add log in filterForResellerError function */
        if($error != "" && $generalError != "")
        {
            //opensrspro_logModuleCall(__FUNCTION__, $callArray, $newError, $newError, '');
        }
        /* End : For add log in filterForResellerError function */

        return $newError;
    }

     // Grabs the expiration year and sends back errors via the global error variables
    private function getExpirationYear($domain) {

            global $connectData;
            $expirationYear = false;

    		$row = $this->getModuleRow();

            $expirationCall = array (
            	"func" => 'lookupGetDomain',
                'data' => array(
                    'domain' => $domain,
                    'typ' => "all_info",
                    'bypass' => $domain,
            ),
                'connect' => $this->generateConnectData($row->meta->key, $row->meta->user, $row->meta->sandbox)
            );

            $expiryReturn = processOpenSRS("array", $expirationCall);

            if (strcmp($expiryReturn->resultFullRaw["is_success"], "1") == 0) {
                $expirationDate = $expiryReturn->resultFullRaw["attributes"]["registry_expiredate"] ? $expiryReturn->resultFullRaw["attributes"]["registry_expiredate"] : $expiryReturn->resultFullRaw["attributes"]["expiredate"];
                $expirationDateArray = explode("-", $expirationDate);
                $expirationYear = $expirationDateArray[0];
            }

            return $expirationYear;
        }

        // Takes any OSRS errors and
        private function osrsError($errno, $errstr, $errfile, $errline) {

            global $osrsError;
            global $osrsLogError;

            /* Added by BC : RA : 5-7-2014 : To set request string at logModuleCall */
            $callArray = array('Error No.' => $errno, 'Error String' => $errstr, 'Error File' => $errfile, 'Error Line' => $errline);
            /* End : To set request string at logModuleCall */


            // Error to be logged, includes file and error line.
            $osrsLogError .=$errstr . " " . " File: " . $errfile . " Line: " . $errline;

            // Error to be displayed to end user, only the error string itself.
            $osrsError.= $errstr . "<br />";

            /* Added by BC : RA : 5-7-2014 : To set request string at logModuleCall */
            $responseArray = array('osrsLogError' => $osrsLogError, 'osrsError' => $osrsError);
            opensrspro_logModuleCall(__FUNCTION__, $callArray, $responseArray, $responseArray, '');
            /* End : To set request string at logModuleCall */
        }

}
?>