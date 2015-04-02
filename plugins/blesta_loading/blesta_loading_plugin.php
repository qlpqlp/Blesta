<?php
/**
 * Blesta Loading plugin handler
 *
 * @package blesta
 * @subpackage blesta.plugins.Blesta_Loading
 * @copyright Copyright (c) 1998, Infoscan - Informática, Lda.
 * @link http://www.weblx.net/ WebLX
 */
class BlestaLoadingPlugin extends Plugin {

	public function __construct() {
		Language::loadLang("live_chat_plugin", null, dirname(__FILE__) . DS . "language" . DS);
		$this->loadConfig(dirname(__FILE__) . DS . "config.json");
	}


	/**
	 * Performs any necessary bootstraping actions
	 *
	 * @param int $plugin_id The ID of the plugin being installed
	 */
	public function install($plugin_id) {

        //nothing to do

	}

	/**
	 * Performs migration of data from $current_version (the current installed version)
	 * to the given file set version
	 *
	 * @param string $current_version The current installed version of this plugin
	 * @param int $plugin_id The ID of the plugin being upgraded
	 */
	public function upgrade($current_version, $plugin_id) {
		// Upgrade if possible
		if (version_compare($this->getVersion(), $current_version, ">")) {
			// Handle the upgrade, set errors using $this->Input->setErrors() if any errors encountered
          // Upgrade to 1.0.4
			if (version_compare($current_version, "1.0.3", "<")) {

        //remove the code to make the magic
        $findcode = '<?php include(PLUGINDIR . DS . "blesta_loading" . DS . "views" . DS . "default" . DS . "admin_blesta_loading_include.pdt");?>';
        $rmbchatcode = '';
        $theme=scandir(VIEWDIR . "admin");
        for ($x=0; $x<count($theme); $x++){
            if (is_dir(VIEWDIR . "admin" . DS . $theme[$x])){
              if($theme[$x]!= "." && $theme[$x]!= ".."){
                $path_to_file = VIEWDIR . "admin" . DS . $theme[$x] . DS . "structure.pdt";
                $rmchat = file_put_contents($path_to_file, str_replace($findcode, $rmbchatcode, file_get_contents($path_to_file)));
              }
            }
        }

	        }
       	}
	}

	/**
	 * Performs any necessary cleanup actions
	 *
	 * @param int $plugin_id The ID of the plugin being uninstalled
	 * @param boolean $last_instance True if $plugin_id is the last instance across all companies for this plugin, false otherwise
	 */
	public function uninstall($plugin_id, $last_instance) {


		if ($last_instance) {
        //nothing to do
		}
	}

	/**
	 * Execute evet on Appcontroller.structure
	 */
   public function getEvents() {
        return array(
            array(
                'event' => "Appcontroller.structure",
                'callback' => array("this", "BlestaLoadingaddCode")
            )
            // Add multiple events here
        );
    }

	/**
	 * On Appcontroller.structure run this
	 */
    public function BlestaLoadingaddCode($event) {

        // Fetch current return val
        $result = $event->getReturnVal();

        $params = $event->getParams();

        // Set return val if not set
        if (!isset($result['body_start']))
                $result['body_start'] = null;

        // Set return val if not set
        if (!isset($result['head']))
                $result['head'] = null;

        // Update return val -- ONLY set if client portal
        if ($params['portal'] == "admin")
            $result['body_start']["loadinginclude"] = '
<!-- display blesta loading-->
<div class="pace pace-active"><div class="pace-progress" data-progress="50" data-progress-text="50%" style="width: 50%;"><div class="pace-progress-inner"></div></div><div class="pace-activity"></div></div>
<!-- end display blesta loading-->
            ';
            $result['head']["loadinginclude"] = '
<!-- display blesta loading-->
<style type="text/css">
<!--
.pace {
  -webkit-pointer-events: none;
  pointer-events: none;
  -webkit-user-select: none;
  -moz-user-select: none;
  user-select: none;
}

.pace .pace-activity {
  display: block;
  position: fixed;
  z-index: 2000;
  height: 100%;
  width: 100%;
  margin: 0px;
  padding: 0px;
  background-color: black;
  opacity: 0.7;
}

.pace .pace-activity::after {
    position: absolute important!;
    display: block;
    border: 5px solid #fff;
    border-radius: 50%;
    content: "";
}

.pace .pace-activity::after {
    bottom: 50%;
    left: 50%;
    position: absolute;
    width: 40px;
    height: 40px;
    border-top-color: rgba(50, 50, 50, .2);
    border-bottom-color: rgba(50, 50, 50, .2);
    -webkit-animation: pace-rotation 1s linear infinite;
    animation: pace-rotation 1s linear infinite;
}

@-webkit-keyframes pace-rotation {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(359deg); }
}
@keyframes pace-rotation {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(359deg); }
}
-->
</style>
<script type="text/javascript">

$(document).ready(function(){

    $( "body" ).on( "click", ".ajax", function() {
       $(".pace").show();
    });

    $(".pace").one("ajaxSend", function() {
        $(this).show();
    }).bind("ajaxStop", function() {
        $(this).hide();
    }).bind("ajaxError", function() {
        $(this).hide();
    });

    $(".pace").css("display","none");

});
</script>
<!-- end display blesta loading-->
                            ';


        // Update return val
        $event->setReturnVal($result);
    }

}
?>