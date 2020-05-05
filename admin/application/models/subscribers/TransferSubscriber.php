<?php

require_once dirname(__FILE__) . "/../events/TransferEvent.php";
require_once dirname(__FILE__) . "/AbstractSubscriber.php";

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TransferSubscriber extends AbstractSubscriber implements EventSubscriberInterface{

    public function __construct(){
        parent::__construct();
        // $this->utils->info_log('load subscriber class', get_class());
    }

    public static function getSubscribedEvents(){
        return array(
            Queue_result::EVENT_TRANSFER_REQUEST_AFTER_DB_TRANS => 'afterTransferDBTrans',
        );
    }

    public function afterTransferDBTrans(TransferEvent $event){
        // ...

    }

}
