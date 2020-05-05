<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * Luosimao 螺丝帽 SMS API implementation
 * http://www.luosimao.com
 *
 * Config items:
 * * Sms_api_luosimao_apikey
 */
class Sms_api_luosimao extends Abstract_sms_api {
	const SUCCESS_CODE = "0";

	public function getUrl() {
		return $this->getParam('url') ? $this->getParam('url') : 'http://sms-api.luosimao.com/v1/send.json';
	}

	private function getBalanceUrl() {
		return $this->getParam('balance_url') ? $this->getParam('balance_url') : 'http://sms-api.luosimao.com/v1/status.json';
	}

	protected function signContent($content) {
		return sprintf("%s【%s】", $content, $this->CI->config->item('sms_from'));
	}

	protected function configCurl($handle, $mobile, $content) {
		$url = $this->getUrl();
		$apikey = $this->getParam('apikey');
		$content = $this->signContent($content);
		$fields = $this->getFields($mobile, $content);

		curl_setopt($handle, CURLOPT_URL, $url);

		curl_setopt($handle, CURLOPT_HTTP_VERSION  , CURL_HTTP_VERSION_1_0 );
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 8);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($handle, CURLOPT_HEADER, FALSE);

		curl_setopt($handle, CURLOPT_HTTPAUTH , CURLAUTH_BASIC);
		curl_setopt($handle, CURLOPT_USERPWD  , 'api:key-'.$apikey);

		curl_setopt($handle, CURLOPT_POST, TRUE);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $fields);

		return $fields;
	}

	# note the $content should be in UTF-8 format
	# Reference: https://luosimao.com/docs/api
	public function getFields($mobile, $content) {
		$fields = array(
			'mobile' => $mobile,
			'message' => $content,
		);
		$this->utils->debug_log("luosimao SMS fields", $fields);
		return $fields;
	}

	public function getErrorMsg($returnQueryString) {
		$params = json_decode($returnQueryString, true);
		return self::ERROR_MSG[$params['error']];
	}

	public function isSuccess($returnQueryString) {
		$params = json_decode($returnQueryString, true);
		return $params['error'] == self::SUCCESS_CODE;
	}

	protected function getParam($name) {
		return $this->CI->config->item('Sms_api_luosimao_'.$name);
	}

	public function getBalanceString() {
		$ch = curl_init();

		# config of CURL
		$url = $this->getBalanceUrl();
		$apikey = $this->getParam('apikey');

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTP_VERSION  , CURL_HTTP_VERSION_1_0 );
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);

		curl_setopt($ch, CURLOPT_HTTPAUTH , CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD  , 'api:key-'.$apikey);

		curl_setopt($ch, CURLOPT_POST, FALSE);

		$result = curl_exec($ch);
		if($result === false) {
			$this->CI->utils->error_log("Error getting balance ...", curl_error($ch));
		}
		curl_close($ch);

		$result = json_decode($result, true);
		return array_key_exists('deposit', $result) ? $result['deposit'] : 0;
	}

	# Reference: https://luosimao.com/docs/api
	const ERROR_MSG = array(
		'0' => 'Success',
		'-10' => '验证信息失败',
		'-11' => '用户接口被禁用',
		'-20' => '短信余额不足',
		'-30' => '短信内容为空',
		'-31' => '短信内容存在敏感词',
		'-32' => '短信内容缺少签名信息',
		'-33' => '短信过长，超过300字（含签名）',
		'-34' => '签名不可用',
		'-40' => '错误的手机号',
		'-41' => '号码在黑名单中',
		'-42' => '验证码类短信发送频率过快',
		'-50' => '请求发送IP不在白名单内',
	);
}
