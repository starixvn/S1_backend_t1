<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * Dummy API, always returns success and write to logs. For testing purpose.
 *
 * To use this, go to admin's config_local.php and add:
 * $config['sms_api'] = 'sms_api_dummy';
 */
class Sms_api_dummy extends Abstract_sms_api {
	# Overwrite the main function to directly return success
	const SMS_FAIL_NUMBER = '00000000';

	public function send($mobile, $content){

		$this->CI->utils->debug_log("Sending SMS through the Dummy SMS API. Number, Content:", $mobile, $content);

		$this->CI->load->model(array('response_result'));
		$player_id = NULL;
		$resultAll['type']     = 'sms';
        $resultAll['url']      = $this->getUrl();
		$resultAll['params']   = $content;
		$resultAll['content']  = 'dummy-response';
		$flag = $this->isSuccess($mobile) ? Response_result::FLAG_NORMAL:Response_result::FLAG_ERROR;

		$response_result_id = $this->CI->response_result->saveResponseResult($this->getPlatformCode(), $flag,
			self::SMS_API, self::SMS_API, json_encode($resultAll), 200, 'dummy', '',
			array('player_id' => $player_id, 'related_id3' => $mobile));

		
		return $mobile ;
	}

	public function getUrl() {
		return "dummy-url";
	}

	public function getFields($mobile, $content) {
		return array();
	}

	public function getErrorMsg($returnQueryString) {
		return "Dummy SMS api error";
	}

	public function isSuccess($returnQueryString) {
			return $returnQueryString == (self::SMS_FAIL_NUMBER) ? false : true;
	}

	public function getBalanceString() {
		return 'Dummy API balance';
	}

}
