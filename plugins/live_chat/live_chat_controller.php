<?php
/**
 * Live Chat parent controller for all Live Chat child controllers to inherit from
 *
 * @package blesta
 * @subpackage blesta.plugins.live_chat
 * @copyright Copyright (c) 1998, Infoscan - Informática, Lda.
 * @link http://www.weblx.net/ WebLX
 */
class LiveChatController extends AppController {
    public function preAction() {
        parent::preAction();

        // Override default view directory
        $this->view->view = "default";
        $this->structure->view = "default";
    }


}
?>