<?php

require_once dirname(__FILE__) . "/BaseEvent.php";

class DebugEvent extends BaseEvent{

    protected $now;
    protected $caller;

    public function extractData($data){
        $this->caller=$data['caller'];
        $this->now=$data['now'];
    }

    public function getNow(){
        return $this->now;
    }

    public function getCaller(){
        return $this->caller;
    }

}
