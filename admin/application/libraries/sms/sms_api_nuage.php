<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * NUAGE SMS API implementation
 * http://sms.nuage.asia/
 *
 * Config items:
 * * sms_api_nuage_name
 * * sms_api_nuage_oasender
 * * sms_api_nuage_pwd
 * * sms_api_nuage_apikey
 * * sms_api_nuage_country_code
 */
class Sms_api_nuage extends Abstract_sms_api {
    const SUCCESS_CODE = true;

	protected function signContent($content) {
		return sprintf("【%s】%s", $this->CI->config->item('sms_from'), $content);
	}

    public function getUrl() {
        return $this->getParam('url') ? $this->getParam('url') : 'http://sms.nuage.asia/TOL/adsmsbatchTOL.php';
    }

    public function getFields($mobile, $content) {
        $fields = array(
            'msg'       => $content,
            'batchlist' => $this->getParam('country_code').$this->getMobile($mobile),
            'name'      => $this->getParam('name'),
            'pwd'       => $this->getParam('pwd'),
            'checksum'  => md5($this->getParam('apikey').$content)
        );
        if($this->getParam('oasender')){
            $fields['oasender'] = $this->getParam('oasender');
        }
        $this->utils->debug_log("===============nuage SMS fields", $fields);
        return $fields;
    }

    public function getErrorMsg($returnQueryString) {
		$params = json_decode($returnQueryString, true);
        return $params['Message'];
    }

	public function isSuccess($returnQueryString) {
        $params = json_decode($returnQueryString, true);
		return $params['IsSuccess'];
	}

    protected function getParam($name) {
        return $this->CI->config->item('sms_api_nuage_'.$name);
    }

}
