<?php
require_once dirname(__FILE__) . '/abstract_sms_api.php';

/**
 * 美联 SMS API implementation
 * http://m.5c.com.cn/
 *
 * Config items:
 * * sms_api_5c_username
 * * sms_api_5c_password_md5
 * * sms_api_5c_apikey
 */
class Sms_api_5c extends Abstract_sms_api {
    const SUCCESS_CODE = "success";

    protected function signContent($content) {
        return urlencode($content);
    }

    public function getUrl() {
        return $this->getParam('url') ? $this->getParam('url') : 'http://m.5c.com.cn/api/send/index.php';
    }

    public function getFields($mobile, $content) {
        $fields = array(
            'username'     => $this->getParam('username'),
            'password_md5' => md5($this->getParam('password_md5')),
            'apikey'       => $this->getParam('apikey'),
            'mobile'       => $mobile,
            'content'      => $content,
            'encode'       => 'UTF-8'
        );
        $this->utils->debug_log("===============5c SMS fields", $fields);
        return $fields;
    }

    public function getErrorMsg($returnQueryString) {
        return $returnQueryString;
    }

    public function isSuccess($returnQueryString) {
        $this->utils->debug_log("===============5c SMS returnQueryString", $returnQueryString);
        if(strpos($returnQueryString, self::SUCCESS_CODE) === false)
            return false;
        else
            return true;
    }

    protected function getParam($name) {
        return $this->CI->config->item('sms_api_5c_'.$name);
    }

}
