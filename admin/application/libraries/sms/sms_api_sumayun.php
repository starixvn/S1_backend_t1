<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * 速码云 SMS API implementation
 * http://193.112.55.83:8868/sms.aspx
 *
 * Config items:
 * * sms_api_sumayun_userid
 * * sms_api_sumayun_account
 * * sms_api_sumayun_password
 */
class Sms_api_sumayun extends Abstract_sms_api {
	const SUCCESS_CODE = "Success";

	protected function signContent($content) {
		return sprintf("【%s】%s", $this->CI->config->item('sms_from'), $content);
	}

	public function getUrl() {
		return $this->getParam('url') ? $this->getParam('url') : "http://193.112.55.83:8868/sms.aspx";
	}

	public function getFields($mobile, $content) {
		$fields = array(
			'userid' => $this->getParam('userid'),
			'account' => $this->getParam('account'),
			'password' => $this->getParam('password'),
			'mobile' => $mobile,
			'content' => $content,
			'sendTime' => '',
			'action' => 'send',
			'extno' => ''
		);
		$this->utils->debug_log("===============sumayun SMS fields", $fields);
		return $fields;
	}

	public function getErrorMsg($returnQueryString) {
		$resp = $this->loadXmlResp($returnQueryString);
		return $resp['returnstatus'].": ".$resp['message'];
	}

    public function isSuccess($returnQueryString) {
        $resp = $this->loadXmlResp($returnQueryString);
        if($resp['returnstatus'] == self::SUCCESS_CODE)
        	return true;
        else
        	return false;
    }

    public function loadXmlResp($returnQueryString) {
    	$xml_object = simplexml_load_string($returnQueryString);
    	$xml_array = $this->object2array($xml_object);
        return $xml_array;
    }

    public function object2array($object) {
    	return @json_decode(@json_encode($object),1);
    }

	protected function getParam($name) {
		return $this->CI->config->item('sms_api_sumayun_'.$name);
	}

}
