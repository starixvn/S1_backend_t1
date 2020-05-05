<?php

require_once (dirname(__FILE__) . '/abstract_email_template_main.php');

Class email_template_affiliate_registered_success extends abstract_email_template_main
{
    public function __construct($params)
    {
        parent::__construct($params);
    }

    public function replaceRule()
    {
        $parent_element = parent::replaceRule();
        return $parent_element;
    }
}
