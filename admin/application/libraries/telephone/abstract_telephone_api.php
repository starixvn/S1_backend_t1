<?php

/**
 *
 * Defines general behavior of telephone API classes.
 *
 * General behaviors include:
 * * Call a phone number
 *
 *
 * @category Telephone
 * @version 2.8.18
 * @copyright 2013-2016 TripleOneTech
 *
 */
abstract class Abstract_telephone_api {
    public $call_socks5_proxy;
    public $call_socks5_proxy_login;
    public $call_socks5_proxy_password;

    public $call_http_proxy_host;
    public $call_http_proxy_port;
    public $call_http_proxy_login;
    public $call_http_proxy_password;

	protected $PLATFORM_CODE;
	protected $SYSTEM_TYPE_ID;

	function __construct() {
		$this->CI = &get_instance();
		$this->utils = $this->CI->utils;
		$this->PLATFORM_CODE = $this->getPlatformCode();
		$this->SYSTEM_TYPE_ID = $this->getPlatformCode();

		$this->loadSystemInfo();

        $this->call_socks5_proxy = $this->getSystemInfo('call_socks5_proxy');
        $this->call_socks5_proxy_login = $this->getSystemInfo('call_socks5_proxy_login');
        $this->call_socks5_proxy_password = $this->getSystemInfo('call_socks5_proxy_password');

        $this->call_http_proxy_host = $this->getSystemInfo('call_http_proxy_host');
        $this->call_http_proxy_port = $this->getSystemInfo('call_http_proxy_port');
        $this->call_http_proxy_login = $this->getSystemInfo('call_http_proxy_login');
        $this->call_http_proxy_password = $this->getSystemInfo('call_http_proxy_password');
	}

	public abstract function getPlatformCode();

	private function loadSystemInfo() {
		$this->CI->load->model('external_system');
		$systemInfo = $this->CI->external_system->getSystemById($this->PLATFORM_CODE);

		# based on whether it's live_mode, provide corresponding extra_info field. Use live field by default.
		$extraInfoJson = (!isset($systemInfo->live_mode) || $systemInfo->live_mode) ? $systemInfo->extra_info : $systemInfo->sandbox_extra_info;
		$extraInfo = json_decode($extraInfoJson, true) ?: array();
		$this->SYSTEM_INFO = array_merge(((array) $systemInfo), $extraInfo);

		# Determine variable for sandbox/live
		$varNames = array('url', 'key', 'secret', 'account');
		foreach ($varNames as $aName) {
			$arrKey = ($this->getSystemInfo('live_mode') ? 'live' : 'sandbox') . '_' . $aName;
			$this->SYSTEM_INFO[$aName] = array_key_exists($arrKey, $this->SYSTEM_INFO) ? $this->SYSTEM_INFO[$arrKey] : "";
		}

		$this->_custom_curl_header = $this->getSystemInfo('curl_headers', []);
	}

	protected $_custom_curl_header = null;

	const REDIRECT_TYPE_GET_URL = 1;
	const REDIRECT_TYPE_POST_RESULT = 2;

	# Note: If you are getting the following properties: url, key, secret, account, the value in 'live_mode'
	# determines whether it will return live_X or sandbox_X value.
	protected function getSystemInfo($key, $def_val='') {
		return isset($this->SYSTEM_INFO[$key]) ? $this->SYSTEM_INFO[$key] : $def_val;
	}

	// -- functions for calling a number --
	/**
	 * Returns a URL, when clicked, the call to the given phone number will be initiated.
	 *
	 * @param $phoneNumber The phone number to call out.
	 * @param $callerId This is used to control which caller should be used; e.g. ext_no in SmartVoice
	 */
	public abstract function getCallUrl($phoneNumber, $callerId);

