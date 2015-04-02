<?php
/**
 * Blesta Loading plugin controller
 *
 * @package blesta
 * @subpackage blesta.plugins.Blesta_Loading
 * @copyright Copyright (c) 1998, Infoscan - Informática, Lda.
 * @link http://www.weblx.net/ WebLX
 */
class AdminBlestaLoading extends BlestaLoadingController {

	public function index() {
	    Language::loadLang("blesta_loading_plugin", null, PLUGINDIR . "blesta_loading" . DS . "language" . DS);
    }

}
?>