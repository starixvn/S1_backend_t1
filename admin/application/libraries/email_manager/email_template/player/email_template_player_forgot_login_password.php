<?php

require_once (dirname(__FILE__) . '/abstract_email_template_main.php');

Class email_template_player_forgot_login_password extends abstract_email_template_main
{
    public function __construct($params)
    {
        parent::__construct($params);
        $this->verify_code = isset($params['verify_code']) ? $params['verify_code'] : 0;
    }

    public function replaceRule()
    {
        $parent_element = parent::replaceRule();
        $element = [
            '[verify_code]' => [
                'callback' => 'getVerifyCode',
                'preview'  => 0
            ],
            '[verify_code_exptime]' => [
                'callback' => 'getExpireTime',
                'preview'  => 1
            ]
        ];

        return array_merge($parent_element, $element);
    }

    public function getVerifyCode()
    {
        return $this->verify_code;
    }

    public function getExpireTime()
    {
        $expire_mins = $this->CI->config->item('password_reset_code_expire_mins') ? : 30;
        return $expire_mins;
    }
}
