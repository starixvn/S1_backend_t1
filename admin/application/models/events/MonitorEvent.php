<?php

require_once dirname(__FILE__) . "/BaseEvent.php";

class MonitorEvent extends BaseEvent{

    protected $trigger_time;

    public function extractData($data){
        $this->trigger_time=$data['trigger_time'];
    }

    public function getTriggerTime(){
        return $this->trigger_time;
    }

}
