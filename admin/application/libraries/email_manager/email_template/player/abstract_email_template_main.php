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
            '[player_name]' => [
                'callback' => 'getPlayerName',
                'preview' => 1
            ],
            '[player_email]' => [
                'callback' => 'getPlayerEmail',
                'preview' => 1
            ],
            '[player_username]' => [
                'callback' => 'getPlayerUsername',
                'preview' => 1
            ]
        ];
    }

    protected function getPlayerName()
    {
        $player = $this->user;
        return (isset($player['firstName']) && isset($player['lastName'])) ? $player['firstName'] . ' ' . $player['lastName'] : '';
    }

    protected function getPlayerEmail()
    {
        $player = $this->user;
        return (isset($player['email'])) ? $player['email'] : '';
    }

    protected function getPlayerUsername()
    {
        $player = $this->user;
        return (isset($player['username'])) ? $player['username'] : '';
    }
}