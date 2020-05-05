<?php

require_once dirname(__FILE__) . "/../events/MonitorEvent.php";
require_once dirname(__FILE__) . "/AbstractSubscriber.php";

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MonitorSubscriber extends AbstractSubscriber implements EventSubscriberInterface{

    public function __construct(){
        parent::__construct();
        $this->utils->info_log('load subscriber class', get_class());
    }

    public static function getSubscribedEvents(){
        return array(
            Queue_result::EVENT_MONITOR_HEART_BEAT => 'monitorHeartBeat',
        );
    }

    public function monitorHeartBeat(MonitorEvent $event){
        $this->utils->debug_log('MonitorEvent', $event);
        $result=[
            'trigger_time'=>$event->getTriggerTime(),
            'header_beat_time'=>$this->utils->getNowForMysql(),
        ];
        // $this->appendResult($event, $result, false, false);
        //finish
        $extra=[
            'trigger_time'=>$event->getTriggerTime(),
            'header_beat_time'=>$this->utils->getNowForMysql(),
        ];
        $this->doneResult($event, $result, false, null, $extra);
    }

}
