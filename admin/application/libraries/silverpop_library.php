<?php
class Silverpop_library {

	const INVALID_USER_SESSION = 145;

	const VISIBILITY_PRIVATE 	= 0;
	const VISIBILITY_SHARED 	= 1;
	
	const LIST_TYPE_DATABASES 						= 0;
	const LIST_TYPE_QUERIES 						= 1;
	const LIST_TYPE_DATABASES_CONTACT_LISTS_QUERIES = 2;
	const LIST_TYPE_TEST_LISTS 						= 5;
	const LIST_TYPE_SEED_LISTS 						= 6;
	const LIST_TYPE_SUPPRESSION_LISTS 				= 13;
	const LIST_TYPE_RELATIONAL_TABLES 				= 15;
	const LIST_TYPE_CONTACT_LISTS 					= 18;

	const CREATED_FROM_ADDED_MANUALLY = 1;

	protected $CI 			= null;
	protected $options 		= null;
	protected $sessionId 	= null;
	protected $lastRequest 	= null;
	protected $lastResponse = null;
	protected $lastFault 	= null;

	public function __construct($options) {
		$CI =& get_instance();
		$this->options = $options;
	}

	public function login() {

		$this->sessionId = null;

		$username = $this->options['username'];
		$password = $this->options['password'];

		$request = "<Login><USERNAME><![CDATA[{$username}]]></USERNAME><PASSWORD><![CDATA[{$password}]]></PASSWORD></Login>";

		try {
			$response = $this->execute($request);
		} catch (Exception $e) {
			throw new Exception('Login failed: ' . $e->getMessage());
		}

		if ( ! isset($response['RESULT']['SESSIONID'])) {
			throw new Exception('Login response did not include SESSIONID');
		}

		$this->sessionId = $response['RESULT']['SESSIONID'];

		return $response['RESULT'];

	}
	
	public function logout() {

		$request = "<Logout/>";

		try {
			$response = $this->execute($request);
		} catch (Exception $e) {
			throw new Exception('Logout failed: ' . $e->getMessage());
		}

		if ( ! isset($response['RESULT']['SUCCESS']) || $response['RESULT']['SUCCESS'] != 'TRUE') {
			throw new Exception('Logout failed');
		}

		$this->sessionId = null;

		return $response['RESULT'];

	}

	public function sendMailing($mailingId, $recipientEmail) {

		$request = "<SendMailing><MailingID>{$mailingId}</MailingID><RecipientEmail>{$recipientEmail}</RecipientEmail></SendMailing>";

		try {
			$response = $this->execute($request);
		} catch (Exception $e) {
			throw new Exception('SendMailing failed: ' . $e->getMessage());
		}

		if ( ! isset($response['RESULT']['SUCCESS']) || $response['RESULT']['SUCCESS'] != 'TRUE') {
			throw new Exception('SendMailing failed');
		}

		return $response['RESULT'];

	}

