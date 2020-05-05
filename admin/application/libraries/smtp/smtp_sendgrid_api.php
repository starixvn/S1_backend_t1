<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/abstract_smtp_api.php';

/**
 *
 * T1 - SENDGRID SMTP API LIBRARY (uses abstract_smtp_api class)
 *
 *
 * @category SMTP
 * @version 1.0.0
 * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
 * @copyright 2013-2018 TripleOneTech
 *
 */
class Smtp_sendgrid_api extends Abstract_smtp_api{

    /**
     * Initialize default variables
     */
    public $api_url = 'https://api.sendgrid.com/v3/mail/send';

    public $_SMTP_API_status_codes = array(
        '200' => 'Your message is valid, but it is not queued to be delivered.',
        '202' => 'Your message is both valid, and queued to be delivered.',
        '401' => 'You do not have authorization to make the request.',
        '403' => 'FORBIDDEN',
        '404' => 'The resource you tried to locate could not be found or does not exist.',
        '405' => 'METHOD NOT ALLOWED',
        '413' => 'The JSON payload you have included in your request is too large.',
        '415' => 'UNSUPPORTED MEDIA TYPE',
        '429' => 'The number of requests you have made exceeds SendGrid’s rate limitations',
        '500' => 'An error occurred on a SendGrid server.',
        '503' => 'The SendGrid v3 Web API is not available.',
    );

	public $parameters = array(
		'personalizations' => array(
            array(
    			'to' => array(),
    			'subject' => ''
            ),
		),
		'from' => array(
			'email' => ''
		),
		'content' => array(
            array(
    			'type' => 'text/html',
    			'value' => ''
            )
		)
	);

	public function __construct(){
		parent::__construct();
	}

	/**
	 * Set Api Credentials
	 *
	 * @param void
	 * @return void
	 * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
	 */
	public function setApiKeys(){
		$API_INFO = $this->CI->config->item('smtp_api_info');

        if(  !is_array($API_INFO) || empty($API_INFO) || 
            (!isset($API_INFO['api_key']) || empty($API_INFO['api_key'])) || 
            (!isset($API_INFO['api_name']) || empty($API_INFO['api_name'])) || 
            (!isset($API_INFO['api_key_id']) || empty($API_INFO['api_key_id'])))
        {
            if($this->debug) $this->print_log("SENDGRID SMTP API: Unable to setup API keys due to missing configurations");
            $this->utils->debug_log('SENDGRID SMTP API: Unable to setup API keys due to missing configurations');
            return false;
        }

		$this->api_key = $API_INFO['api_key'];
		$this->api_name = $API_INFO['api_name'];
		$this->api_key_id = $API_INFO['api_key_id'];

        return $this->api_key;
	}

    /**
     * Get Api Credentials
     *
     * @param void
     * @return string API KEY
     * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
     */
    public function getApiKeys(){
        return $this->api_key;
    }

	/**
	 * Return CURL headers
	 *
	 * @param void
	 * @return array API Headers
	 * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
	 */
	public function getHeaders(){
		return array(
			'Content-Type: application/json',
			'Authorization: Bearer ' . $this->api_key
		);
	}

	/**
	 * Return API URL
	 *
	 * @param void
	 * @return string API URL
	 * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
	 */
	public function getURL()
    {
        return $this->api_url;
    }


    /**
     * Sets the API Request Parameters for sending emails
     * 
     * @param string $to        
     * @param string $from      
     * @param string $from_name 
     * @param string $subject   
     * @param string $content   
     * @param string $cc        
     * @param string $bcc       
     * @return array API Request Parameters
     * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
     */
    public function setParameters($to, $from, $from_name, $subject, $content, $cc = null, $bcc = null){

        if($this->debug) $this->print_log("===============setParameters arguments' values: TO => $to | FROM => $from | FROM_NAME => $from_name | SUBJECT => $subject | CONTENT => $content | CC => $cc | BCC => $bcc");

        $this->utils->debug_log("===============setParameters arguments' values: TO => $to | FROM => $from | FROM_NAME => $from_name | SUBJECT => $subject | CONTENT => $content | CC => $cc | BCC => $bcc");
    	if(empty(trim($to)) || empty(trim($from)) || empty(trim($from_name)) || empty(trim($subject)) || empty(trim($content)))
    		return false;

    	$tos = explode(',',$to);
    	$ccs = explode(',',$cc);
    	$bccs = explode(',',$bcc);

    	// -- set TO recepients
    	foreach ($tos as $to_key => $to_item) {
			$this->parameters['personalizations'][0]['to'][] = array('email' => $to_item);
		}

		// -- set FROM details from operator settings
    	$this->parameters['from']['email'] = $from;
    	$this->parameters['from']['name']  = $from_name;

    	// -- set CC recepients
    	if(isset($cc) && trim($cc) != null){
    		$this->parameters['personalizations'][0]['cc'] = array();

    		$ccs = explode(',',$cc);
    		foreach ($ccs as $cc_key => $cc_item) {
				$this->parameters['personalizations'][0]['cc'][] = array('email' => $cc_item);
			}
    	}

    	// -- set BCC recepients
    	if(isset($bcc) && trim($bcc) != null){
    		$this->parameters['personalizations'][0]['bcc'] = array();

    		$bccs = explode(',',$bcc);
    		foreach ($bccs as $bcc_key => $bcc_item) {
				$this->parameters['personalizations'][0]['bcc'][] = array('email' => $bcc_item);
			}
    	}

        $this->parameters['personalizations'][0]['subject'] = $subject;
    	$this->parameters['content'][0]['value'] = $content;

    	return $this->parameters;
    }


    /**
     * Get error messages from API
     * 
     * @param  array $response API response
     * @return array           Array of error messages
     * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
     */
    public function getErrorMessages($response)
    {
        $result = (array) json_decode($response);
        $error_messages = array();

        if(isset($result['errors']) && is_array($result['errors']))
        {
        	foreach ($result['errors'] as $errors_key => $errors) {
                
                $error_messages[] = $errors->message;
        	}
        }

        return $error_messages;
    }

    /**
     * Checks if sending of email was a success
     * 
     * @param  json  $response API Respone
     * @return boolean         API Response Status
     * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
     */
    public function isSuccess($response)
    {
    	if(trim($response) == '')
    		return true;

    	$result = (array) json_decode($response);

        if(isset($result['errors']) && is_array($result['errors']))
        	return false;
        
        return true;
    }

}