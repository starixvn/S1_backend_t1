<?php

require_once dirname(__FILE__) . "/../events/GenerateCommandEvent.php";
require_once dirname(__FILE__) . "/AbstractSubscriber.php";

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GenerateCommandSubscriber extends AbstractSubscriber implements EventSubscriberInterface{

    public function __construct(){
        parent::__construct();
        // $this->utils->info_log('load subscriber class', get_class());
    }

    public static function getSubscribedEvents(){
        return array(
            Queue_result::EVENT_GENERATE_COMMAND => 'generateCommand',
        );
    }

    public function generateCommand(GenerateCommandEvent $event) {
        $this->utils->debug_log('===================GenerateCommand Event start', $event);

        $command=$event->getCommand();
        $command_params=$event->getCommandParams();
        $is_blocked=$event->getIsBlocked();

        $cmd=$this->utils->generateCommandLine($command, $command_params, $is_blocked);
        raw_debug_log($cmd, pclose(popen($cmd, 'r')));

        $this->utils->debug_log('===================GenerateCommand Event end', $event);
    }
}