	public function addRecipient($listId, $player) {

		$columns = array_filter(array(
			'Email' 							=> isset($player['email']) ? $player['email'] : null,
			'City' 								=> isset($player['city']) ? $player['city'] : null,
			'Country' 							=> isset($player['residentCountry']) ? $player['residentCountry'] : null,
			'Currency' 							=> 'CNY',
			'Date of Birth' 					=> isset($player['birthdate']) ? $player['birthdate'] : null,
			'Date Registered' 					=> isset($player['createdOn']) ? $player['createdOn'] : null,
			'First Name' 						=> isset($player['firstName']) ? $player['firstName'] : null,
			'Gender' 							=> isset($player['gender']) ? $player['gender'] : null,
			'Language' 							=> isset($player['language']) ? $player['language'] : null,
			'Last Login' 						=> isset($player['lastLoginTime']) ? $player['lastLoginTime'] : null,
			'Last Name' 						=> isset($player['lastName']) ? $player['lastName'] : null,
			'User ID' 							=> isset($player['playerId']) ? $player['playerId'] : null,
			'Username' 							=> isset($player['username']) ? $player['username'] : null,

			'Contact number' 					=> isset($player['contactNumber']) ? $player['contactNumber'] : null,
			'IM Contact' 						=> isset($player['imAccount']) ? $player['imAccount'] : null,
			'First Time Deposit' 				=> isset($player['approved_deposit_count']) && $player['approved_deposit_count'] > 0,
			'First Deposit Date' 				=> isset($player['first_deposit_date']) ? $player['first_deposit_date'] : null,
			'Last Deposit Date' 				=> isset($player['last_deposit_date']) ? $player['last_deposit_date'] : null,
			'Last Activity Date' 				=> isset($player['lastActivityTime']) ? $player['lastActivityTime'] : null,
			'Total Balance' 					=> isset($player['total_total']) ? $player['total_total'] : null,
			'Total Deposit' 					=> isset($player['totalDepositAmount']) ? $player['totalDepositAmount'] : null,
			'Total Deposit Count' 				=> isset($player['approved_deposit_count']) ? $player['approved_deposit_count'] : null,
			'Total Withdrawal' 					=> isset($player['approvedWithdrawAmount']) ? $player['approvedWithdrawAmount'] : null,

			# TOTAL BETS
			'AG Turnover' 						=> isset($player['AG Turnover']) ? $player['AG Turnover'] : null,
			'HB Turnover' 						=> isset($player['HB Turnover']) ? $player['HB Turnover'] : null,
			'ENTWINE Turnover' 					=> isset($player['ENTWINE Turnover']) ? $player['ENTWINE Turnover'] : null,
			'GAMESOS Turnover' 					=> isset($player['GAMESOS Turnover']) ? $player['GAMESOS Turnover'] : null,
			'FG Turnover' 						=> isset($player['FG Turnover']) ? $player['FG Turnover'] : null,
			'ONEWORKS Turnover' 				=> isset($player['ONEWORKS Turnover']) ? $player['ONEWORKS Turnover'] : null,

			# BETS MINUS WINS 
			'AG GGR' 							=> isset($player['AG GGR']) ? $player['AG GGR'] : null,
			'HB GGR' 							=> isset($player['HB GGR']) ? $player['HB GGR'] : null,
			'ENTWINE GGR' 						=> isset($player['ENTWINE GGR']) ? $player['ENTWINE GGR'] : null,
			'GAMESOS GGR' 						=> isset($player['GAMESOS GGR']) ? $player['GAMESOS GGR'] : null,
			'FG GGR' 							=> isset($player['FG GGR']) ? $player['FG GGR'] : null,
			'ONEWORKS GGR' 						=> isset($player['ONEWORKS GGR']) ? $player['ONEWORKS GGR'] : null,

			# BETS MINUS WINS MINUS CASHBACK
			'AG NGR' 							=> isset($player['AG NGR']) ? $player['AG NGR'] : null,
			'HB NGR' 							=> isset($player['HB NGR']) ? $player['HB NGR'] : null,
			'ENTWINE NGR' 						=> isset($player['ENTWINE NGR']) ? $player['ENTWINE NGR'] : null,
			'GAMESOS NGR' 						=> isset($player['GAMESOS NGR']) ? $player['GAMESOS NGR'] : null,
			'FG NGR' 							=> isset($player['FG NGR']) ? $player['FG NGR'] : null,
			'ONEWORKS NGR' 						=> isset($player['ONEWORKS NGR']) ? $player['ONEWORKS NGR'] : null,

			# LAST ACTIVITY DATE
			'AG Last Activity Date' 			=> isset($player['AG Last Activity Date']) ? $player['AG Last Activity Date'] : null,
			'HB Last Activity Date' 			=> isset($player['HB Last Activity Date']) ? $player['HB Last Activity Date'] : null,
			'ENTWINE Last Activity Date' 		=> isset($player['ENTWINE Last Activity Date']) ? $player['ENTWINE Last Activity Date'] : null,
			'GAMESOS Last Activity Date' 		=> isset($player['GAMESOS Last Activity Date']) ? $player['GAMESOS Last Activity Date'] : null,
			'FG Last Activity Date'		 		=> isset($player['FG Last Activity Date']) ? $player['FG Last Activity Date'] : null,
			'ONEWORKS Last Activity Date' 		=> isset($player['ONEWORKS Last Activity Date']) ? $player['ONEWORKS Last Activity Date'] : null,

			# TODO: I DON'T THINK WE HAVE THIS
			'AG Bonus'							=> isset($player['AG Bonus']) ? $player['AG Bonus'] : null, 
			'HB Bonus'							=> isset($player['HB Bonus']) ? $player['HB Bonus'] : null, 
			'ENTWINE Bonus'						=> isset($player['ENTWINE Bonus']) ? $player['ENTWINE Bonus'] : null, 
			'GAMESOS Bonus'						=> isset($player['GAMESOS Bonus']) ? $player['GAMESOS Bonus'] : null, 
			'FG Bonus'							=> isset($player['FG Bonus']) ? $player['FG Bonus'] : null, 
			'ONEWORKS Bonus'					=> isset($player['ONEWORKS Bonus']) ? $player['ONEWORKS Bonus'] : null, 

		));

		$columns_str = "";
		foreach ($columns as $key => $value) {
			$columns_str .= "<COLUMN><NAME>{$key}</NAME><VALUE>{$value}</VALUE></COLUMN>";
		}

		$request = "<AddRecipient><LIST_ID>{$listId}</LIST_ID><CREATED_FROM>" . self::CREATED_FROM_ADDED_MANUALLY . "</CREATED_FROM><UPDATE_IF_FOUND>true</UPDATE_IF_FOUND>" . $columns_str . "</AddRecipient>";

		try {
			$response = $this->execute($request);
		} catch (Exception $e) {
			throw new Exception('AddRecipient failed: ' . $e->getMessage());
		}

		if ( ! isset($response['RESULT']['SUCCESS']) || $response['RESULT']['SUCCESS'] != 'TRUE') {
			throw new Exception('AddRecipient failed');
		}

		return $response['RESULT'];

	}

