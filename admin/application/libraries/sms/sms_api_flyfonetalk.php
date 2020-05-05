<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * Flyfonetalk
 * https://cms.flyfonetalk.com/httpapi/SendSMSHTTP.php
 *
 * Config items:
 * * sms_api_flyfonetalk_account
 * * sms_api_flyfonetalk_passcode
 * * sms_api_flyfonetalk_country_code
 */
class Sms_api_flyfonetalk extends Abstract_sms_api {
    const SUCCESS_CODE = 0;

    protected function signContent($content) {
        return sprintf("【%s】%s", $this->CI->config->item('sms_from'), $content);
    }

    public function getUrl() {
        return $this->getParam('url') ? $this->getParam('url') : "https://cms.flyfonetalk.com/httpapi/SendSMSHTTP.php";
    }

    public function getFields($mobile, $content) {

        $fields = array(
            'account' => $this->getParam('account'),
            'passcode' => $this->getParam('passcode'),
            'phone' => $this->getParam('country_code').$mobile,
            'sms' => $this->getParam('skip_sign') ? $content : $this->signContent($content),
        );

        $this->utils->debug_log("===============flyfonetalk SMS fields", $fields);
        return $fields;
    }

	protected function configCurl($handle, $mobile, $content) {

		$fields = $this->getFields($mobile, $content);
        $url = $this->getUrl().'?'.http_build_query($fields);
        $this->utils->debug_log("===============flyfonetalk SMS url", $url);

		curl_setopt($handle,CURLOPT_URL,$url);
        curl_setopt($handle,CURLOPT_RETURNTRANSFER,1);
        $this->setCurlProxyOptions($handle);

		return $fields;
    }

    public function getErrorMsg($returnQueryString) {
        $result = json_decode($returnQueryString, true);
        if (!is_array($result)) {
            return $returnQueryString;
        }
        $errorCode = $result['ResultCode'];
        $errorDesc = $result['ResultDesc'];

        $this->utils->error_log("===============flyfonetalk return [$errorCode]: $errorDesc", $returnQueryString);

        return $errorCode.": ".$errorDesc;
    }

    public function isSuccess($returnQueryString) {
        $result = json_decode($returnQueryString, true);
        if( isset($result['ResultCode']) && $result['ResultCode'] == self::SUCCESS_CODE)
            return true;
        else
            return false;
    }


    protected function getParam($name) {
        return $this->CI->config->item('sms_api_flyfonetalk_'.$name);
    }
}
