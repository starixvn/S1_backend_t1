<?php

require_once (dirname(__FILE__) . '/abstract_email_template_main.php');

Class email_template_affiliate_activated extends abstract_email_template_main
{
    public function __construct($params)
    {
        parent::__construct($params);
    }

    public function replaceRule()
    {
        $parent_element = parent::replaceRule();
        $element = [
            '[aff_track_code]' => [
                'callback' => 'getAffTrackCode',
                'preview'  => 1
            ]
        ];

        return array_merge($parent_element, $element);
    }

    public function getAffTrackCode()
    {
        $aff = $this->user;
        return (isset($aff['trackingCode'])) ? $aff['trackingCode'] : '';
    }
}
