<?php

require_once (dirname(__FILE__) . "/../abstract_email_template.php");

Abstract Class abstract_email_template_main extends abstract_email_template
{
    public function __construct($params)
    {
        parent::__construct($params);
    }

    protected function replaceRule()
    {
        return [
            '[aff_username]' => [
                'callback' => 'getAffUserName',
                'preview' => 1
            ]
        ];
    }

    protected function getAffUserName()
    {
        $aff = $this->user;
        return (isset($aff['username'])) ? $aff['username'] : '';
    }
}