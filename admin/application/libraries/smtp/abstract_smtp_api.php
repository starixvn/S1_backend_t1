<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * Defines general behavior of SMTP API classes.
 *
 *
 * @category SMTP
 * @version 1.0.0
 * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
 * @copyright 2013-2018 TripleOneTech
 *
 */
abstract class Abstract_smtp_api {

	/**
     * Initialize default variables
     */
    public $api_url = null;
    public $api_key = null;
    public $api_name = null;
    public $api_key_id = null;

	public $_SMTP_API_status_codes = array(
		'200' => 'OK',
		'202' => 'ACCEPTED',
		'401' => 'UNAUTHORIZED',
		'403' => 'FORBIDDEN',
		'404' => 'NOT FOUND',
		'405' => 'METHOD NOT ALLOWED',
		'413' => 'PAYLOAD TOO LARGE',
		'415' => 'UNSUPPORTED MEDIA TYPE',
		'429' => 'TOO MANY REQUESTS',
		'500' => 'SERVER UNAVAILABLE',
		'503' => 'SERVICE NOT AVAILABLE',
	);

	public $print_debugger = '';
	public $debug = false;

	const SMTP_API = "SMTP";

	/**
	 * initialize setters / getters
	 */
	
	public abstract function setApiKeys();
	public abstract function getApiKeys();
	public abstract function setParameters($to, $from, $from_name, $subject, $content, $cc = null, $bcc = null);
	public abstract function getHeaders();
	public abstract function getURL();
	public abstract function getErrorMessages($response);
	public abstract function isSuccess($response);

	public function __construct(){
		$this->CI = &get_instance();
		$this->utils = $this->CI->utils;

		$this->utils->debug_log('INITIALIZE SMTP API');
		$this->setApiKeys();
	}

	public function getPlatformCode(){
		return SMTP_API;
	}
	
	/**
	 * Send email thru API
	 * 
	 * @param  string $to        Recipient's email
	 * @param  string $from      Sender's email
	 * @param  string $from_name Sender's name
	 * @param  string $subject   Email subject
	 * @param  string $content   Email content
	 * @param  string $cc        Email's Carbon Copy recipient
	 * @param  string $bcc       Emails's Blind Carbon Copy recipient
	 * @return array/boolean     API RESPONSE
	 * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
	 */
	public function sendEmail($to, $from, $from_name, $subject, $content, $cc = null, $bcc = null, $debug = false){

		if($debug)
			$this->debug = true;

		if(empty($this->getApiKeys())){
			if($this->debug) $this->print_log("===============Failed to setup SMTP API Keys");
			$this->utils->debug_log("===============Failed to setup SMTP API Keys");
			return false;
		}

		// -- Setup API parameters based on given data
		$parameter   = $this->setParameters($to, $from, $from_name, $subject, $content, $cc = null, $bcc = null);

		if(!$parameter){
			if($this->debug) $this->print_log("===============Failed to generate proper SMTP API parameter: ".var_export($parameter,true));
			$this->utils->debug_log("===============Failed to generate proper SMTP API parameter ", $parameter);
			return false;
		}
		else{
			if($this->debug) $this->print_log("===============GENERATED API PARAMETERS:" . var_export($parameter,true));
			$this->utils->debug_log("===============GENERATED API PARAMETERS: ", $parameter);
		}

		// -- set up CURL handler
		$ch 		 = curl_init();
		$fields 	 = $this->configCurl($ch, $parameter);
		$result      = curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header      = substr($result, 0, $header_size);
		$statusCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$errCode     = curl_errno($ch);
		$error       = curl_error($ch);

		if($this->debug) $this->print_log("===============SMTP API curl response:" . var_export($result,true));
		$this->utils->debug_log("===============SMTP API curl response ", $result);

		if($result === false) {
			if($this->debug) $this->print_log("===============Error sending SMTP through:" . get_class($this). '... '. var_export($error,true));

			$this->utils->error_log("===============Error sending SMTP through ".get_class($this). "...", $error);
		}

		curl_close($ch);

		// -- Use API's default status code message
		if($statusCode == 0)
			$statusText  = $errCode . ' : ' . $error;
		else
			$statusText = $statusCode . ' : ' . $this->getDefaultStatusMessage($statusCode);
		

		// -- save to response result
		$this->CI->load->model(array('response_result','player_model'));
		$player_id = NULL;
		$result_all = array(
			'type' 		=> 'smtp',
			'url' 		=> $this->getUrl(),
			'params' 	=> $fields,
			'content' 	=> $result,
		);

		$player_id = $this->CI->player_model->getPlayerIdByEmail($to);

		$isSuccess = $this->isSuccess($result);

		if($this->debug) $this->print_log("=============== SMTP isSuccess = " . var_export($isSuccess, true));
		$this->utils->debug_log("=============== SMTP isSuccess = ", $isSuccess);

		$flag = Response_result::FLAG_NORMAL;

		if(!$this->isSuccess($result) || $statusCode == 0) {
			$flag = Response_result::FLAG_ERROR;

			// -- use API's actual error message if the API call was unsuccessful
			$api_error_message = $this->getErrorMessages($result);
			if(!empty($api_error_message)){
				$statusText = $statusCode .' : '. json_encode($api_error_message);
			}
		}
		
		$response_result_id = $this->CI->response_result->saveResponseResult($this->getPlatformCode(), $flag, self::SMTP_API, json_encode($fields), json_encode($result_all), $statusCode, $statusText, $result, array('player_id' => $player_id, 'related_id3' => $to));

		if($this->debug) echo $this->print_debugger;

		return $result;
	}

	/**
	 * Configures Curl Options
	 * 
	 * @param  curl handle $handle CURL
	 * @param  array $parameters   API Parameters
	 * @return array               API Parameters
	 * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
	 */
	protected function configCurl($handle, $parameters) {
		$url = $this->getUrl();
		if($this->debug) $this->print_log("===============SMTP API CURL URL =>  " .var_export($url, true));
		$this->utils->debug_log("===============SMTP API CURL URL =>  $url", $url);
		$headers = $this->getHeaders();

		if(!empty($headers))
			curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
		
		curl_setopt($handle,CURLOPT_URL, $url);
		curl_setopt($handle,CURLOPT_POST, TRUE);
		curl_setopt($handle,CURLOPT_POSTFIELDS, json_encode($parameters));
		curl_setopt($handle,CURLOPT_RETURNTRANSFER, TRUE);

		return $parameters;
	}

	/**
	 * Returns API default status message
	 * 
	 * @param  string $status_code response status code
	 * @return string              response status message
	 * @author Cholo Miguel Antonio <cholo.php.ph@triplonetech.net>
	 */
	protected function getDefaultStatusMessage($status_code){
		$message = '';

		if (array_key_exists($status_code, $this->_SMTP_API_status_codes))
			return lang($this->_SMTP_API_status_codes[$status_code]);

		return '';

	}

	/**
	 * Prepares debug logs for testing 
	 * @param  string $message Log message
	 * @return void
	 */
	public function print_log($message){
		$this->print_debugger .= date('Y-m-d H:i:s') ." ----->  ". $message . "\n";
	}

}