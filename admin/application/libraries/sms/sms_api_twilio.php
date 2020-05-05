<?php

require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * url : https://www.twilio.com/
 *
 * need default params : account_sid, auth_token, from_phone_num
 *
 */
class Sms_api_twilio extends Abstract_sms_api {

    private $accountSid;
    private $authToken;
    private $fromPhoneNum;

    private $restUrl = 'https://api.twilio.com';

    public function __construct() {
        parent::__construct();

        $twilioInfo = $this->CI->config->item('Sms_api_twilio');
        $this->accountSid = isset($twilioInfo['account_sid']) ? $twilioInfo['account_sid'] : "" ;
        $this->authToken  = isset($twilioInfo['auth_token'])  ? $twilioInfo['auth_token'] : "" ;
        $this->fromPhoneNum = isset($twilioInfo['from_phone_num']) ? $twilioInfo['from_phone_num'] : "" ;

        if (!$this->validateDefaultParam()) {
            throw new Exception("Default params must be set in config file");
        }
    }

    protected function configCurl($handle, $mobile, $content) {
        $nationalMobile = $this->addNationalNum($mobile);
        $options = [
            CURLOPT_URL => $this->getUrl('sendSms'),
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $this->getFields($nationalMobile, $content),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERPWD  => $this->accountSid . ":" . $this->authToken
        ];
        curl_setopt_array($handle, $options);

        $fields = $this->getFields($nationalMobile, $content);
        return $fields;
	}

    # @return rest url
    public function getUrl() {
        $_url = $this->getParam('url') ? $this->getParam('url') : $this->restUrl;
        $_url .= "/2010-04-01/Accounts/" . $this->accountSid . "/Messages";
        return $_url;
    }

    public function getFields($mobile, $content) {
		$fields = [
			'To' => $mobile,
			'Body' => $content,
            'From' => $this->fromPhoneNum,
        ];

		$this->utils->debug_log("twilio SMS fields", $fields);
		return $fields;
	}

    private function validateDefaultParam() {
        $defaultParams = ['accountSid', 'authToken', 'fromPhoneNum'];
        foreach ($defaultParams as $param) {
            if (empty($this->$param)) return false;
        }
        return true;
    }

    public function isSuccess($resp) {
        $resp = $this->loadXmlResp($resp);
        return (empty($resp->Message->ErrorCode) && empty($resp->RestException));
    }

    public function getErrorMsg($resp) {
        $resp = $this->loadXmlResp($resp);
        if ($resp->RestException) {
            return $resp->RestException->Code . " : " . $resp->RestException->Message;
        } else {
            return $resp->Message->ErrorMessage;
        }
    }

    public function loadXmlResp($resp) {
        return simplexml_load_string($resp);
    }

    private function addNationalNum($mobile) {
        if (preg_match("/^09[0-9]{2}[0-9]{6}$/", $mobile)) {
            return "+886" . substr($mobile,1, 9);
        } else if (preg_match("/^1[34578]\d{9}$/", $mobile)) {
            return "+86" . $mobile;
        }

        return $mobile;
    }

    protected function getParam($name) {
        return $this->CI->config->item('Sms_api_twilio_'.$name);
    }
}