	public function selectRecipientData($listId, $recipientEmail) {

		$request = "<SelectRecipientData><LIST_ID>{$listId}</LIST_ID><EMAIL>{$recipientEmail}</EMAIL></SelectRecipientData>";

		try {
			$response = $this->execute($request);
		} catch (Exception $e) {
			throw new Exception('SelectRecipientData failed: ' . $e->getMessage());
		}

		if ( ! isset($response['RESULT']['SUCCESS']) || $response['RESULT']['SUCCESS'] != 'TRUE') {
			throw new Exception('SelectRecipientData failed');
		}

		return $response['RESULT'];

	}

	public function getLists($visibility, $listType) {

		$request = "<GetLists><VISIBILITY>{$visibility}</VISIBILITY><LIST_TYPE>{$listType}</LIST_TYPE></GetLists>";

		try {
			$response = $this->execute($request);
		} catch (Exception $e) {
			throw new Exception('GetLists failed: ' . $e->getMessage());
		}

		if ( ! isset($response['RESULT']['SUCCESS']) || $response['RESULT']['SUCCESS'] != 'TRUE') {
			throw new Exception('GetLists failed');
		}

		return $response['RESULT'];

	}
	

	# UTILS -------------------------------------------------------------------------------------------------------------------------------------- //
	public function execute($request) {

		if ($request instanceof SimpleXMLElement) {
			$requestXml = $request->asXML();
		} else {
			$requestXml = "<?xml version=\"1.0\"?>\n<Envelope><Body>{$request}</Body></Envelope>";
		}

		$this->lastRequest = $requestXml;
		$this->lastResponse = null;
		$this->lastFault = null;

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $this->getApiUrl());
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $requestXml);
		curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=UTF-8', 'Content-Length: ' . strlen($requestXml)));
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($curl, CURLOPT_TIMEOUT, 180);

		$responseXml = @curl_exec($curl);

		if ($responseXml === false) {
			throw new Exception('CURL error: ' . curl_error($curl));
		}

		curl_close($curl);

		if ($responseXml === true || !trim($responseXml)) {
			throw new Exception('Empty response from Engage');
		}

		$this->lastResponse = $responseXml;

		$response = @simplexml_load_string('<?xml version="1.0"?>' . $responseXml);

		if ($response === false) {
			throw new Exception('Invalid XML response from Engage');
		}

		if (!isset($response->Body)) {
			throw new Exception('Engage response contains no Body');
		}

		$response = $response->Body;

		$this->checkResult($response);

		return json_decode(json_encode($response), true);

	}

	public function getApiUrl() {

		$url = "https://" . $this->options['apiHost'] . "/XMLAPI";

		if ($this->sessionId !== null) {
			$url .= ';jsessionid=' . urlencode($this->sessionId);
		}
		
		return $url;

	}

	public function checkResult($xml) {

		if (!isset($xml->RESULT)) {
			throw new Exception('Engage XML response body does not contain RESULT');
		}

		if (!isset($xml->RESULT->SUCCESS)) {
			throw new Exception('Engage XML response body does not contain RESULT/SUCCESS');
		}

		$success = strtoupper($xml->RESULT->SUCCESS);

		if (in_array($success, array('TRUE', 'SUCCESS'))) {
			return true;
		}

		if ($xml->Fault) {
			$this->lastFault = $xml->Fault;
			$code = (string)$xml->Fault->FaultCode;
			$error = (string)$xml->Fault->FaultString;
			throw new Exception("Engage fault '{$error}'" . ($code ? "(code: {$code})" : ''));
		}

		throw new Exception('Unrecognized Engage API response');

	}

	public function getLastRequest() {
		return $this->lastRequest;
	}

	public function getLastResponse() {
		return $this->lastResponse;
	}

	public function getLastFault() {
		return $this->lastFault;
	}

}