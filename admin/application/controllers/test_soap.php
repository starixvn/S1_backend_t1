<?php

class Test_soap extends CI_Controller {
	const LOGIN_NAME = 'dw171208';
	const PIN_CODE = '04352d';
	const MG_URL = "https://entservices.totalegame.net?wsdl";
	function __construct() {
		parent::__construct();
		$this->ci = &get_instance();
	}

	function index() {
		$this->loginMG();
	}

	private function loginMG() {
		$client = new SoapClient(self::MG_URL, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));

		/**
		 * Try to login
		 */
		try {
			$result = $client->IsAuthenticate(
				array(
					'loginName' => self::LOGIN_NAME,
					'pinCode' => self::PIN_CODE,
				)
			);

			//var_dump($client->__getFunctions());
		} catch (Exception $e) {
			/**
			 * Setting error output to Session
			 */
			echo '<pre>Error: ' . print_r($e, true) . '</pre>';
			exit;
		}
	}

}