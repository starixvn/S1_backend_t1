<?php

class Http_utils {

	private $url 	 		   = NULL;
	private $headers 		   = array();
	private $cookie  		   = NULL;
	private $method 		   = NULL;
	private $postfields_method = 'http_build_query';
	public $response 	   	   = array();

	public function __construct($options = null) {
		if (isset($options['cookie'])) {
			$this->cookie = $options['cookie'];
		}
	}

	public function set_url($url) {
		$this->url = $url;
		return $this;
	}

	public function set_method($method) {
		$this->method = $method;
		return $this;
	}

	public function set_headers($headers) {
		$this->headers = $headers;
		return $this;
	}

	public function add_header($header, $value = NULL) {
		if ($value) {
			$header = "{$header}: {$value}";
		}
		$this->headers[] = $header;
		return $this;
	}

	public function set_cookie($cookie) {
		$this->cookie = $cookie;
		return $this;
	}

	public function set_postfields_method($postfields_method) {
		$this->postfields_method = $postfields_method;
		return $this;
	}

	public function get_response() {
		return $this->response['content'];
	}

	public function curl($url = null, $params = null, $cookie = false) {
		
		if ($url) {
			$this->set_url($url);
		}

		$ch = curl_init($this->url);

		try {
			
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

			if ($this->method) {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
			}

			if (isset($params)) {
				
				if (empty($this->method)) {
					curl_setopt($ch, CURLOPT_POST, TRUE);
				}

				if (is_array($params)) {
					$postfields_method = $this->postfields_method;
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields_method($params));
				}

			}
			
			if ($this->headers) {
				curl_setopt($ch, CURLOPT_HTTPHEADER, array_unique($this->headers)); 
			}
			
			if ($this->cookie) {
				
				curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);

				if ($cookie) {
					curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
				}
				
			}

			$this->response['content'] = curl_exec($ch);

			$info = curl_getinfo($ch);

			$this->response['info'] = $info;
			$this->response['code'] = $info['http_code'];
			$this->response['url']  = $info['url'];

		} catch (\Exception $e) {
			throw new \Exception("Error Processing Request", 1);
		} finally {
			curl_close($ch);
		}

		return $this->response['content'];

	}

	public function queryXpath($query) {
		libxml_use_internal_errors(true);
		$dom = new \DOMDocument();
		$dom->loadHTML($this->response['content']);
		libxml_clear_errors();
		$xpath = new \DOMXPath($dom);
		$result = $xpath->query($query);
		return ($result->length == 1) ? $result->item(0) : $result;
	}

}