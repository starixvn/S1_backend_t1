<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class acuris_api {

	private $headers = array('Content-Type: application/json; charset=utf-8');

	public function __construct() {
		$this->CI =& get_instance();
		$this->acuris_c6_config = $this->CI->utils->getConfig('acuris_c6_config');
		$this->acuris_c6_sanctions = isset($this->acuris_c6_config['sanctions']) ? $this->acuris_c6_config['sanctions'] : null;
		$this->api_host = isset($this->acuris_c6_config['api_host']) ? $this->acuris_c6_config['api_host'] : null;
		$this->api_key = isset($this->acuris_c6_config['api_key']) ? $this->acuris_c6_config['api_key'] : null;
		$this->api_threshold = isset($this->acuris_c6_config['Threshold']) ? $this->acuris_c6_config['Threshold'] : null;
		$this->request_url_person_search = isset($this->acuris_c6_config['request_url_person_search']) ? $this->acuris_c6_config['request_url_person_search'] : null;
	}

	// this function authenticate Search Person in Acuris
    public function auth_sp($playerDetails) {
    	$response_data = false;

    	if(empty($playerDetails)){
    		return array(
    			'status'	=> 'error',
    			'response' 	=> lang("Player Details are empty or invalid."),
    		);
    	}

    	if(empty($this->api_host) || empty($this->api_key)  || empty($this->request_url_person_search)) {
    		return array(
    			'status'	=> 'error',
    			'response' 	=> lang("Acuris credential and API url not configure or invalid."),
    		);
    	}

    	//data with consist player information to validate to acuris c6
    	if(!empty($playerDetails)){
    		$this->headers[] = 'apiKey: ' . $this->api_key;
    		$this->headers[] = 'Host: ' . $this->api_host;

			$url = $this->request_url_person_search;

			$this->CI->utils->info_log('<<====== Acuris_c6_api submit_c6_validation url  ======>>', $url);
			$params = array(
							'Threshold' 			=> isset($this->api_threshold) ? $this->api_threshold : FALSE,
							'PEP' 					=> isset($this->acuris_c6_sanctions['PEP']['enable']) ? $this->acuris_c6_sanctions['PEP']['enable'] : FALSE,
							'PreviousSanctions' 	=> isset($this->acuris_c6_sanctions['PreviousSanctions']['enable']) ? $this->acuris_c6_sanctions['PreviousSanctions']['enable'] : FALSE,
							'CurrentSanctions' 		=> isset($this->acuris_c6_sanctions['CurrentSanctions']['enable']) ? $this->acuris_c6_sanctions['CurrentSanctions']['enable'] : FALSE,
							'LawEnforcement' 		=> isset($this->acuris_c6_sanctions['LawEnforcement']['enable']) ? $this->acuris_c6_sanctions['LawEnforcement']['enable'] : FALSE,
							'FinancialRegulator' 	=> isset($this->acuris_c6_sanctions['FinancialRegulator']['enable']) ? $this->acuris_c6_sanctions['FinancialRegulator']['enable'] : FALSE,
							'Insolvency'			=> isset($this->acuris_c6_sanctions['Insolvency']['enable']) ? $this->acuris_c6_sanctions['Insolvency']['enable'] : FALSE,
							'DisqualifiedDirector' 	=> isset($this->acuris_c6_sanctions['DisqualifiedDirector']['enable']) ? $this->acuris_c6_sanctions['DisqualifiedDirector']['enable'] : FALSE,
							'AdverseMedia' 			=> isset($this->acuris_c6_sanctions['AdverseMedia']['enable']) ? $this->acuris_c6_sanctions['AdverseMedia']['enable'] : FALSE,
							'Forename' 				=> $playerDetails["firstName"],
							'Middlename' 			=> null,
							'Surname' 				=> $playerDetails["lastName"],
							'DateOfBirth' 			=> date('Y-m-d', strtotime($playerDetails["birthdate"])),
							'YearOfBirth' 			=> date('Y', strtotime($playerDetails["birthdate"])),
							'Address' 				=> $playerDetails["address"],
							'City' 					=> $playerDetails["city"],
							'County' 				=> null,
							'Postcode' 				=> $playerDetails["zipcode"],
							'Country' 				=> $playerDetails["residentCountry"],
						);

			$this->CI->utils->debug_log('<<====== Acuris_c6_api submit_c6_validation url params  ======>>', $params);

	    	$response = $this->curl($url, $params);
	    	$response_data = json_decode($response, TRUE);
			$this->CI->utils->info_log('<<====== Acuris_c6_api submit_c6_validation response ======>>', $response_data);
    	}
    	
    	return $response_data;
    }

	private function curl($url, $params = null) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_SSL_VERIFYHOST => FALSE,
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($params),
			CURLOPT_HTTPHEADER => $this->headers,
		));

		if ($call_socks5_proxy = @$this->CI->utils->getConfig('acuris_c6_config')['call_socks5_proxy']) {
	        curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
	        curl_setopt($curl, CURLOPT_PROXY, $call_socks5_proxy);
		}
		
		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return "cURL Error #:" . $err;
		} else {
		  return $response;
		}

	}

}