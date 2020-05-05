<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * Sunmax SMS API implementation
 * http://www.512688.com
 *
 * Config items: Sms_api_sunmax_CorpID, Sms_api_sunmax_Pwd
 */
class Sms_api_sunmax extends Abstract_sms_api {
	const ERROR_MSG = array(
		"0" => "发送成功",
		"-1" => "帐号未注册",
		"-2" => "其他错误",
		"-3" => "密码错误",
		"-4" => "手机号格式不对",
		"-5" => "余额不足",
		"-6" => "定时发送时间不是有效的时间格式",
	);

	protected function signContent($content) {
		return sprintf("%s【%s】", $content, $this->CI->config->item('sms_from'));
	}

	public function getUrl() {
		return $this->getParam('url') ? $this->getParam('url') : "http://www.512688.com/ws/Send.aspx";
	}

	public function getFields($mobile, $content) {
		//use gb2312
		$content = iconv("UTF-8", "gb2312//IGNORE", $content);

		return array(
			'CorpID' => $this->getParam('CorpID'),
			'Pwd' => $this->getParam('Pwd'),
			'Mobile' => $mobile,
			'Content' => $content,
		);
	}

	public function getErrorMsg($errorCode) {
		return self::ERROR_MSG[$errorCode];
	}

	public function isSuccess($errorCode) {
		return strcmp($errorCode, '0') === 0;
	}

	protected function getParam($name) {
		return $this->CI->config->item('Sms_api_sunmax_'.$name);
	}
}
