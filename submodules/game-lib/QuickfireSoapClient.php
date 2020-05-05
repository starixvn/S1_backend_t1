<?php
class QuickfireSoapClient extends SoapClient {

	private $call_socks5_proxy = null;
	private $call_socks5_proxy_login = null;
	private $call_socks5_proxy_password = null;
	private $ignore_ssl_verify=false;
	private $save_response = true;
	public $header = null;
	public $resultText = null;
	private $statusCode = null;
	private $statusText = null;
	private $soap_timeout = null;
	private $error=null;
	private $errCode=null;

	private $basic_auth_username=null;
	private $basic_auth_password=null;

	private $CI = null;

    public $requestXml=null;

	public function __construct($url, $options = null) {

		$this->CI = &get_instance();

		if ($options) {
			if (array_key_exists('call_socks5_proxy', $options)) {
				$this->call_socks5_proxy = $options['call_socks5_proxy'];

			}
			if (array_key_exists('call_socks5_proxy_login', $options)) {
				$this->call_socks5_proxy_login = $options['call_socks5_proxy_login'];

			}
			if (array_key_exists('call_socks5_proxy_password', $options)) {
				$this->call_socks5_proxy_password = $options['call_socks5_proxy_password'];

			}
			if (array_key_exists('save_response', $options)) {
				$this->save_response = $options['save_response'];
			}
			if (array_key_exists('soap_timeout', $options)) {
				$this->soap_timeout = $options['soap_timeout'];
			}
			if (array_key_exists('ignore_ssl_verify', $options)) {
				$this->ignore_ssl_verify = $options['ignore_ssl_verify'];
			}

			if (array_key_exists('basic_auth_username', $options)) {
				$this->basic_auth_username = $options['basic_auth_username'];
			}

			if (array_key_exists('basic_auth_password', $options)) {
				$this->basic_auth_password = $options['basic_auth_password'];
			}

		}

		if (empty($this->soap_timeout)) {
			$this->soap_timeout = $this->CI->config->item('default_soap_timeout');
		}

		try {
			parent::__construct($url, $options);
		} catch (Exception $e) {
			$this->CI->utils->error_log($e);
		}
	}

	public function _fullResponse() {
        return array($this->header, $this->resultText, $this->statusCode, $this->statusText, $this->errCode, $this->error, $this->requestXml);
	}

	protected function getTimeoutSecond() {
		return $this->soap_timeout;
	}

	protected function getConnectTimeoutSecond() {
		return $this->CI->config->item('default_connect_timeout');
	}

	protected function callCurl($url, $data, $action) {
		$handle = curl_init();
		curl_setopt($handle, CURLOPT_URL, $url);

		$headers = array(
			"Content-Type: text/xml", 
			'SOAPAction: "' . $action . '"',
			'Request-Id: ' . $this->CI->utils->getGUID()
		);

		curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($handle, CURLOPT_HEADER, true);

		curl_setopt($handle, CURLOPT_TIMEOUT, $this->getTimeoutSecond());
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, $this->getConnectTimeoutSecond());

		if($this->ignore_ssl_verify){
			curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
		}

		$this->CI->utils->debug_log('url', $url, 'action', $action, 'call_socks5_proxy', $this->call_socks5_proxy, 'ignore_ssl_verify:'.$this->ignore_ssl_verify);

		if ($this->call_socks5_proxy) {
			curl_setopt($handle, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
			curl_setopt($handle, CURLOPT_PROXY, $this->call_socks5_proxy); // "103.224.83.131:8899"); // 1080 is your -D parameter
			if ($this->call_socks5_proxy_login && $this->call_socks5_proxy_password) {
				curl_setopt($handle, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
				curl_setopt($handle, CURLOPT_PROXYUSERPWD, $this->call_socks5_proxy_login . ':' . $this->call_socks5_proxy_password);
			}
		}

		if(!empty($this->basic_auth_username) && !empty($this->basic_auth_password)){

			curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
			curl_setopt($handle, CURLOPT_USERPWD, $this->basic_auth_username.':'.$this->basic_auth_password);

		}

		$response = curl_exec($handle);

		$statusCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		$errCode = curl_errno($handle);
		$error = curl_error($handle);

		$header_size = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $header_size);
		$content = substr($response, $header_size);

		curl_close($handle);

		// list($headers, $content) = explode("\r\n\r\n", $response, 2);

		// If you need headers for something, it's not too bad to
		// keep them in e.g. $this->headers and then use them as needed
		// $this->headers = $headers;
		// $this->content = $content;
		if ($this->save_response) {
			//write to
			$this->header = $headers;
			$this->resultText = $content;
			$this->statusCode = $statusCode;
			$this->statusText = $errCode . ':' . $error;
			$this->errCode = $errCode;
			$this->error = $error;
            $this->requestXml=$data;
		}

		return $content;
	}

	public function __doRequest($request, $location, $action, $version, $one_way = 0) {
		return $this->callCurl($location, $request, $action);
	}
}

///END OF FILE/////