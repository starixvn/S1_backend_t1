<?php

require_once dirname(__FILE__) . '/abstract_otp_api.php';

/**
 * OTP api google auth
 *
 */
class Otp_api_google_auth extends Abstract_otp_api{

    private $lib;
    private $secret;

    function __construct() {
        parent::__construct();
        $this->CI->load->library('third_party/lib_google_authenticator', null, 'lib_google_authenticator');
        $this->lib=$this->CI->lib_google_authenticator;
        $this->CI->utils->debug_log('load lib', $this->lib);
    }

    public function initCodeInfo(array $extra=null){
        $settings=$this->getSettings();
        $title=$settings['title'];
        $this->secret=$this->lib->createSecret();
        $text=$this->secret;
        $url=$this->getOTPFormat($this->getObjectName(), $this->secret, $title);
        return $this->returnCodeInfo($url, $text, $this->secret);
    }

    public function validateCode($secret, $code, array $extra=null){
        $this->CI->utils->debug_log('validate code', $secret, $code);
        $settings=$this->getSettings();
        $discrepancy=$settings['discrepancy'];
        $diff=null;
        $success=$this->lib->verifyCode($secret, $code, $discrepancy, null, $diff);
        $this->CI->utils->debug_log('otp_api_google_auth verify code '.$this->getObjectName(), $success, $secret, $code, $discrepancy, 'found code at', $diff);

        return ['success'=>$success, 'message'=>$success ? lang('Code is verified') : lang('Wrong 2FA Code')];
    }

}
