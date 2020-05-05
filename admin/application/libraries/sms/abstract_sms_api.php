<?php

/**
 *
 * Defines general behavior of SMS API classes.
 *
 * General behaviors include:
 * * Generate Content
 * * Send Content
 *
 *
 * @category SMS
 * @version 2.8.18
 * @copyright 2013-2016 TripleOneTech
 *
 */
abstract class Abstract_sms_api {

    public $call_socks5_proxy;
    public $call_socks5_proxy_login;
    public $call_socks5_proxy_password;
    public $call_http_proxy_host;
    public $call_http_proxy_port;
    public $call_http_proxy_login;
    public $call_http_proxy_password;

	function __construct() {
		$this->CI = &get_instance();
		$this->utils = $this->CI->utils;

		$this->call_socks5_proxy          = $this->CI->config->item('call_socks5_proxy');
		$this->call_socks5_proxy_login    = $this->CI->config->item('call_socks5_proxy_login');
		$this->call_socks5_proxy_password = $this->CI->config->item('call_socks5_proxy_password');
		$this->call_http_proxy_host       = $this->CI->config->item('call_http_proxy_host');
		$this->call_http_proxy_port       = $this->CI->config->item('call_http_proxy_port');
		$this->call_http_proxy_login      = $this->CI->config->item('call_http_proxy_login');
		$this->call_http_proxy_password   = $this->CI->config->item('call_http_proxy_password');
	}

	const SMS_API = 'SMS';
    const STATUS_OK = '200';
	// -- functions for sending sms --
	public abstract function getUrl();
	public abstract function getFields($mobile, $content);
	# Parse the response, return error message if any
	public abstract function getErrorMsg($response);
	# Parse the response, return a boolean indicating whether the send succeed
	public abstract function isSuccess($response);

	public function getPlatformCode(){
		return SMS_API; #API number for SMS apis
	}

	// $content needs to be in UTF-8 encoding
	public function send($mobile, $content){
		$ch = curl_init();

		$fields      = $this->configCurl($ch, $mobile, $content);
		$result      = curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header      = substr($result, 0, $header_size);
		$statusCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$errCode     = curl_errno($ch);
		$error       = curl_error($ch);
		$statusText  = $errCode . ':' . $error;


		$this->utils->debug_log("===============sms api curl response ", $result);
		if($result === false) {
			$this->utils->error_log("Error sending SMS through ".get_class($this). "...", curl_error($ch));
		}
		curl_close($ch);

		#save to response result
		$this->CI->load->model(array('response_result'));
		$player_id = NULL;
		$resultAll['type']     = 'sms';
        $resultAll['url']      = $this->getUrl();
		$resultAll['params']   = $fields;
		$resultAll['content']  = $result;
		$flag = $this->isSuccess($result) ? Response_result::FLAG_NORMAL:Response_result::FLAG_ERROR;

		$response_result_id = $this->CI->response_result->saveResponseResult($this->getPlatformCode(), $flag,
			self::SMS_API, self::SMS_API, json_encode($resultAll), $statusCode, $statusText, $header,
			array('player_id' => $player_id, 'related_id3' => $mobile));

		return $result;
	}

	// perform curl configuration, by default post the fields returned by getFields.
	// Subclasses can overwrite this function for non-default curl configuration
	protected function configCurl($handle, $mobile, $content) {
		$url = $this->getUrl();
		$content = $this->signContent($content);
		$fields = $this->getFields($mobile, $content);
		$fields_string = http_build_query($fields);

		curl_setopt($handle,CURLOPT_URL,$url);
		curl_setopt($handle,CURLOPT_POST,count($fields));
		curl_setopt($handle,CURLOPT_POSTFIELDS,$fields_string);
		curl_setopt($handle,CURLOPT_RETURNTRANSFER,1);
        $this->setCurlProxyOptions($handle);

		return $fields;
	}

	// Some API will require user to append signature to SMS content
	// Subclasses can overwrite this function to provide implementation of the signature
	protected function signContent($content) {
		return $content;
	}

	# Returns a string describing the balance. Empty string if balance query is not supported by API.
	public function getBalanceString() {
		return '';
	}

	# set proxy
	public function setCurlProxyOptions($curl_resources){
        // set proxy
        $settle_proxy = FALSE;
        if ($settle_proxy === FALSE && !empty($this->call_socks5_proxy)) {
            $this->CI->utils->debug_log('http call with socks5 proxy', $this->call_socks5_proxy);
            curl_setopt($curl_resources, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            curl_setopt($curl_resources, CURLOPT_PROXY, $this->call_socks5_proxy);
            if (!empty($this->call_socks5_proxy_login) && !empty($this->call_socks5_proxy_password)) {
                curl_setopt($curl_resources, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
                curl_setopt($curl_resources, CURLOPT_PROXYUSERPWD, $this->call_socks5_proxy_login . ':' . $this->call_socks5_proxy_password);
            }
            $settle_proxy = TRUE;
        }

        if($settle_proxy === FALSE && !empty($this->call_http_proxy_host)){
            //http proxy
            $this->CI->utils->debug_log('http call with http proxy', $this->call_http_proxy_host);
            curl_setopt($curl_resources, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($curl_resources, CURLOPT_PROXY, $this->call_http_proxy_host);
            curl_setopt($curl_resources, CURLOPT_PROXYPORT,  (empty($this->call_http_proxy_port)) ? 3128 : $this->call_http_proxy_port);
            if (!empty($this->call_http_proxy_login) && !empty($this->call_http_proxy_password)) {
                curl_setopt($curl_resources, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
                curl_setopt($curl_resources, CURLOPT_PROXYUSERPWD, $this->call_http_proxy_login . ':' . $this->call_http_proxy_password);
            }
        }

        return $this;
    }

    #get mobile without '+', country code and the starting '0'
    public function getMobile($mobile) {
        # Trim spaces and '+' first
        $mobile = trim($mobile);
        $mobile = trim($mobile, '+');

        if(strpos($mobile, '+') === 0) {
            $mobile = substr($mobile, 1);
        }

        # Prefix with default country code, if defined
        $defaultCountryCode = $this->CI->config->item('sms_default_country');
        if($defaultCountryCode) {
            if(strpos($mobile, $defaultCountryCode) === 0) {
                $mobile = substr($mobile, strlen($defaultCountryCode));
            }
        }

        if(strpos($mobile, '0') === 0) {
            $mobile = substr($mobile, 1);
        }

        return $mobile;
    }

}
