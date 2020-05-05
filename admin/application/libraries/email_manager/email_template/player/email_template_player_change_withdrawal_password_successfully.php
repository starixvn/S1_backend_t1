<?php

require_once (dirname(__FILE__) . '/abstract_email_template_main.php');

Class email_template_player_change_withdrawal_password_successfully extends abstract_email_template_main
{
    public function __construct($params)
    {
        parent::__construct($params);
        $this->withdrawal_password = isset($params['new_withdrawal_password']) ? $params['new_withdrawal_password'] : 0;
    }

    public function replaceRule()
    {
        $parent_element = parent::replaceRule();
        $element = [
            '[withdrawal_password]' => [
                'callback' => 'getWithdrawalPassword',
                'preview'  => 0
            ]
        ];

        return array_merge($parent_element, $element);
    }

    public function getWithdrawalPassword()
    {
        return $this->withdrawal_password;
    }
}
