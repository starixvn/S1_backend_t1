<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * Nexmo SMS API implementation
 * https://developer.nexmo.com/api/sms
 *
 * Config items:
 * * Sms_api_nexmo_apikey
 */
class Sms_api_nexmo extends Abstract_sms_api {
	const SUCCESS_CODE = "0";

	public function getUrl() {
		return $this->getParam('url') ? $this->getParam('url') : "https://rest.nexmo.com/sms/json";
	}

	protected function signContent($content) {
		return sprintf("【%s】%s", $this->CI->config->item('sms_from'), $content);
	}

	protected function configCurl($handle, $mobile, $content) {
		$url = $this->getUrl();
		$fields = $this->getFields($mobile, $content);
		$fields_string = http_build_query($fields);

		curl_setopt($handle, CURLOPT_URL,$url);
		curl_setopt($handle, CURLOPT_POSTFIELDS,$fields_string);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER,1);
		if ($this->CI->config->item('sms_used_GSM7')==true) {
			curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Transfer-Encoding:7bit'));
		}else{
			curl_setopt($handle, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
		}
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_TIMEOUT, 10);
		curl_setopt($handle, CURLOPT_POST, 1);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

		return $fields;
	}

	# note the $content should be in UTF-8 format
	# Reference: https://nexmo.com/docs/api
	public function getFields($mobile, $content) {
		$fields = array(
			'from' => $this->getParam('api_from'),
			'to' => $this->getParam('api_country_code').$mobile,
			'text' => $content,
			'api_key' => $this->getParam('api_key'),
			'api_secret' => $this->getParam('api_secret'),
			'type' => 'unicode'
		);
		if($this->CI->config->item('Sms_content_use_sms_from')==true){
			$text_content = $this->signContent($fields['text']);
			$fields['text'] = $text_content;
		}

		$phoneNumber = $fields['to'];
		if($this->CI->config->item('sms_entaplayid_begin_number_is_there_0')==true){
			if (substr($phoneNumber,2,1)=='0'){
				$phone = substr($phoneNumber,3);
				$fields['to'] = '62'.$phone;
			}
		}else if($this->CI->config->item('sms_entaplayth_begin_number_is_there_0')==true){
			if (substr($phoneNumber,2,1)=='0'){
				$phone = substr($phoneNumber,3);
				$fields['to'] = '66'.$phone;
			}
		}else{
			$this->utils->debug_log("nexmo SMS phoneNumber", $phoneNumber);
		}
		$this->utils->debug_log("nexmo SMS fields", $fields);
		return $fields;
	}

	public function getErrorMsg($returnQueryString) {
		$params = json_decode($returnQueryString, true);
		$this->utils->debug_log("===============Sms_api_nexmo getErrorMsg ", $params);
		return self::ERROR_MSG[$params['messages'][0]['status']];
	}

	public function isSuccess($returnQueryString) {
		$params = json_decode($returnQueryString, true);
		return $params['messages'][0]['status'] == self::SUCCESS_CODE;
	}

	protected function getParam($name) {
		return $this->CI->config->item('sms_api_nexmo_'.$name);
	}

	# Reference: https://developer.nexmo.com/api/sms
	const ERROR_MSG = array(
		'0' => '【Success】',
		'1' => '【Throttled】: You have exceeded the submission capacity allowed on this account. Please wait and retry.',
		'2' => '【Missing params】: Your request is incomplete and missing some mandatory parameters.',
		'3' => '【Invalid params】: The value of one or more parameters is invalid.',
		'4' => '【Invalid credentials】: The api_key / api_secret you supplied is either invalid or disabled.',
		'5' => '【Internal error】: There was an error processing your request in the Platform.',
		'6' => '【Invalid message】: The Platform was unable to process your request. For example, due to an unrecognised prefix for the phone number.',
		'7' => '【Number barred】: The number you are trying to submit to is blacklisted and may not receive messages.',
		'8' => '【Partner account barred】: The api_key you supplied is for an account that has been barred from submitting messages.',
		'9' => '【Partner quota exceeded】: Your pre-paid account does not have sufficient credit to process this message.',
		'11' => '【Account not enabled for REST】: This account is not provisioned for REST submission, you should use SMPP instead.',
		'12' => '【Message too long】: The length of udh and body was greater than 140 octets for a binary type SMS request.',
		'13' => '【Communication Failed】: Message was not submitted because there was a communication failure.',
		'14' => '【Invalid Signature】: Message was not submitted due to a verification failure in the submitted signature.',
		'15' => '【Illegal Sender Address - rejected】: Due to local regulations, the SenderID you set in from in the request was not accepted. Please check the Global messaging section.',
		'16' => '【Invalid TTL】: The value of ttl in your request was invalid.',
		'19' => '【Facility not allowed】: Your request makes use of a facility that is not enabled on your account.',
		'20' => '【Invalid Message class】: The value of message-class in your request was out of range. See https://en.wikipedia.org/wiki/Data_Coding_Scheme.',
		'23' => '【Bad callback :: Missing Protocol】: You did not include https in the URL you set in callback.',
		'29' => '【Non White-listed Destination】: The phone number you set in to is not in your pre-approved destination list. To send messages to this phone number, add it using Dashboard.',
		'34' => '【Invalid or Missing Msisdn Param】: The phone number you supplied in the to parameter of your request was either missing or invalid.'
	);
}
