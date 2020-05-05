<?php

/**
 *
 * Defines general behavior of external login API classes.
 *
 * General behaviors include:
 * * Generate Content
 * * Send Content
 *
 *
 * @category API
 * @version 3.42.18
 * @copyright 2013-2018 TripleOneTech
 *
 */
abstract class Abstract_external_login_api {

	function __construct() {
		$this->CI = &get_instance();
		$this->utils = $this->CI->utils;
	}

	/**
	 * check username password
	 * @param  string $username
	 * @param  string $password decrypted password
	 * @param  string &$message [description]
	 * @return boolean
	 */
	public abstract function validateUsernamePassword($playerId, $username, $password, &$message='');

	public function getPlatformCode(){
		return EXTERNAL_LOGIN_API; #API number for SMS apis
	}

	public function getSettings(){

		return $this->CI->utils->getConfig('external_login_settings');
	}

	public function callHttpApi($playerId, $url, $method, $params, $curlOptions = null, $headers = null, $jsonMode=false){

		//return array($header, $content, $statusCode, $statusText, $errCode, $error, null);
		$show_debug_on_http_call=false;
		$options=$this->getSettings();

		// $this->CI->utils->debug_log('options', $options);

		if(!empty($options)){

			$show_debug_on_http_call= isset($options['show_debug_on_http_call']) ? $options['show_debug_on_http_call'] : false ;
			//try proxy
			if(empty($curlOptions)){
				$curlOptions=[];
			}

            $settle_proxy=false;
            // set proxy
            if (isset($options['call_socks5_proxy']) && !empty($options['call_socks5_proxy'])) {
                $this->CI->utils->debug_log('http call with proxy', $options['call_socks5_proxy']);
                $curlOptions[CURLOPT_PROXYTYPE]=CURLPROXY_SOCKS5_HOSTNAME;
                $curlOptions[CURLOPT_PROXY]=$options['call_socks5_proxy'];
                if (!empty($options['call_socks5_proxy_login']) && !empty($options['call_socks5_proxy_password'])) {
                    $curlOptions[CURLOPT_PROXYAUTH]=CURLAUTH_BASIC;
                    $curlOptions[CURLOPT_PROXYUSERPWD]=$options['call_socks5_proxy_login'] . ':' . $options['call_socks5_proxy_password'];
                }
                $settle_proxy=true;
            }

            if(!$settle_proxy){
                //http proxy
                if (isset($options['call_http_proxy_host']) && !empty($options['call_http_proxy_host'])) {
                    $this->CI->utils->debug_log('http call with http proxy', $options['call_http_proxy_host']);
                    $curlOptions[CURLOPT_PROXYTYPE]=CURLPROXY_HTTP;
                    $curlOptions[CURLOPT_PROXY]=$options['call_http_proxy_host'];
                    $curlOptions[CURLOPT_PROXYPORT]=$options['call_http_proxy_port'];
                    if (!empty($options['call_http_proxy_login']) && !empty($options['call_http_proxy_password'])) {
                        $curlOptions[CURLOPT_PROXYAUTH]=CURLAUTH_BASIC;
                        $curlOptions[CURLOPT_PROXYUSERPWD]=$options['call_http_proxy_login'] . ':' . $options['call_http_proxy_password'];
                    }
                }
            }

            if(isset($options['ignore_ssl_verify']) && $options['ignore_ssl_verify']){
                $curlOptions[CURLOPT_SSL_VERIFYPEER]=0;
                $curlOptions[CURLOPT_SSL_VERIFYHOST]=0;
            }

		}

		if($jsonMode){
			$params=json_encode($params);
		}

		$this->CI->utils->debug_log('show_debug_on_http_call', $show_debug_on_http_call);

		if($show_debug_on_http_call){
			$this->CI->utils->debug_log('start call', $url, $method, $params, $curlOptions, $headers, $jsonMode);
		}

		list($header, $content, $statusCode, $statusText, $errCode, $error, $obj)=
			$this->CI->utils->callHttp($url, $method, $params, $curlOptions, $headers);

		if($show_debug_on_http_call){
			$this->CI->utils->debug_log('end call', $header, $content, $statusCode, $statusText, $errCode, $error, $obj);
		}

		$respId=$this->saveToResponseResult($playerId, $url, $method, $params, $curlOptions, $headers, $header, $content, $statusCode, $statusText, $errCode, $error);

		return [$header, $content, $statusCode, $statusText, $errCode, $error, $respId];
	}

	public function saveToResponseResult($playerId, $url, $method, $params, $curlOptions, $headers,
		$header, $content, $statusCode, $statusText, $errCode, $error){
		$this->CI->load->model(['response_result']);

		$flag=($statusCode>=400 || $errCode!=0) ? Response_result::FLAG_ERROR : Response_result::FLAG_NORMAL;
		$fields=['player_id'=>$playerId];

		return $this->CI->response_result->saveResponseResult($this->getPlatformCode(), $flag, $method, $params,
			$content, $statusCode, $statusText, $header, $fields);
	}

}
