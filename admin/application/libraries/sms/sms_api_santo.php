<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * Santo 闪通 SMS API implementation
 * http://www.santo.cc
 *
 * Config items:
 * * Sms_api_santo_cpid
 * * Sms_api_santo_cppwd
 */
class Sms_api_santo extends Abstract_sms_api {
	const SUCCESS_CODE = "000";
	# Reference: Documentation section 5.1
	const ERROR_MSG = array(
		"000" => "Success",
		"0101" => "无效的command参数",
		"0100" => "请求参数错误",
		"0104" => "账号信息错误",
		"0106" => "账号密码错误",
		"0110" => "目标号码格式错误或群发号码数量超过100个",
		"0600" => "未知错误",
	);

	protected function configCurl($handle, $mobile, $content) {
		$url = $this->getUrl();
		$content = $this->signContent($content);
		$fields = $this->getFields($mobile, $content);
		$fields_string =http_build_query($fields);

		$this->utils->debug_log("URL: ", $url.'?'.$fields_string);
		curl_setopt($handle, CURLOPT_URL, $url.'?'.$fields_string);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);

		return $fields;
	}

	# This API requires the mobile number to be prefixed with country code
	private function getMobile($mobile) {
		# Trim spaces first
		$mobile = trim($mobile);

		# Strip away preceding '+'. if there is a preceding '+', means the number already comes with country code
		if(strpos($mobile, '+') === 0) {
			$mobile = substr($mobile, 1);
			return $mobile;
		}

		# Prefix with default country code, if defined
		$defaultCountryCode = $this->CI->config->item('sms_default_country');
		if(!$defaultCountryCode) {
			$this->utils->debug_log("No default country code defined: \$config['sms_default_country']");
			return $mobile;
		}
		if(!strpos($mobile, $defaultCountryCode)) {
			$mobile = $defaultCountryCode.$mobile;
		}

		return $mobile;
	}

	public function getUrl() {
		return $this->getParam('url') ? $this->getParam('url') : "http://api2.santo.cc/submit";
	}

	# note the $content should be in UTF-8 format, will be converted to GBK here
	# Reference: Documentation section 4.1
	public function getFields($mobile, $content) {
		$content = iconv("UTF-8","gbk//TRANSLIT", $content);
		$fields = array(
			'command' => 'MT_REQUEST',
			'cpid' => $this->getParam('cpid'),
			'cppwd' => $this->getParam('cppwd'),
			'da' => $this->getMobile($mobile),
			'dc' => 15, # 15: GBK
			'sm' => urlencode($this->encodeHexStr($content)),
		);
		$this->utils->debug_log("Santo SMS fields", $fields);
		return $fields;
	}

	public function getErrorMsg($returnQueryString) {
		parse_str($returnQueryString, $params);
		if(!empty(self::ERROR_MSG[$params['mterrcode']])){
			return sprintf("%s", self::ERROR_MSG[$params['mterrcode']]);
		}else{
			return 'Unknown Error';
		}
	}

	public function isSuccess($returnQueryString) {
		parse_str($returnQueryString, $params);
		return $params['mterrcode'] == self::SUCCESS_CODE;
	}


	/**
	 * encode Hex String. Ref: encode_phpdemo.txt
	 *
	 * @param string $dataCoding
	 * @param string $realStr
	 * @return string hex string
	 */
	private function encodeHexStr($realStr) {
		return bin2hex($realStr);
	}

	// -- helper functions --
	# This method is repeated here because sometimes parent method is not able to use child class' name
	# We need to hardcode the classname
	protected function getParam($name) {
		return $this->CI->config->item('Sms_api_santo_'.$name);
	}
}
