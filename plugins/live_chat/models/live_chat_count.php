<?php
/**
 * Live Chat plugin controller
 *
 * @package blesta
 * @subpackage blesta.plugins.live_chat
 * @copyright Copyright (c) 1998, Infoscan - Informática, Lda.
 * @link http://www.weblx.net/ WebLX
 */
class LiveChatCount extends LiveChatModel {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}

   	public function LiveChatCountOnline() {
		$count = $this->Record->select()->from("lh_chat_online_user")->numResults();
        if (!$count){$count=0;}
		return ($count);
	}
}
?>