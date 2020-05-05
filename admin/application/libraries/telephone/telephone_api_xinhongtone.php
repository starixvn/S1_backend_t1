<?php
require_once dirname(__FILE__) . '/abstract_telephone_api.php';

/**
 * Xinhongtone telephone API implementation
 *
 * XINHONGTONE_TELEPHONE_API, ID: 845
 *
 * Required Fields:
 *
 * * URL
 * * Extra Info
 *
 * Field Values:
 *
 * * Live URL: http://120.25.161.228:8080/external/server/CallBack
 * * Key: ## key ##
 * * Extra Info
 * > {
 * >    "xinhongtone_default_callerE164" : "",
 * >	"xinhongtone_accessE164" : ""
 * > }
 *
 *
 * @category Telephone
 * @copyright 2013-2016 TripleOneTech
 */
class Telephone_api_xinhongtone extends Abstract_telephone_api {
	const RETURN_RET_CODE_SUCCESS = '0';

	public function getPlatformCode() {
		return XINHONGTONE_TELEPHONE_API;
	}

	public function getCallUrl($phoneNumber, $callerId) {
		if(empty($callerId)) {
			$callerId = $this->getSystemInfo('xinhongtone_default_callerE164');
		}

		$params = array(
			'callerDisplayNumber' => '',
			'callerE164' => $callerId,
			'calleeE164s' => $phoneNumber,
			'accessE164' => $this->getSystemInfo('xinhongtone_accessE164'),
			'accessE164Password' =>	$this->getSystemInfo('key')
		);

		$this->utils->debug_log("==============xinhongtone getCallUrl params generated: ", $params);

		return $this->processTeleUrlForm($params, true);
	}

	private function processTeleUrlForm($params, $postJson=false) {
		$url = $this->getSystemInfo('url');
		$this->utils->debug_log("==============Xinhongtone processTeleUrlForm Call URL", $url);
		$result_content = $this->submitPostForm($url, $params, true, $params['calleeE164s']);

		return $result_content;
	}

	public function decodeResult($resultString) {
		$decode_content = json_decode($resultString, true);
		$result = array(
			'success' => false,
			'type' => self::REDIRECT_TYPE_POST_RESULT,
			'msg' => 'Failed for unknown reason.'
		);
		$this->utils->debug_log("============xinhongtone processTeleUrlForm decodeResult", $decode_content);
		if(!isset($decode_content['retCode'])) {
			$result['msg'] = "API response doesn't contain the key 'retCode'.";
		}
		else {
			if($decode_content['retCode'] == self::RETURN_RET_CODE_SUCCESS) {
				$result['success'] = true;
				$result['msg'] = 'API success retCode - ['. $decode_content['retCode'] .']: Wait for connecting...';
			}
			else if(isset($decode_content['retCode'])) {
				$result['msg'] = 'API failed retCode - ['. $decode_content['retCode'] .']: '.$decode_content['exception'];
			}
		}

		return $result;
	}
}
