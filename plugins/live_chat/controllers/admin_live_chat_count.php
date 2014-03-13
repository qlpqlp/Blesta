<?php
/**
 * Live Chat plugin controller
 *
 * @package blesta
 * @subpackage blesta.plugins.live_chat
 * @copyright Copyright (c) 1998, Infoscan - Informática, Lda.
 * @link http://www.weblx.net/ WebLX
 */
class AdminLiveChatCount extends LiveChatController {

	public function index() {
	    Language::loadLang("live_chat_plugin", null, PLUGINDIR . "live_chat" . DS . "language" . DS);
        $this->uses(array("LiveChat.LiveChatCount"));

        $count = $this->LiveChatCount->LiveChatCountOnline();
        $this->set("online", $count);
    }

}
?>