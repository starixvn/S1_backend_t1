<?php

require_once (dirname(__FILE__) . '/abstract_email_template_main.php');

Class email_template_player_verify_email extends abstract_email_template_main
{
    public function __construct($params)
    {
        parent::__construct($params);
        $this->setExpireTime();
    }

    public function replaceRule()
    {
        $parent_element = parent::replaceRule();
        $element = [
            '[link]' => [
                'callback' => 'getVerifyLink',
                'preview'  => 1
            ],
            '[link_exptime]' => [
                'callback' => 'getExpireTime',
                'preview'  => 1
            ]
        ];

        if($this->CI->config->item('email_template_show_verify_token')){
            $verify_token = [
                '[verify_token]' => [
                    'callback' => 'getVerifyToken',
                    'preview'  => 1
                ]
            ];

            $element = array_merge($element, $verify_token);
        }

        return array_merge($parent_element, $element);
    }

    private function setExpireTime()
    {
        $player = $this->user;
        $this->expire_mins = $this->CI->config->item('verify_link_expire_mins') ? : 720;
        $this->expired_time = date('Y-m-d H:i:s', strtotime('+' . $this->expire_mins . ' mins'));

        $data = ["email_verify_exptime" => $this->expired_time];
        $this->CI->player_model->updatePlayer($player['playerId'], $data);
    }

    public function getVerifyLink()
    {
        $url = $this->CI->utils->getSystemUrl('player','/iframe_module/verify');
        $link = $url . '/' . $this->getVerifyToken();

        if($this->email_mode == 'text'){
            return $link;
        }
        else{
            return anchor($link, $link, 'target="_blank"');
        }
    }

    public function getExpireTime()
    {
        return $this->expire_mins;
    }

    public function getVerifyToken()
    {
        $player = $this->user;
        $verify_token = md5($player['playerId']) . $player['playerId'] . md5($this->expired_time);

        return $verify_token;
    }
}