<?php
/**
 * Blesta Loading parent controller for all Blesta Loading child controllers to inherit from
 *
 * @package blesta
 * @subpackage blesta.plugins.Blesta_Loading
 * @copyright Copyright (c) 1998, Infoscan - Informática, Lda.
 * @link http://www.weblx.net/ WebLX
 */
class BlestaLoadingController extends AppController {
    public function preAction() {
        parent::preAction();

        // Override default view directory
        $this->view->view = "default";
        $this->structure->view = "default";
    }


}
?>