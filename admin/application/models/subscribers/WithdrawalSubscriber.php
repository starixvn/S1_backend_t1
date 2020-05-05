<?php

require_once dirname(__FILE__) . "/../events/WithdrawalEvent.php";
require_once dirname(__FILE__) . "/AbstractSubscriber.php";

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WithdrawalSubscriber extends AbstractSubscriber implements EventSubscriberInterface{

    public function __construct(){
        parent::__construct();
    }

    public static function getSubscribedEvents(){
        return array(
            Queue_result::EVENT_WITHDRAWAL_AFTER_DB_TRANS => 'afterWithdrawalDBTrans',
        );
    }

    public function afterWithdrawalDBTrans(WithdrawalEvent $event){
        $walletAccountId=$event->getWalletAccountId();
        $transactionId=$event->getTransactionId();
        $playerId=$event->getPlayerId();
        $walletAccount=null;
        $transaction=null;
        $player=null;

        $this->load->model(['wallet_model', 'transactions', 'player_model']);
        if(!empty($walletAccountId)){
            $walletAccount=$this->wallet_model->getWalletAccountInfoById($walletAccountId);
        }
        if(!empty($transactionId)){
            $transaction=$this->transactions->getTransactionInfoById($transactionId);
        }
        if(!empty($playerId)){
            $player=$this->player_model->getPlayerArrayById($playerId);
        }

        $this->utils->debug_log('start process withdrawal event', $event);

        $this->utils->debug_log('end process withdrawal event', $event);

    }

}
