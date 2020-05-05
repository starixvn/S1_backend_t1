<?php

require_once dirname(__FILE__) . "/../events/DebugEvent.php";
require_once dirname(__FILE__) . "/AbstractSubscriber.php";

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DebugSubscriber extends AbstractSubscriber implements EventSubscriberInterface{

    public function __construct(){
        parent::__construct();
        $this->utils->info_log('load subscriber class', get_class());
    }

    public static function getSubscribedEvents(){
        return array(
            Queue_result::EVENT_DEBUG => 'doDebug',
        );
    }

    public function doDebug(DebugEvent $event){
        // ...
        $this->utils->debug_log('DebugEvent', $event);
        $result=[
            'now'=>$event->getNow(),
            'caller'=>$event->getCaller(),
            'doNow'=>$this->utils->getNowForMysql(),
        ];
        $this->appendResult($event, $result, false, false);
        //finish
        $event->setQueueResult(['done time'=>$this->utils->getNowForMysql()]);
    }

}
