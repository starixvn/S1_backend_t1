<?php

require_once dirname(__FILE__) . "/../events/BaseEvent.php";
require_once dirname(__FILE__) . "/AbstractSubscriber.php";

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AutoFinishSubscriber extends AbstractSubscriber implements EventSubscriberInterface{

    public function __construct(){
        parent::__construct();
        $this->utils->info_log('load subscriber class', get_class());
    }

    public static function getSubscribedEvents(){
        $events=[];
        foreach (Queue_result::AUTO_FINISH_EVENTS as $evt) {
            $events[$evt]='doFinish';
        }

        return $events;
    }

    public function doFinish(BaseEvent $event){
        $this->utils->debug_log('doFinish', $event);
        $this->doneResult($event, $event->getQueueResult(), $event->isError());
    }

}