	public function submitPostForm($url, $params, $postJson=false, $mobile=NULL) {
		try {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

			if(!empty($this->_custom_curl_header)){
				curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_custom_curl_header);
			}

			if($postJson){
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->CI->utils->encodeJson($params) );
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			}else{
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params) );
			}

			$this->setCurlProxyOptions($ch);

            $response    = curl_exec($ch);
            $statusCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header      = substr($response, 0, $header_size);
            $content     = substr($response, $header_size);

            $errCode     = curl_errno($ch);
            $error       = curl_error($ch);
			$statusText = $errCode . ':' . $error;
			curl_close($ch);

			$this->CI->utils->debug_log('url', $url, 'params', $params , 'response', $response, 'errCode', $errCode, 'error', $error, 'statusCode', $statusCode);

			$decoded_result = $this->decodeResult($content);
			$flag = $this->isSuccess($decoded_result) ? Response_result::FLAG_NORMAL : Response_result::FLAG_ERROR;

			#save to response result
			$response_result_id = $this->saveTeleResponseResult($params, $content, $url, $response, $flag, $header, array('errCode' => $errCode, 'error' => $error, 'statusCode' => $statusCode), $mobile);

			return $decoded_result;
		} catch (Exception $e) {
			$this->CI->utils->error_log('POST failed', $e);
		}
	}

    public function submitGetForm($url, $params, $url_encode=true, $mobile=NULL) {
        try {
            $ch = curl_init();

            $query_string = '';
            if($url_encode == false) {
                foreach ($params as $key => $value) {
                    $query_string .= $key . '=' . $value . '&';
                }
                $query_string = rtrim($query_string,'&');
            }

            if(strpos($url, '?')!==FALSE){
                $url = empty($query_string) ? rtrim($url,'&').'&'.http_build_query($params) : rtrim($url,'&').'&'.$query_string;
            }else{
                $url = empty($query_string) ? $url.'?'.http_build_query($params) : $url.'?'.$query_string;
            }

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, false);
            if(!empty($this->_custom_curl_header)){
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_custom_curl_header);
            }

            $this->setCurlProxyOptions($ch);

            $response    = curl_exec($ch);
            $statusCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header      = substr($response, 0, $header_size);
            $content     = substr($response, $header_size);

            $errCode    = curl_errno($ch);
            $error      = curl_error($ch);
            $statusText = $errCode . ':' . $error;
            curl_close($ch);

            $this->CI->utils->debug_log('url', $url, 'params', $params , 'response', $response, 'errCode', $errCode, 'error', $error, 'statusCode', $statusCode);

            $decoded_result = $this->decodeResult($content);
            $flag = $this->isSuccess($decoded_result) ? Response_result::FLAG_NORMAL : Response_result::FLAG_ERROR;

            #save to response result
            $response_result_id = $this->saveTeleResponseResult($params, $content, $url, $response, $flag, $header, array('errCode' => $errCode, 'error' => $error, 'statusCode' => $statusCode), $mobile);

            return $decoded_result;
        } catch (Exception $e) {
            $this->CI->utils->error_log('GET failed', $e);
        }
    }

	protected function saveTeleResponseResult($params, $content, $url, $response, $flag, $header, $fields=NULL, $mobile=NULL) {
		$this->CI->load->model(array('response_result'));
		$player_id = NULL;
		$resultAll['type']     = 'telesales';
		$resultAll['url']      = $url;
		$resultAll['params']   = $params;
		$resultAll['content']  = $content;

		$statusCode = (array_key_exists("statusCode", $fields)) ? $fields['statusCode'] : NULL;
		$errCode    = (array_key_exists("errCode", $fields)) ? $fields['errCode'] : NULL;
		$error      = (array_key_exists("error", $fields)) ? $fields['error'] : NULL;
		$statusText = $errCode.":".$error;

		$method = 'telesales_response';

		return $this->CI->response_result->saveResponseResult($this->getPlatformCode(), $flag,
			$method, 'submit', json_encode($resultAll), $statusCode, $statusText, $header,
			array('player_id' => $player_id, 'related_id3' => $mobile));
	}

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

	public function decodeResult($result_string) {
		return $resultString;
	}

	public function isSuccess($decode_result) {
		if(!isset($decode_result['success'])) {
			return false;
		}
		else if($decode_result['success']) {
			return true;
		}
		else {
			return false;
		}
	}
}
