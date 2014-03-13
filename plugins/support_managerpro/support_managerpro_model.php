<?php
/**
 * Support Manager parent model
 * 
 * @package blesta
 * @subpackage blesta.plugins.support_managerpro
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class SupportManagerproModel extends AppModel {
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		
		Configure::load("support_managerpro", dirname(__FILE__) . DS . "config" . DS);
	}
}
?>