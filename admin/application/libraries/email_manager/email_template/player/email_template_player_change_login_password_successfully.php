<?php

require_once (dirname(__FILE__) . '/abstract_email_template_main.php');

Class email_template_player_change_login_password_successfully extends abstract_email_template_main
{
    public function __construct($params)
    {
        parent::__construct($params);
        $this->login_password = isset($params['new_login_password']) ? $params['new_login_password'] : 0;
    }

    public function replaceRule()
    {
        $parent_element = parent::replaceRule();
        $element = [
            '[login_password]' => [
                'callback' => 'getLogInPassword',
                'preview'  => 0
            ]
        ];

        return array_merge($parent_element, $element);
    }

    public function getLogInPassword()
    {
        return $this->login_password;
    }
}
