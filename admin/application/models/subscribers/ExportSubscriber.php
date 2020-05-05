<?php

require_once dirname(__FILE__) . "/../events/ExportEvent.php";
require_once dirname(__FILE__) . "/AbstractSubscriber.php";

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExportSubscriber extends AbstractSubscriber implements EventSubscriberInterface{

    public function __construct(){
        parent::__construct();
        $this->utils->info_log('load subscriber class', get_class());
    }

    public static function getSubscribedEvents(){
        return array(
            Queue_result::EVENT_EXPORT_CSV => 'doExport',
        );
    }

    public function doExport(ExportEvent $event){
        $this->utils->debug_log('ExportEvent', $event);
        //call export csv
        //async call
        $this->runAsyncCommand($event, 'exporting_csv_from_queue');
    }

}
