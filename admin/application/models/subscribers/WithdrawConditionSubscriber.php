<?php

require_once dirname(__FILE__) . "/../events/WithdrawConditionEvent.php";
require_once dirname(__FILE__) . "/AbstractSubscriber.php";

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WithdrawConditionSubscriber extends AbstractSubscriber implements EventSubscriberInterface{

    public function __construct(){
        parent::__construct();
        // $this->utils->info_log('load subscriber class', get_class());
    }

    public static function getSubscribedEvents(){
        return array(
            Queue_result::EVENT_WITHDRAW_CONDITION_BEFORE_CHECK => 'beforeCheckWithdrawCondition',
            Queue_result::EVENT_WITHDRAW_CONDITION_AFTER_CHECK => 'afterCheckWithdrawCondition',
        );
    }

    public function beforeCheckWithdrawCondition(WithdrawConditionEvent $event){
        // ...

    }

    public function afterCheckWithdrawCondition(WithdrawConditionEvent $event){

    }

}
