<?php

require_once dirname(__FILE__) . "/BaseEvent.php";

class GenerateCommandEvent extends BaseEvent{

    protected $command;
    protected $command_params;
    protected $is_blocked;

    public function extractData($data){
        $this->command=$data['command'];
        $this->command_params=$data['command_params'];
        $this->is_blocked=$data['is_blocked'];
    }

    public function getCommand(){
        return $this->command;
    }

    public function getCommandParams(){
        return $this->command_params;
    }

    public function getIsBlocked(){
        return $this->is_blocked;
    }
}
