<?php

require_once dirname(__FILE__) . "/BaseEvent.php";

class RegisterEvent extends BaseEvent{

    protected $player_id;

    public function extractData($data){
        $this->player_id=$data['player_id'];
    }

    public function getPlayerId(){
        return $this->player_id;
    }
}