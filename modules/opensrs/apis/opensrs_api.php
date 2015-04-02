<?php
/**
 * Opensrs API processor
 *
 * @package opensrs
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "opensrs_response.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "commands" . DIRECTORY_SEPARATOR . "opensrs_all.php";
class OpensrsApi {

	const SANDBOX_URL = "https://horizon.opensrs.net";
	const LIVE_URL = "https://rr-n1-tor.opensrs.net";

	/**
	 * @var string The user to connect as
	 */
	private $user;
	/**
	 * @var string The key to use when connecting
	 */
	private $key;
	/**
	 * @var boolean Whether or not to process in sandbox mode (for testing)
	 */
	private $sandbox;
	/**
	 * @var array An array representing the last request made
	 */
	private $last_request = array('url' => null, 'args' => null);
	
	/**
	 * Sets the connection details
	 *
	 * @param string $user The user to connect as
	 * @param string $key The key to use when connecting
	 * @param boolean $sandbox Whether or not to process in sandbox mode (for testing)
	 */
	public function __construct($user, $key, $sandbox = true) {
		$this->user = $user;
		$this->key = $key;
		$this->sandbox = $sandbox;
	}
	
	/**
	 * Submits a request to the API
	 *
	 * @param string $command The command to submit
	 * @param array $args An array of key/value pair arguments to submit to the given API command
	 * @return OpensrsResponse The response object
	 */
	public function submit($command, array $args = array()) {

		$url = self::LIVE_URL;
		if ($this->sandbox)
			$url = self::SANDBOX_URL;


		$args['apikey'] = $this->user;
		$args['password'] = $this->key;
		$args['responseformat'] = "XML";

        $semformat = $command;
        $command = "/".str_replace("_", "/", $command)."/";
		$this->last_request = array(
			'url' => $url,
			'args' => $args
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url . $command ."?" . http_build_query($args));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$response = curl_exec($ch);
//mail("vidal@weblx.pt", $semformat."<->".$command, $response);//echo $response;
		return new OpensrsResponse($response);
	}
	
	/**
	 * Returns the details of the last request made
	 *
	 * @return array An array containg:
	 * 	- url The URL of the last request
	 * 	- args The paramters passed to the URL
	 */
	public function lastRequest() {
		return $this->last_request;
	}
}
?>