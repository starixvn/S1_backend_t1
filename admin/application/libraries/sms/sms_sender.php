<?php

/**
 * Wrapper class used to send SMS through API.
 *
 * SMS API configuration sample is defined in config_default_common.php. Each API should have
 * their own parameters defined in config_secret_local.php.
 *
 * Check the documentation of each SMS API implementation class to see what parameters are expected.
 */
class Sms_sender {
	private $lastError;
	private $CI;

	public function __construct() {
		$this->CI = &get_instance();
	}

	/**
	 * [send description]
	 * @param  [string] $recipientNumber
	 * @param  [string] $smsContent
	 * @param  [string] $useSmsApiName       [eg. sms_api_<name>]
	 * @return [boolean]
	 */
	public function send($recipientNumber, $smsContent, $useSmsApiName = null) {
		if(!$useSmsApiName) {
			$apiName = $this->getSmsApiName();
			if(empty($apiName)) {
				$this->CI->utils->error_log("No SMS API configured.");
				$this->lastError = lang("No SMS API configured.");
				return false;
			}
		} else {
			# Use the specified API to send sms
			$apiName = $useSmsApiName;
		}

		$this->CI->load->library('sms/'.$apiName);
		$smsApi = $this->CI->$apiName;

		$sendResult = $smsApi->send($recipientNumber, $smsContent);
		$this->CI->utils->debug_log("Sending sms to [$recipientNumber], content before signature is [$smsContent], result is [$sendResult]");
		$success = $smsApi->isSuccess($sendResult);

		if(!$success){
			$this->lastError = $smsApi->getErrorMsg($sendResult);
		}
		return $success;
	}

	public function getLastError() {
		return $this->lastError;
	}

	# Returns an array of configured SMS API's balances
	public function getSmsBalances() {
		$apiNames = $this->CI->config->item('sms_api');
		if(!is_array($apiNames)) {
			$apiNames = array($apiNames);
		}

		$apiBalances = array();
		foreach($apiNames as $apiName) {
			$this->CI->load->library('sms/'.$apiName);
			$smsApi = $this->CI->$apiName;
			$balanceString = $smsApi->getBalanceString();
			if(!empty($balanceString)) {
				$apiBalances[$apiName] = $balanceString;
			}
		}
		return $apiBalances;
	}

	public function getSmsApiName() {
        $settings = $this->CI->operatorglobalsettings->getSystemSetting('sms_api_list');
		$value = $this->CI->operatorglobalsettings->getSettingValueWithoutCache('sms_api_list');
		$this->CI->utils->debug_log("SMS api list value", $value);
		if(!is_null($value) && !empty($settings)){
			$this->CI->utils->debug_log("SMS api list", $settings);
			$apis = array_values($settings['params']['list']);
			$apiName = isset($settings['params']['list'][$value]) ? $settings['params']['list'][$value] : array_shift($apis);
			$this->CI->utils->debug_log("System Setting SMS API", $apiName);
		}
		else{
			# load the API instance based on configuration (defined in config_secret_local)
			$apiNames = $this->CI->config->item('sms_api');
			if(is_array($apiNames)) {
				$apiIndex = array_rand($apiNames); # pick a random API to send
				$apiName = $apiNames[$apiIndex];
				$this->CI->utils->debug_log("Random SMS API", $apiName);
			} else {
				$apiName = $apiNames;
				$this->CI->utils->debug_log("Fixed SMS API", $apiName);
			}
		}
		return $apiName;
	}
}
